<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = ['name' => $request->name];

        // Fotoğraf yükleme işlemi
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Debug bilgisi
            \Log::info('Category image upload attempt', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'is_valid' => $image->isValid()
            ]);
            
            if ($image->isValid()) {
                try {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('categories', $imageName, 'public');
                    $data['image'] = $imagePath;
                    \Log::info('Category image uploaded successfully', ['path' => $imagePath]);
                } catch (\Exception $e) {
                    \Log::error('Category image upload failed', ['error' => $e->getMessage()]);
                    return back()->withErrors(['image' => 'Fotoğraf yüklenirken hata oluştu: ' . $e->getMessage()]);
                }
            } else {
                \Log::error('Invalid category image file');
                return back()->withErrors(['image' => 'Geçersiz resim dosyası']);
            }
        }

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
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = ['name' => $request->name];

        // Fotoğraf güncelleme işlemi
        if ($request->hasFile('image')) {
            // Eski fotoğrafı sil
            if ($category->image && \Storage::disk('public')->exists($category->image)) {
                \Storage::disk('public')->delete($category->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $category->update($data);
        return back()->with('success', 'Kategori güncellendi');
    }

    public function destroy($id)
    {
        $category = Category::with('products')->findOrFail($id);
        
        // Kategoriye ait ürün sayısını al
        $productCount = $category->products->count();
        
        // Kategoriye ait ürünlerin fotoğraflarını sil
        foreach ($category->products as $product) {
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
        }
        
        // Kategoriye ait ürünleri sil
        $category->products()->delete();
        
        // Kategori fotoğrafını sil
        if ($category->image && \Storage::disk('public')->exists($category->image)) {
            \Storage::disk('public')->delete($category->image);
        }
        
        // Kategoriyi sil
        $category->delete();
        
        $message = $productCount > 0 
            ? "Kategori ve {$productCount} adet ürün silindi"
            : 'Kategori silindi';
            
        return back()->with('success', $message);
    }
}
