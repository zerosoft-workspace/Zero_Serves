<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaiterController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // /admin → login'e yönlendir
    Route::get('/', fn() => redirect()->route('admin.login'));

    // Giriş
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    // Sadece giriş yapmış admin erişir
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Çıkış
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Masalar
        Route::get('/tables', [TableController::class, 'index'])->name('tables');
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::delete('/tables/{id}', [TableController::class, 'destroy'])->name('tables.destroy');
        Route::post('/tables/{table}/clear', [TableController::class, 'clear'])->name('tables.clear');

        // Kategoriler
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Ürünler
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{id}/deactivate', [ProductController::class, 'deactivate'])->name('products.deactivate');
        Route::patch('/products/{id}/activate', [ProductController::class, 'activate'])->name('products.activate');
    });
});

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('landing'))->name('landing');

/*
|--------------------------------------------------------------------------
| Müşteri (QR)
|--------------------------------------------------------------------------
*/
Route::get('/table/{token}', [CustomerOrderController::class, 'index'])->name('customer.table.token');
Route::post('/table/{token}/cart/add', [CustomerOrderController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/table/{token}/cart/remove/{productId}', [CustomerOrderController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::post('/table/{token}/cart/clear', [CustomerOrderController::class, 'clearCart'])->name('customer.cart.clear');
Route::post('/table/{token}/checkout', [CustomerOrderController::class, 'checkout'])->name('customer.checkout');
Route::post('/table/{token}/call-waiter', [CustomerOrderController::class, 'callWaiter'])->name('customer.call');
Route::post('/table/{token}/pay', [CustomerOrderController::class, 'pay'])->name('customer.pay');

/*
|--------------------------------------------------------------------------
| Garson
|--------------------------------------------------------------------------
*/
Route::prefix('waiter')->group(function () {
    Route::get('/', [WaiterController::class, 'index'])->name('waiter.index');

    // Implicit binding için {table}
    Route::get('/table/{table}', [WaiterController::class, 'showTable'])->name('waiter.table');

    // Sipariş için de implicit binding
    Route::post('/order/{order}/status', [WaiterController::class, 'updateOrderStatus'])->name('waiter.order.status');
});

