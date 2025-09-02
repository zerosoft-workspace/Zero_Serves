<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Liste + Arama + Sayfalama
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $categories = Category::query()
            ->withCount('products')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        Category::create(['name' => $request->name]);

        return back()->with('success', 'Kategori eklendi');
    }

    /**
     * Silme uyarısı ve modal doldurma için sade bir JSON döner
     */
    public function show($id)
    {
        try {
            $category = Category::withCount('products')->findOrFail($id);

            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'products_count' => $category->products_count,
            ]);
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

        $category->update(['name' => $request->name]);

        return back()->with('success', 'Kategori güncellendi');
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        $productCount = $category->products_count;

        try {
            DB::transaction(function () use ($category, $id) {
                // SQLite için FK kısıtlarını geçici kapat
                DB::statement('PRAGMA foreign_keys = OFF');

                // Ürün görsellerini sil
                $products = DB::table('products')
                    ->where('category_id', $id)
                    ->whereNotNull('image')
                    ->pluck('image');

                foreach ($products as $imagePath) {
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                // Order items (bağımlı kayıtlar)
                DB::statement(
                    'DELETE FROM order_items WHERE product_id IN (SELECT id FROM products WHERE category_id = ?)',
                    [$id]
                );

                // Ürünleri sil
                DB::table('products')->where('category_id', $id)->delete();

                // Kategori resmi
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                // Kategori
                $category->delete();

                // FK tekrar aç
                DB::statement('PRAGMA foreign_keys = ON');
            });

            $message = $productCount > 0
                ? "Kategori ve {$productCount} adet ürün silindi"
                : 'Kategori silindi';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Hata durumunda FK'ları açmayı garantiye al
            DB::statement('PRAGMA foreign_keys = ON');

            return back()->with('error', 'Kategori silinirken hata oluştu: ' . $e->getMessage());
        }
    }
}
