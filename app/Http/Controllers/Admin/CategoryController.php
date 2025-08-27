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
        
        // Kategoriye ait ürünleri sil
        \DB::table('products')->where('category_id', $id)->delete();
        
        // Kategoriyi sil
        $category->delete();
        
        $message = $productCount > 0 
            ? "Kategori ve {$productCount} adet ürün silindi"
            : 'Kategori silindi';
            
        return back()->with('success', $message);
    }
}
