<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // 👈 eklendi

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
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\CustomerCartController;

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Session Management & API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/csrf-token', [SessionController::class, 'getCsrfToken'])->name('csrf.token');
    Route::get('/session/status', [SessionController::class, 'checkStatus'])->name('session.status');
    Route::post('/session/heartbeat', [SessionController::class, 'heartbeat'])->name('session.heartbeat');
    Route::post('/session/browser-close', [SessionController::class, 'browserClose'])->name('session.browser-close');
});

// Session expired page
Route::get('/session-expired', [SessionController::class, 'expired'])->name('session.expired');
Route::post('/logout', [SessionController::class, 'logout'])->name('logout');

// Admin login routes (accessible to guests)
Route::prefix('admin')->name('admin.')->group(function () {
    // /admin → akıllı giriş noktası
    Route::get('/', function () {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->name('entry'); // landing'ten buna link ver

    /** Misafir (login ekranları) */
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');      // admin.login
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');        // admin.login.post
    });
});

// Admin authenticated routes
Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'multi.auth:admin,admin'])->group(function () {
    /** Girişli admin */
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');                  // admin.dashboard
    Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats'])->name('dashboard.stats'); // admin.dashboard.stats
    Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('notifications');                  // admin.dashboard

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');                      // admin.logout
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout.get');                   // admin.logout (GET fallback)

    Route::prefix('tables')
        ->name('tables.')
        ->controller(\App\Http\Controllers\Admin\TableController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::post('/{table}/clear', 'clear')->whereNumber('table')->name('clear');
            Route::delete('/{table}', 'destroy')->whereNumber('table')->name('destroy');

            Route::post('/assign-waiter', 'assignWaiter')->name('assign.waiter');
            Route::post('/bulk-assign', 'bulkAssign')->name('bulk.assign');
            Route::get('/assignment-report', 'assignmentReport')->name('assignment.report');

            // PDF (toplu ve tek)
            Route::post('/qr-pdf', 'qrPdfAll')->name('qr.pdf');
            Route::get('/{table}/qr-pdf', 'qrPdfSingle')->whereNumber('table')->name('qr.single');
        });

    Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->whereNumber('id')->name('show');
        Route::put('/{id}', 'update')->whereNumber('id')->name('update');
        Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
    });

    Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->whereNumber('id')->name('show');
        Route::put('/{id}', 'update')->whereNumber('id')->name('update');
        Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
        Route::patch('/{id}/deactivate', 'deactivate')->whereNumber('id')->name('deactivate');
        Route::patch('/{id}/activate', 'activate')->whereNumber('id')->name('activate');
    });

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{user}', 'update')->whereNumber('user')->name('update');
        Route::delete('/{user}', 'destroy')->whereNumber('user')->name('destroy');
        Route::get('/{id}/permissions', 'permissions')->whereNumber('id')->name('permissions');
    });

    Route::prefix('stock')->name('stock.')->controller(StockController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/low-stock', 'lowStock')->name('low-stock');
        Route::get('/reports', 'reports')->name('reports');
        Route::get('/export', 'exportStock')->name('export');
        Route::get('/stats', 'stockStats')->name('stats');
        Route::get('/alerts', 'checkStockAlerts')->name('alerts');
        Route::get('/suggestions', 'restockSuggestions')->name('suggestions');
        Route::get('/{product}/history', 'stockHistory')->name('history');
        Route::put('/{product}/update', 'updateStock')->name('update');
        Route::post('/bulk-update', 'bulkUpdateStock')->name('bulk-update');
    });

    Route::prefix('orders')->name('orders.')->controller(OrderManagementController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulk-status');
        Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
        Route::get('/stats/realtime', 'realtimeUpdates')->name('realtime');
        Route::get('/export/csv', 'export')->name('export');
        Route::get('/{order}', 'show')->name('show');
        Route::put('/{order}/status', 'updateStatus')->name('update-status');
        Route::delete('/{order}', 'destroy')->name('destroy');
        Route::get('/{order}/print', 'print')->name('print');
    });

    Route::prefix('reservations')->name('reservations.')->controller(AdminReservationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{reservation}', 'show')->whereNumber('reservation')->name('show');
        Route::post('/{reservation}/approve', 'approve')->whereNumber('reservation')->name('approve');
        Route::post('/{reservation}/reject', 'reject')->whereNumber('reservation')->name('reject');
        Route::delete('/{reservation}', 'destroy')->whereNumber('reservation')->name('destroy');
        Route::get('/_unread-count', 'unreadCount')->name('unread_count');


    });

});

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('landing'))->name('landing');
Route::get('/menu', [PublicMenuController::class, 'index'])->name('public.menu');
Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
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

Route::get('/table/{token}/cart', [\App\Http\Controllers\CustomerCartController::class, 'view'])
    ->name('customer.cart.view');
Route::get('/table/{token}/cart/items', [\App\Http\Controllers\CustomerCartController::class, 'items'])
    ->name('customer.cart.items');

Route::get('/table/{token}/menu/{category}', [\App\Http\Controllers\CustomerOrderController::class, 'category'])
    ->name('customer.menu');
/*
|--------------------------------------------------------------------------
| Garson
|--------------------------------------------------------------------------
*/
Route::prefix('waiter')->name('waiter.')->group(function () {
    // /waiter → akıllı giriş noktası
    Route::get('/', function () {
        if (Auth::check() && Auth::user()->role === 'waiter') {
            return redirect()->route('waiter.dashboard');
        }
        return redirect()->route('waiter.login');
    })->name('entry'); // 👈 landing'ten buna link ver

    // Misafir (login)
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [WaiterAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [WaiterAuthController::class, 'login'])->name('login.post');
    });

    // Giriş yapan garson
    Route::middleware([
        'auth:waiter',
        'multi.auth:waiter,waiter',
        'prevent.back.history',
    ])->group(function () {
        Route::post('/logout', [WaiterAuthController::class, 'logout'])->name('logout');
        Route::get('/logout', [WaiterAuthController::class, 'logout'])->name('logout.get');

        Route::get('/dashboard', [WaiterController::class, 'index'])->name('dashboard');
        Route::get('/table/{table}', [WaiterController::class, 'showTable'])->name('table');
        Route::post('/orders/{order}/status', [WaiterController::class, 'updateOrderStatus'])->name('orders.status');

        // Garson çağrısı yönetimi
        Route::get('/calls', [WaiterController::class, 'calls'])->name('calls');
        Route::post('/calls/{call}/respond', [WaiterController::class, 'respondToCall'])->name('calls.respond');
        Route::delete('/calls/{call}', [WaiterController::class, 'deleteCall'])->name('calls.delete');
    });
});

// Debug routes (sadece development için)
if (config('app.debug')) {
    Route::prefix('debug')->name('debug.')->controller(App\Http\Controllers\DebugController::class)->group(function () {
        Route::get('/cache-status', 'cacheStatus')->name('cache.status');
        Route::post('/clear-cache', 'clearCache')->name('cache.clear');
    });
}

// (isteğe bağlı) debug logout
Route::get('/_force_logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return 'forced logout';
});
