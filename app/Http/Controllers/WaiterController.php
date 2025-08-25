<?php

namespace App\Http\Controllers;
use App\Models\OrderStatusLog;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class WaiterController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    // Garson ana sayfa: masa listesi
    public function index()
    {
        $tables = Table::with('active_order')->get();
        return view('waiter.dashboard', compact('tables'));
    }


    // Masa detay sayfası
    public function showTable($id)
    {
        $table = Table::findOrFail($id);
        // Örnek: showTable($tableId) gibi bir metot içinde…
        $order = Order::query()
            ->where('table_id', $table->id)
            ->latest('id')
            ->with(['items.product', 'table']) // <-- ekledik
            ->first();

        if ($order) {
            $order->loadMissing(['items.product']); // <-- kritik satır
        }
        return view('waiter.table', [
            'table' => $table,
            'order' => $order,
            // 'statusLogs' => $order?->statusLogs ?? collect(),  // varsa
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

}
