<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));            // isim/açıklama arama
        $category = $request->get('category');                    // kategori id
        $status = $request->get('status');                      // active | inactive | low_stock

        $products = Product::query()
            ->with('category')
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($category !== null && $category !== '', function ($builder) use ($category) {
                $builder->where('category_id', $category);
            })
            ->when($status === 'active', function ($builder) {
                $builder->where('is_active', true);
            })
            ->when($status === 'inactive', function ($builder) {
                $builder->where('is_active', false);
            })
            ->when($status === 'low_stock', function ($builder) {
                // low stock: stok <= min
                $builder->whereColumn('stock_quantity', '<=', 'min_stock_level');
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString(); // filtreler sayfalama linklerinde kalsın

        $categories = Category::orderBy('name')->get();

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

        $product->update($data);

        return back()->with('success', 'Ürün başarıyla güncellendi');
    }

    public function destroy($id)
    {
        $p = Product::findOrFail($id);

        // Siparişlerde kullanılmışsa silme, pasifleştir - optimize edilmiş
        $used = \DB::table('order_items')->where('product_id', $id)->exists();

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
