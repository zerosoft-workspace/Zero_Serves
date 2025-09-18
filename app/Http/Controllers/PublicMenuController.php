<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class PublicMenuController extends Controller
{
    public function index()
    {
        
        // 1) Versiyon tohumu: en son değişiklik zamanları + sayılar (toggle'ları da yakalar)
        $hasProductActiveCol  = schema()->hasColumn('products', 'is_active');
        $hasCategoryActiveCol = schema()->hasColumn('categories', 'is_active');

        $prodActiveCount = $hasProductActiveCol
            ? Product::where('is_active', 1)->count()
            : Product::count();

        $catActiveCount = $hasCategoryActiveCol
            ? Category::where('is_active', 1)->count()
            : Category::count();

        $verSeed = implode('|', [
            (string) Category::max('updated_at'),
            (string) Category::max('deleted_at'),
            (int) Category::count(),
            (int) $catActiveCount,
            (string) Product::max('updated_at'),
            (string) Product::max('deleted_at'),
            (int) Product::count(),
            (int) $prodActiveCount,
        ]);
        $verHash = substr(md5($verSeed), 0, 12);
        $cacheKey = "public_menu_categories_v1:{$verHash}";

        // 2) Cache (5 dk). Versiyon değişince otomatik yeni key ile tazelenir.
        $categories = Cache::remember($cacheKey, 300, function () use ($hasProductActiveCol, $hasCategoryActiveCol) {
            $query = Category::query()
                ->when($hasCategoryActiveCol, fn($q) => $q->where('is_active', 1))
                ->whereHas('products', function ($q) use ($hasProductActiveCol) {
                    $q->when($hasProductActiveCol, fn($qq) => $qq->where('is_active', 1));
                })
                ->with([
                    'products' => function ($q) use ($hasProductActiveCol) {
                        $q->when($hasProductActiveCol, fn($qq) => $qq->where('is_active', 1))
                          ->orderBy('name');
                    }
                ])
                ->when(
                    schema()->hasColumn('categories', 'order_no'),
                    fn($q) => $q->orderBy('order_no')->orderBy('name'),
                    fn($q) => $q->orderBy('name')
                );

            return $query->get(['id', 'name', 'image']);
        });

        // İstersen testte tarayıcı/CDN cache'ini by-pass et:
        return response()->view('menu', compact('categories'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        // Prod'da üstteki header'ı kaldırabilirsin.
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
