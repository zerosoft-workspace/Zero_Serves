<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
        $this->middleware('auth');
    }

    /**
     * Sipariş yönetimi ana sayfası
     */
    public function index(Request $request)
    {
        $query = Order::with(['table:id,name', 'orderItems:order_id,product_id,quantity'])
            ->select(['id', 'table_id', 'status', 'total_amount', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc');

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('table', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(20);

        // İstatistikler - optimize edilmiş
        $stats = $this->getOrderStatsOptimized();

        // Masalar (filtreleme için) - cache edilebilir
        $tables = Table::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.orders.index', compact('orders', 'stats', 'tables'));
    }

    /**
     * Sipariş detayları
     */
    public function show(Order $order)
    {
        $order->load(['table', 'orderItems.product', 'payments']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Sipariş durumu güncelleme
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,delivered,paid,cancelled'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Durum geçiş kontrolü
        if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz durum geçişi'
            ], 400);
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            $order->update(['status' => $newStatus]);

            // Stok işlemleri
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                // Sipariş ödendi, stoktan düş
                $this->stockService->updateStockAfterOrder($order);
            } elseif ($newStatus === 'cancelled' && in_array($oldStatus, ['preparing', 'delivered', 'paid'])) {
                // Sipariş iptal edildi, stoku geri yükle
                $this->stockService->restoreStockAfterCancellation($order);
            }

            // Masa durumunu güncelle
            if ($order->table) {
                $order->table->updateStatusBasedOnOrders();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sipariş durumu başarıyla güncellendi',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'status_color' => $this->getStatusColor($order->status)
            ]
        ]);
    }

    /**
     * Toplu durum güncelleme
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,preparing,delivered,paid,cancelled'
        ]);

        $updated = 0;
        foreach ($request->order_ids as $orderId) {
            $order = Order::find($orderId);
            if ($order && $this->isValidStatusTransition($order->status, $request->status)) {
                $order->update(['status' => $request->status]);
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} sipariş güncellendi"
        ]);
    }

    /**
     * Toplu sipariş silme
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id'
        ]);

        $deleted = 0;
        $errors = [];

        DB::transaction(function () use ($request, &$deleted, &$errors) {
            foreach ($request->order_ids as $orderId) {
                try {
                    $order = Order::with(['orderItems', 'payments'])->find($orderId);
                    if (!$order)
                        continue;

                    // Stoku geri yükle (eğer sipariş preparing veya delivered durumundaysa)
                    if (in_array($order->status, ['preparing', 'delivered'])) {
                        $this->stockService->restoreStockAfterCancellation($order);
                    }

                    // İlişkili kayıtları sil
                    $order->orderItems()->delete();
                    $order->payments()->delete();

                    // Siparişi sil
                    $order->delete();
                    $deleted++;

                } catch (\Exception $e) {
                    $errors[] = "Sipariş #{$orderId}: " . $e->getMessage();
                }
            }
        });

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bazı siparişler silinemedi',
                'errors' => $errors,
                'deleted' => $deleted
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "{$deleted} sipariş başarıyla silindi"
        ]);
    }

    /**
     * Sipariş silme
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Stoku geri yükle (eğer sipariş preparing veya delivered durumundaysa)
                if (in_array($order->status, ['preparing', 'delivered'])) {
                    $this->stockService->restoreStockAfterCancellation($order);
                }

                // İlişkili kayıtları sil
                $order->orderItems()->delete();
                $order->payments()->delete();

                // Siparişi sil
                $order->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş silinirken bir hata oluştu'
            ], 500);
        }
    }

    /**
     * Sipariş istatistikleri
     */
    public function getOrderStats()
    {
        $today = now()->startOfDay();

        return [
            'total_today' => Order::whereDate('created_at', $today)->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'today_revenue' => Order::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'avg_preparation_time' => $this->getAveragePreparationTime()
        ];
    }

    /**
     * Optimize edilmiş sipariş istatistikleri
     */
    public function getOrderStatsOptimized()
    {
        $today = now()->startOfDay();

        // Tek query ile tüm istatistikleri al
        $stats = DB::table('orders')
            ->select([
                DB::raw('COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as total_today'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN status = "preparing" THEN 1 END) as preparing'),
                DB::raw('COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered'),
                DB::raw('SUM(CASE WHEN DATE(created_at) = ? AND status = "paid" THEN total_amount ELSE 0 END) as today_revenue')
            ])
            ->setBindings([$today->format('Y-m-d'), $today->format('Y-m-d')])
            ->first();

        return [
            'total_today' => $stats->total_today,
            'pending' => $stats->pending,
            'preparing' => $stats->preparing,
            'delivered' => $stats->delivered,
            'today_revenue' => $stats->today_revenue,
            'avg_preparation_time' => $this->getAveragePreparationTime()
        ];
    }

    /**
     * Real-time sipariş güncellemeleri
     */
    public function realtimeUpdates()
    {
        $recentOrders = Order::with(['table', 'orderItems.product'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->getOrderStats();

        return response()->json([
            'recent_orders' => $recentOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_name' => $order->table->name ?? 'Masa Yok',
                    'status' => $order->status,
                    'status_text' => $this->getStatusText($order->status),
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->format('H:i'),
                    'items_count' => $order->orderItems->count()
                ];
            }),
            'stats' => $stats
        ]);
    }

    /**
     * Sipariş yazdırma
     */
    public function print(Order $order)
    {
        $order->load(['table', 'orderItems.product']);

        return view('admin.orders.print', compact('order'));
    }

    /**
     * Sipariş export
     */
    public function export(Request $request)
    {
        $query = Order::with(['table', 'orderItems.product']);

        // Filtreleme
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'siparisler_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, [
                'Sipariş No',
                'Masa',
                'Durum',
                'Toplam Tutar',
                'Ürün Sayısı',
                'Sipariş Tarihi',
                'Güncelleme Tarihi'
            ], ';');

            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->table->name ?? 'Masa Yok',
                    $this->getStatusText($order->status),
                    number_format($order->total_amount, 2),
                    $order->orderItems->count(),
                    $order->created_at->format('d.m.Y H:i'),
                    $order->updated_at->format('d.m.Y H:i')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Durum geçiş kontrolü
     */
    private function isValidStatusTransition($from, $to)
    {
        $validTransitions = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['delivered', 'cancelled'],
            'delivered' => ['paid', 'cancelled'],
            'paid' => [],
            'cancelled' => []
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    /**
     * Durum metni
     */
    private function getStatusText($status)
    {
        return match ($status) {
            'pending' => 'Bekliyor',
            'preparing' => 'Hazırlanıyor',
            'delivered' => 'Teslim Edildi',
            'paid' => 'Ödendi',
            'cancelled' => 'İptal Edildi',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Durum rengi
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'preparing' => 'info',
            'delivered' => 'success',
            'paid' => 'secondary',
            'cancelled' => 'danger'
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Ortalama hazırlık süresi
     */
    private function getAveragePreparationTime()
    {
        $orders = Order::where('status', 'served')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $totalMinutes = $orders->sum(function ($order) {
            return $order->created_at->diffInMinutes($order->updated_at);
        });

        return round($totalMinutes / $orders->count());
    }
}
