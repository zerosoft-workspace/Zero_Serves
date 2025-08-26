<?php

namespace App\Http\Controllers;

use App\Models\Category;

class PublicMenuController extends Controller
{
    public function index()
    {
        // Yalnızca aktif ürünleri çek (sende "add_is_active_to_products" migration’ı var)
        $categories = Category::with([
            'products' => function ($q) {
                $q->when(schema()->hasColumn('products', 'is_active'), fn($qq) => $qq->where('is_active', 1))
                    ->orderBy('name');
            }
        ])->orderBy('name')->get();

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
