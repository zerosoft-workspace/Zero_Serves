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
        $user = Auth::user();
        
        // Filtreleme parametreleri
        $query = request('q');
        $statusFilter = request('status');

        // Cache anahtarı oluştur (filtreler ve garson ID dahil)
        $cacheKey = 'waiter_dashboard_' . $user->id . '_' . md5($query . '_' . $statusFilter);

        // Cache'i devre dışı bırak - her zaman güncel veri getir
        $data = (function () use ($query, $statusFilter, $user) {
            // Sadece bu garsona atanan masaları getir
            $tablesQuery = Table::query()
                ->select(['id', 'name', 'status', 'capacity', 'waiter_id'])
                ->where('waiter_id', $user->id)
                ->with([
                    'active_order' => function ($q) {
                        $q->select(['id', 'table_id', 'status', 'total_amount', 'customer_name', 'created_at'])
                            ->whereNotIn('status', ['paid', 'canceled']);
                    }
                ]);

            // Arama filtresi
            if ($query) {
                $tablesQuery->where('name', 'like', '%' . $query . '%');
            }

            // Durum filtresi (aktif siparişe göre)
            if ($statusFilter) {
                $tablesQuery->whereHas('active_order', function ($q) use ($statusFilter) {
                    $q->where('status', $statusFilter);
                });
            }

            $tables = $tablesQuery->get();

            // Sadece bu garsona atanan masalardan gelen çağrılar
            $activeCalls = WaiterCall::query()
                ->select(['id', 'table_id', 'status', 'created_at'])
                ->with(['table:id,name'])
                ->whereHas('table', function ($q) use ($user) {
                    $q->where('waiter_id', $user->id);
                })
                ->where('status', 'new')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return compact('tables', 'activeCalls');
        })();

        $tables = $data['tables'];
        $activeCalls = $data['activeCalls'];

        return view('waiter.dashboard', compact('tables', 'activeCalls'));
    }


    // Masa detay sayfası
    public function showTable($id)
    {
        $user = Auth::user();
        
        // Garson yetki kontrolü - sadece kendi masalarına erişebilir
        $user = Auth::user();
        if (!$user || $user->role !== 'waiter') {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        // Masanın bu garsona atanıp atanmadığını kontrol et
        $table = Table::select(['id', 'name', 'status', 'capacity', 'waiter_id'])
            ->where('id', $id)
            ->where('waiter_id', $user->id)
            ->first();
            
        if (!$table) {
            abort(403, 'Bu masaya erişim yetkiniz yok.');
        }

        $data = (function () use ($table) {
            // Aktif tüm siparişler (ödenmemiş) - en yenisi ilk
            $activeOrders = Order::query()
                ->select(['id', 'table_id', 'status', 'total_amount', 'customer_name', 'created_at', 'updated_at'])
                ->where('table_id', $table->id)
                ->whereNotIn('status', ['paid', 'canceled'])
                ->orderByDesc('id')
                ->with([
                    'items' => function ($q) {
                        $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                    },
                    'items.product:id,name,price'
                ])
                ->get();

            $currentOrder = $activeOrders->first();

            // Geçmiş siparişler (ödenmiş) - optimize edilmiş
            $pastOrders = Order::query()
                ->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
                ->where('table_id', $table->id)
                ->where('status', 'paid')
                ->with([
                    'items' => function ($q) {
                        $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                    },
                    'items.product:id,name'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return compact('table', 'currentOrder', 'activeOrders', 'pastOrders');
        })();

        $table = $data['table'];
        $currentOrder = $data['currentOrder'];
        $activeOrders = $data['activeOrders'];
        $pastOrders = $data['pastOrders'];

        return view('waiter.table', [
            'table' => $table,
            'order' => $currentOrder,
            'currentOrder' => $currentOrder,
            'activeOrders' => $activeOrders,
            'pastOrders' => $pastOrders,
        ]);
    }

    // Sipariş durumunu güncelle
    public function updateOrderStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'waiter') {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim.'
            ], 403);
        }

        // Siparişin masasının bu garsona ait olup olmadığını kontrol et
        if ($order->table && $order->table->waiter_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu siparişe erişim yetkiniz yok.'
            ], 403);
        }

        // Debug için request içeriğini logla
        \Log::info('WaiterController updateOrderStatus request:', [
            'all_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'order_current_status' => $order->status
        ]);

        // Hem status hem to_status parametrelerini kontrol et
        $to = $request->input('status') ?? $request->input('to_status');

        if (!$to) {
            return response()->json([
                'success' => false,
                'message' => 'Status parametresi bulunamadı.',
                'debug' => $request->all()
            ], 422);
        }

        // Status validation
        if (!in_array($to, ['pending', 'preparing', 'delivered', 'paid', 'canceled', 'refunded'])) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz status değeri: ' . $to
            ], 422);
        }

        $from = (string) $order->status;

        // Debug için transition kontrolü
        \Log::info('WaiterController transition check:', [
            'order_id' => $order->id,
            'from' => $from,
            'to' => $to,
            'can_transition' => $order->canTransitionTo($to, 'waiter'),
            'waiter_transitions' => \App\Models\Order::$TRANSITIONS['waiter'] ?? []
        ]);

        if (!$order->canTransitionTo($to, 'waiter')) {
            return response()->json([
                'success' => false,
                'message' => "Geçersiz durum geçişi: {$from} -> {$to}",
                'from' => $from,
                'to' => $to,
                'allowed_transitions' => \App\Models\Order::$TRANSITIONS['waiter'][$from] ?? []
            ], 422);
        }

        try {
            DB::transaction(function () use ($order, $from, $to, $user) {
                $order->status = $to;
                $order->save();

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
        } catch (\Exception $e) {
            \Log::error('WaiterController updateOrderStatus transaction error:', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'from' => $from,
                'to' => $to,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }

        // Debug için response logla
        \Log::info('WaiterController updateOrderStatus response:', [
            'success' => true,
            'order_id' => $order->id,
            'from' => $from,
            'to' => $to,
            'new_status' => $to,
        ]);

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
        try {
            $user = Auth::user();
            
            // Spesifik masa cache'ini temizle
            Cache::forget("waiter_table_{$user->id}_{$tableId}");
            
            // Dashboard cache'lerini temizle - garson bazlı
            $dashboardKeys = [
                "waiter_dashboard_{$user->id}",
                "waiter_tables_{$user->id}",
                "waiter_calls_{$user->id}_page_1",
            ];

            foreach ($dashboardKeys as $key) {
                Cache::forget($key);
            }

            // Tüm waiter cache'lerini temizle
            $patterns = [
                "waiter_dashboard_{$user->id}*",
                "waiter_tables_{$user->id}*", 
                "waiter_calls_{$user->id}*",
                "waiter_table_{$user->id}*"
            ];

            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }

            \Log::info('Cache temizlendi:', [
                'user_id' => $user->id,
                'table_id' => $tableId,
                'cleared_keys' => $dashboardKeys
            ]);

        } catch (\Exception $e) {
            // Cache temizleme hatası durumunda log'la
            \Log::warning('Cache temizleme hatası: ' . $e->getMessage());

            // Son çare olarak tüm cache'i temizle (dikkatli kullan)
            // Cache::flush();
        }
    }

    // Garson çağrıları listesi
    public function calls()
    {
        $user = Auth::user();
        $page = request('page', 1);
        $cacheKey = "waiter_calls_{$user->id}_page_{$page}";

        $calls = $this->cacheQuery($cacheKey, function () use ($user) {
            return WaiterCall::query()
                ->select(['id', 'table_id', 'status', 'message', 'created_at', 'responded_at', 'completed_at', 'responded_by'])
                ->with(['table:id,name'])
                ->whereHas('table', function ($q) use ($user) {
                    $q->where('waiter_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }, self::CACHE_SHORT);

        return view('waiter.calls', compact('calls'));
    }

    // Garson çağrısını yanıtla
    public function respondToCall(Request $request, WaiterCall $call)
    {
        // Debug için request içeriğini logla
        \Log::info('WaiterController respondToCall request:', [
            'all_data' => $request->all(),
            'call_id' => $call->id,
            'call_status' => $call->status,
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);

        $action = $request->input('action');

        if (!$action) {
            return response()->json([
                'success' => false,
                'message' => 'Action parametresi bulunamadı.',
                'debug' => $request->all()
            ], 422);
        }

        if (!in_array($action, ['respond', 'complete'])) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz action değeri: ' . $action
            ], 422);
        }

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

        // Cache temizle
        $this->clearOrderCache($call->table_id ?? 0);

        // === EK: hızlı istatistikleri hazırla ===
        $stats = [
            'new' => WaiterCall::where('status', 'new')->count(),
            'responded' => WaiterCall::where('status', 'responded')->count(),
            'completed' => WaiterCall::where('status', 'completed')->count(),
            'total' => WaiterCall::count(),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'call' => [
                    'id' => $call->id,
                    'status' => $call->status,
                    'responded_at' => optional($call->responded_at)->format('H:i'),
                    'completed_at' => optional($call->completed_at)->format('H:i'),
                    'table_name' => $call->table?->name,
                ],
                'stats' => $stats, // <=== EKLEDİK
            ]);
        }

        return back()->with('success', $message);
    }

    // Garson çağrısını sil
    public function deleteCall(WaiterCall $call)
    {
        // Debug için log
        \Log::info('WaiterController deleteCall:', [
            'call_id' => $call->id,
            'call_status' => $call->status,
            'table_id' => $call->table_id,
            'method' => request()->method(),
            'is_ajax' => request()->ajax(),
            'expects_json' => request()->expectsJson()
        ]);

        $call->delete();

        // Cache temizle
        $this->clearOrderCache($call->table_id ?? 0);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Çağrı silindi.'
            ]);
        }

        return back()->with('success', 'Çağrı silindi.');
    }

}
