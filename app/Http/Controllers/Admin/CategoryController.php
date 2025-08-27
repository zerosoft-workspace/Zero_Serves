<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $data = ['name' => $request->name];

        Category::create($data);
        return back()->with('success', 'Kategori eklendi');
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kategori bulunamadı'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $data = ['name' => $request->name];

        $category->update($data);
        return back()->with('success', 'Kategori güncellendi');
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        
        // Kategoriye ait ürün sayısını al
        $productCount = $category->products_count;
        
        try {
            \DB::transaction(function () use ($category, $id) {
                // SQLite için foreign key constraint'leri geçici olarak devre dışı bırak
                \DB::statement('PRAGMA foreign_keys = OFF');
                
                // Önce kategoriye ait ürünlerin resimlerini sil
                $products = \DB::table('products')
                    ->where('category_id', $id)
                    ->whereNotNull('image')
                    ->pluck('image');
                    
                foreach ($products as $imagePath) {
                    if ($imagePath && \Storage::disk('public')->exists($imagePath)) {
                        \Storage::disk('public')->delete($imagePath);
                    }
                }
                
                // Order items tablosundan bu ürünlere ait kayıtları sil
                \DB::statement('DELETE FROM order_items WHERE product_id IN (SELECT id FROM products WHERE category_id = ?)', [$id]);
                
                // Kategoriye ait ürünleri sil
                \DB::table('products')->where('category_id', $id)->delete();
                
                // Kategori resmini sil
                if ($category->image && \Storage::disk('public')->exists($category->image)) {
                    \Storage::disk('public')->delete($category->image);
                }
                
                // Kategoriyi sil
                $category->delete();
                
                // Foreign key constraint'leri tekrar aktif et
                \DB::statement('PRAGMA foreign_keys = ON');
            });
            
            $message = $productCount > 0 
                ? "Kategori ve {$productCount} adet ürün silindi"
                : 'Kategori silindi';
                
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            // Hata durumunda foreign key'leri tekrar aç
            \DB::statement('PRAGMA foreign_keys = ON');
            
            return back()->with('error', 'Kategori silinirken hata oluştu: ' . $e->getMessage());
        }
    }
}
