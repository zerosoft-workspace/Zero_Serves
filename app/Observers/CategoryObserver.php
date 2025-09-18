<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    private function bust(): void
    {
        Cache::forget('public_menu_categories_v1');
    }

    public function created(Category $category): void   { $this->bust(); }
    public function updated(Category $category): void   { $this->bust(); } // is_active/order_no
    public function deleted(Category $category): void   { $this->bust(); }
    public function restored(Category $category): void  { $this->bust(); }
    public function forceDeleted(Category $category): void { $this->bust(); }
}
