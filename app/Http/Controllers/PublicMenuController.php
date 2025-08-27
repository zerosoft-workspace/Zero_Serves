<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class PublicMenuController extends Controller
{
    public function index()
    {
        // 5 dakikalık cache (isteğe göre artır/azalt)
        $categories = Cache::remember('public_menu_categories_v1', 300, function () {
            $hasProductActiveCol = schema()->hasColumn('products', 'is_active');
            $hasCategoryActiveCol = schema()->hasColumn('categories', 'is_active');

            $query = Category::query()
                // yalnızca aktif kategori (kolon varsa)
                ->when($hasCategoryActiveCol, fn($q) => $q->where('is_active', 1))
                // en az bir ürünü olan kategoriler
                ->whereHas('products', function ($q) use ($hasProductActiveCol) {
                    $q->when($hasProductActiveCol, fn($qq) => $qq->where('is_active', 1));
                })
                // ürünleri eager load + filtre + sıralama
                ->with([
                    'products' => function ($q) use ($hasProductActiveCol) {
                        $q->when($hasProductActiveCol, fn($qq) => $qq->where('is_active', 1))
                            ->orderBy('name');
                    }
                ])
                // kategori sıralaması (order_no varsa önce onu kullan)
                ->when(
                    schema()->hasColumn('categories', 'order_no'),
                    fn($q) => $q->orderBy('order_no')->orderBy('name'),
                    fn($q) => $q->orderBy('name')
                );

            return $query->get();
        });

        return view('menu', compact('categories'));
    }
}

/**
 * Küçük helper: runtime'da kolon var mı?
 */
if (!function_exists('schema')) {
    function schema()
    {
        return app('db.schema');
    }
}
