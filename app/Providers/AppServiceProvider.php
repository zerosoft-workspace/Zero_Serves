<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Observer ve modelleri dahil et
use App\Models\Product;
use App\Models\Category;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer kayıtları
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
    }
}
