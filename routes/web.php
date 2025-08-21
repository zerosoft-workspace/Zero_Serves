<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Waiter\WaiterAuthController;


use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminAuthController;


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/


Route::prefix('admin')->name('admin.')->group(function () {
    // /admin → login (misafir), girişliyse dashboard
    Route::get('/', fn() => redirect()->route('admin.login'));


    /** Misafir (login ekranları) */
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    /** Girişli admin */
    Route::middleware(['auth', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats'])->name('dashboard.stats');

        // Çıkış
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Masalar
        Route::prefix('tables')->name('tables.')->controller(TableController::class)->group(function () {
            Route::get('/', 'index')->name('index');                // admin.tables.index
            Route::post('/', 'store')->name('store');               // admin.tables.store
            Route::post('/{table}/clear', 'clear')
                ->whereNumber('table')->name('clear');              // admin.tables.clear
            Route::delete('/{table}', 'destroy')
                ->whereNumber('table')->name('destroy');            // admin.tables.destroy
        });

        // Kategoriler
        Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
        });

        // Ürünler
        Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
            Route::patch('/{id}/deactivate', 'deactivate')->whereNumber('id')->name('deactivate');
            Route::patch('/{id}/activate', 'activate')->whereNumber('id')->name('activate');
        });

        // Kullanıcılar (REST'e uygun: index/store/update/destroy) + izinler
        Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');               // NOT: create yerine store
            Route::put('/{user}', 'update')->whereNumber('user')->name('update');
            Route::delete('/{user}', 'destroy')->whereNumber('user')->name('destroy');

            Route::get('/{id}/permissions', 'permissions')
                ->whereNumber('id')->name('permissions');
        });
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
Route::prefix('waiter')->name('waiter.')->group(function () {
    // /waiter → login ekranına yönlendir
    Route::get('/', fn() => redirect()->route('waiter.login'));

    // 🔑 Login işlemleri
    Route::get('/login', [WaiterAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WaiterAuthController::class, 'login'])->name('login.post');

    // 🔒 Giriş yapan garson erişebilir
    Route::middleware(['auth', 'waiter'])->group(function () {   // 👈 role:waiter yerine waiter
        Route::post('/logout', [WaiterAuthController::class, 'logout'])->name('logout');

        // Garson dashboard → WaiterController@index
        Route::get('/dashboard', [WaiterController::class, 'index'])->name('dashboard');

        // Implicit binding: {table}
        Route::get('/table/{table}', [WaiterController::class, 'showTable'])->name('table');

        // Sipariş durum güncelleme
        Route::post('/order/{order}/status', [WaiterController::class, 'updateOrderStatus'])->name('order.status');
    });
});

