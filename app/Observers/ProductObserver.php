<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    private function bust(): void
    {
        Cache::forget('public_menu_categories_v1');
    }

    public function created(Product $product): void   { $this->bust(); }
    public function updated(Product $product): void   { $this->bust(); }
    public function deleted(Product $product): void   { $this->bust(); }
    public function restored(Product $product): void  { $this->bust(); }
    public function forceDeleted(Product $product): void { $this->bust(); }
}
