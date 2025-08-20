<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    // Masa listesi (Admin için)
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables.index', compact('tables'));
    }

    // Yeni masa oluştur + QR üret
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $table = Table::create([
            'name' => $request->name,
            'token' => bin2hex(random_bytes(16)), // 32 karakterlik benzersiz token
            'status' => 'empty'
        ]);

        return redirect()->back()->with('success', 'Masa oluşturuldu');
    }
    public function showByToken(string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();


        // Oturumu bu masa ile ilişkilendir
        session(['table_id' => $table->id, 'table_token' => $token]);

        // Ürünleri kategorileriyle al
        $categories = \App\Models\Category::with([
            'products' => function ($q) {
                $q->where('is_active', true);
            }
        ])->get();


        return view('customer.menu', compact('table', 'categories'));
    }

    public function destroy($id)
    {
        $table = \App\Models\Table::findOrFail($id);

        // Açık sipariş var mı? (ödeme tamamlanmamış akışlar)
        $hasOpen = $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivered'])
            ->exists();

        if ($hasOpen) {
            return back()->with('error', 'Bu masada devam eden sipariş var. Önce siparişi kapatın (Ödendi).');
        }

        $table->delete(); // (Not: orders.table_id FK’iniz cascade ise geçmiş siparişler de silinir)

        return back()->with('success', 'Masa silindi.');
    }


    // QR okutunca müşteri tarafı
    public function showCustomerPage($id)
    {
        $table = Table::findOrFail($id);
        return view('customer.menu', compact('table'));
    }
}
