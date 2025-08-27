<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock_quantity' => $request->stock_quantity,
            'min_stock_level' => $request->min_stock_level,
            'max_stock_level' => $request->max_stock_level,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Fotoğraf yükleme işlemi
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Debug bilgisi
            \Log::info('Image upload attempt', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'is_valid' => $image->isValid()
            ]);
            
            if ($image->isValid()) {
                try {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('products', $imageName, 'public');
                    $data['image'] = $imagePath;
                    \Log::info('Image uploaded successfully', ['path' => $imagePath]);
                } catch (\Exception $e) {
                    \Log::error('Image upload failed', ['error' => $e->getMessage()]);
                    return back()->withErrors(['image' => 'Fotoğraf yüklenirken hata oluştu: ' . $e->getMessage()]);
                }
            } else {
                \Log::error('Invalid image file');
                return back()->withErrors(['image' => 'Geçersiz resim dosyası']);
            }
        }

        Product::create($data);

        return back()->with('success', 'Ürün başarıyla eklendi');
    }

    public function show($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock_quantity' => $request->stock_quantity,
            'min_stock_level' => $request->min_stock_level,
            'max_stock_level' => $request->max_stock_level,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Fotoğraf güncelleme işlemi
        if ($request->hasFile('image')) {
            // Eski fotoğrafı sil
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return back()->with('success', 'Ürün başarıyla güncellendi');
    }

    public function destroy($id)
    {
        $p = Product::findOrFail($id);

        // Siparişlerde kullanılmışsa silme, pasifleştir
        $used = OrderItem::where('product_id', $p->id)->exists();

        if ($used) {
            $p->update(['is_active' => false]);
            return back()->with('success', 'Ürün siparişlerde kullanılıyor. Silinmedi, pasifleştirildi.');
        }

        $p->delete();
        return back()->with('success', 'Ürün silindi.');
    }
    public function deactivate($id)
    {
        $p = Product::findOrFail($id);
        $p->update(['is_active' => false]);
        return back()->with('success', 'Ürün pasifleştirildi (menüden kaldırıldı).');
    }

    public function activate($id)
    {
        $p = Product::findOrFail($id);
        $p->update(['is_active' => true]);
        return back()->with('success', 'Ürün yeniden aktifleştirildi.');
    }

}
