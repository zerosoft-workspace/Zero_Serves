<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // Masa listesi (Admin için)
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables.index', compact('tables'));
    }

    // Yeni masa oluştur
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        Table::create([
            'name' => $request->name,
            'token' => bin2hex(random_bytes(16)), // benzersiz token
            'status' => 'empty'
        ]);

        return redirect()->back()->with('success', 'Masa oluşturuldu');
    }
    public function clear(Table $table)
    {
        // Masanın aktif siparişlerini sil
        $table->orders()->whereIn('status', ['pending', 'preparing', 'delivered'])->delete();

        // Masayı boş hale getir → enum'da tanımlı olan değer: "empty"
        $table->status = 'empty';
        $table->save();

        return back()->with('success', 'Masa başarıyla temizlendi.');
    }


    // Masa sil
    public function destroy($id)
    {
        $table = Table::findOrFail($id);

        $hasOpen = $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivered'])
            ->exists();

        if ($hasOpen) {
            return back()->with('error', 'Bu masada devam eden sipariş var. Önce siparişi kapatın (Ödendi).');
        }

        $table->delete();

        return back()->with('success', 'Masa silindi.');
    }
}
