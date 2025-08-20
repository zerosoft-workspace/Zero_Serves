<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

// =========================
// Admin tarafı
// =========================
Route::get('/admin/tables', [TableController::class, 'index'])->name('admin.tables');
Route::post('/admin/tables', [TableController::class, 'store'])->name('admin.tables.store');
Route::delete('/admin/tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
Route::post('/admin/tables/{table}/clear', [App\Http\Controllers\TableController::class, 'clear'])
    ->name('admin.tables.clear');


Route::prefix('admin')->group(function () {
    // Kategoriler
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::patch('/products/{id}/deactivate', [ProductController::class, 'deactivate'])->name('admin.products.deactivate');
    Route::patch('/products/{id}/activate', [ProductController::class, 'activate'])->name('admin.products.activate');

    // Ürünler
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});

// =========================
// Landing Page
// =========================
Route::get('/', function () {
    return view('landing');
})->name('landing');

// =========================
// Müşteri tarafı (QR kod okutulunca açılır)
// =========================
Route::get('/table/{token}', [CustomerOrderController::class, 'index'])->name('customer.table.token');

Route::post('/table/{token}/cart/add', [CustomerOrderController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/table/{token}/cart/remove/{productId}', [CustomerOrderController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::post('/table/{token}/cart/clear', [CustomerOrderController::class, 'clearCart'])->name('customer.cart.clear');
Route::post('/table/{token}/checkout', [CustomerOrderController::class, 'checkout'])->name('customer.checkout');
Route::post('/table/{token}/call-waiter', [CustomerOrderController::class, 'callWaiter'])->name('customer.call');
Route::post('/table/{token}/pay', [CustomerOrderController::class, 'pay'])->name('customer.pay');

// =========================
// Garson Paneli
// =========================
Route::prefix('waiter')->group(function () {
    Route::get('/', [WaiterController::class, 'index'])->name('waiter.index');
    Route::get('/table/{id}', [WaiterController::class, 'showTable'])->name('waiter.table');
    Route::post('/order/{id}/status', [WaiterController::class, 'updateOrderStatus'])->name('waiter.order.status');
});
