<?php

namespace App\Http\Controllers;
use App\Models\OrderStatusLog;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Models\WaiterCall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\StockService;

class WaiterController extends Controller
{
    protected $stockService;
    
    // Cache süreleri (dakika)
    protected const CACHE_SHORT = 2;    // 2 dakika - sık değişen veriler
    protected const CACHE_MEDIUM = 10;  // 10 dakika - orta sıklıkta değişen veriler
    
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    /**
     * Cache'li sorgu çalıştırma
     */
    protected function cacheQuery(string $key, callable $callback, int $minutes = self::CACHE_SHORT)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }
    // Garson ana sayfa: masa listesi
    public function index(Request $request)
    {
        // Filtreleme parametreleri
        $query = request('q');
        $statusFilter = request('status');
        
        // Cache anahtarı oluştur (filtreler dahil)
        $cacheKey = 'waiter_dashboard_' . md5($query . '_' . $statusFilter);
        
        $data = $this->cacheQuery($cacheKey, function() use ($query, $statusFilter) {
            // Optimize edilmiş masa sorgusu
            $tablesQuery = Table::query()
                ->select(['id', 'name', 'status', 'capacity'])
                ->with(['active_order' => function($q) {
                    $q->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
                      ->whereNotIn('status', ['paid', 'canceled']);
                }]);
                
            // Arama filtresi
            if ($query) {
                $tablesQuery->where('name', 'like', '%' . $query . '%');
            }
            
            // Durum filtresi (aktif siparişe göre)
            if ($statusFilter) {
                $tablesQuery->whereHas('active_order', function($q) use ($statusFilter) {
                    $q->where('status', $statusFilter);
                });
            }
            
            $tables = $tablesQuery->get();
            
            // Aktif garson çağrıları - optimize edilmiş
            $activeCalls = WaiterCall::query()
                ->select(['id', 'table_id', 'status', 'created_at'])
                ->with(['table:id,name'])
                ->where('status', 'new')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            return compact('tables', 'activeCalls');
        }, self::CACHE_SHORT);
        
        $tables = $data['tables'];
        $activeCalls = $data['activeCalls'];
            
        return view('waiter.dashboard', compact('tables', 'activeCalls'));
    }


    // Masa detay sayfası
    public function showTable($id)
    {
        $cacheKey = "waiter_table_{$id}";
        
        $data = $this->cacheQuery($cacheKey, function() use ($id) {
            $table = Table::select(['id', 'name', 'status', 'capacity'])
                ->findOrFail($id);
            
            // Mevcut sipariş (ödenmemiş) - optimize edilmiş eager loading
            $currentOrder = Order::query()
                ->select(['id', 'table_id', 'status', 'total_amount', 'created_at', 'updated_at'])
                ->where('table_id', $table->id)
                ->whereNotIn('status', ['paid', 'canceled'])
                ->latest('id')
                ->with([
                    'items' => function($q) {
                        $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                    },
                    'items.product:id,name,price'
                ])
                ->first();

            // Geçmiş siparişler (ödenmiş) - optimize edilmiş
            $pastOrders = Order::query()
                ->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
                ->where('table_id', $table->id)
                ->where('status', 'paid')
                ->with([
                    'items' => function($q) {
                        $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                    },
                    'items.product:id,name'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            return compact('table', 'currentOrder', 'pastOrders');
        }, self::CACHE_SHORT);
        
        $table = $data['table'];
        $currentOrder = $data['currentOrder'];
        $pastOrders = $data['pastOrders'];

        return view('waiter.table', [
            'table' => $table,
            'order' => $currentOrder,
            'currentOrder' => $currentOrder,
            'pastOrders' => $pastOrders,
        ]);
    }

    // Sipariş durumunu güncelle
    public function updateOrderStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'waiter') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'to_status' => ['required', 'string', 'in:pending,preparing,delivered,paid,canceled,refunded'],
        ]);

        $to = $request->string('to_status')->toString();
        $from = (string) $order->status;

        if (!$order->canTransitionTo($to, 'waiter')) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz durum geçişi.',
                'from' => $from,
                'to' => $to,
            ], 422);
        }

        DB::transaction(function () use ($order, $from, $to, $user) {
            $order->status = $to;
            $order->save();

            // Stok işlemleri
            if ($to === 'paid' && $from !== 'paid') {
                // Sipariş ödendi, stoktan düş
                $this->stockService->updateStockAfterOrder($order);
            } elseif ($to === 'canceled' && in_array($from, ['preparing', 'delivered', 'paid'])) {
                // Sipariş iptal edildi, stoku geri yükle
                $this->stockService->restoreStockAfterCancellation($order);
            }

            // Masa durumunu güncelle
            if ($order->table) {
                $order->table->updateStatusBasedOnOrders();
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => $from,
                'to_status' => $to,
                'changed_by' => $user?->id,
            ]);
            
            // Cache temizleme
            $this->clearOrderCache($order->table_id);
        });

        return response()->json([
            'success' => true,
            'message' => 'Durum güncellendi.',
            'order_id' => $order->id,
            'from' => $from,
            'to' => $to,
            'new_status' => $to,
        ]);
    }
    
    /**
     * Cache temizleme metodları
     */
    protected function clearOrderCache(int $tableId): void
    {
        Cache::forget("waiter_table_{$tableId}");
        Cache::forget('waiter_dashboard_' . md5('_'));
        
        // Tüm dashboard cache'lerini temizle
        $keys = ['waiter_dashboard_*', 'waiter_calls_*'];
        foreach ($keys as $pattern) {
            $cacheKeys = Cache::getRedis()->keys($pattern);
            if (!empty($cacheKeys)) {
                Cache::getRedis()->del($cacheKeys);
            }
        }
    }

    // Garson çağrıları listesi
    public function calls()
    {
        $page = request('page', 1);
        $cacheKey = "waiter_calls_page_{$page}";
        
        $calls = $this->cacheQuery($cacheKey, function() {
            return WaiterCall::query()
                ->select(['id', 'table_id', 'status', 'message', 'created_at', 'responded_at', 'completed_at', 'responded_by'])
                ->with(['table:id,name'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }, self::CACHE_SHORT);
            
        return view('waiter.calls', compact('calls'));
    }

    // Garson çağrısını yanıtla
    public function respondToCall(Request $request, WaiterCall $call)
    {
        $request->validate([
            'action' => 'required|in:respond,complete'
        ]);

        $action = $request->action;
        
        if ($action === 'respond') {
            $call->update([
                'status' => 'responded',
                'responded_at' => now(),
                'responded_by' => Auth::id()
            ]);
            $message = 'Çağrıya yanıt verildi.';
        } else {
            $call->update([
                'status' => 'completed',
                'completed_at' => now(),
                'responded_by' => Auth::id()
            ]);
            $message = 'Çağrı tamamlandı.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

}
