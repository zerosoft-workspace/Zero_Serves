<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;

class WaiterController extends Controller
{
    // Garson ana sayfa: masa listesi
    public function index()
    {

        $tables = Table::all();
        return view('waiter.dashboard', compact('tables'));
    }

    // Masa detay sayfası
    public function showTable($id)
    {
        $table = Table::findOrFail($id);
        $order = $table->orders()->latest()->with('items.product')->first();
        return view('waiter.table', compact('table', 'order'));
    }

    // Sipariş durumunu güncelle
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,preparing,delivered,paid']);

        $order->update(['status' => $request->status]);

        if ($request->status === 'paid') {
            $order->table->update(['status' => 'empty']);
        } else {
            $order->table->update(['status' => $request->status]);
        }

        return back()->with('success', 'Sipariş durumu güncellendi.');
    }
}
