<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Bugünkü tarih
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // === TEMEL İSTATİSTİKLER ===

        // Masalar
        $totalTables = Table::count();
        $activeTables = Table::whereHas('orders', function ($query) {
            $query->whereIn('status', ['pending', 'preparing'])
                ->whereDate('created_at', Carbon::today());
        })->count();

        // Siparişler
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::whereIn('status', ['pending', 'preparing'])->count();

        // Gelir
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('total_amount');

        $monthlyRevenue = Order::whereBetween('created_at', [$startOfMonth, now()])
            ->where('status', 'paid')
            ->sum('total_amount');

        // Dün ile karşılaştırma
        $yesterdayRevenue = Order::whereDate('created_at', $today->copy()->subDay())
            ->where('status', 'paid')
            ->sum('total_amount');

        $revenueGrowth = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : 0;

        // Müşteriler (unique table_id bazında)
        $todayCustomers = Order::whereDate('created_at', $today)
            ->distinct('table_id')
            ->count('table_id');

        // === DETAYLI ANALİTİKLER ===

        // En popüler ürün
        $popularProductData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', $today)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->first();
            
        $popularProduct = $popularProductData ? $popularProductData->name : 'Veri yok';
        $popularProductQuantity = $popularProductData ? $popularProductData->total_quantity : 0;

        // Ortalama sipariş tutarı
        $averageOrderValue = $todayOrders > 0
            ? round($todayRevenue / $todayOrders, 2)
            : 0;

        // === SON AKTİVİTELER ===

        $recentActivities = Order::with(['table', 'orderItems.product'])
            ->whereDate('created_at', $today)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_name' => $order->table->name ?? 'Masa ' . $order->table_id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'time_ago' => $order->updated_at->diffForHumans(),
                    'items_count' => $order->orderItems->count(),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at
                ];
            });

        // === SİSTEM DURUMU ===

        // Aktif kullanıcılar (son 30 dakikada giriş yapmış)
        $activeUsers = User::where('last_activity', '>', Carbon::now()->subMinutes(30))
            ->count();

        // Stok durumu (düşük stoklu ürünler)
        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->where('is_active', true)
            ->count();

        // Bugün tamamlanan siparişler
        $completedOrders = Order::whereDate('created_at', $today)
            ->where('status', 'delivered')
            ->count();

        // === HAFTALIK TRENDLİ VERİLER ===

        $weeklyData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyRevenue = Order::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('total_amount');
            $dailyOrders = Order::whereDate('created_at', $date)->count();

            $weeklyData->push([
                'date' => $date->format('d.m'),
                'day_name' => $date->isoFormat('dddd'),
                'revenue' => $dailyRevenue,
                'orders' => $dailyOrders
            ]);
        }

        // === MASA DURUMU ===

        $tableStatus = [
            'empty' => Table::whereDoesntHave('orders', function ($query) {
                $query->whereIn('status', ['pending', 'preparing'])
                    ->whereDate('created_at', Carbon::today());
            })->count(),
            'occupied' => $activeTables,
            'reserved' => Table::where('status', 'reserved')->count()
        ];

        // === SİPARİŞ DURUMU DAĞILIMI ===

        $orderStatusDistribution = Order::whereDate('created_at', $today)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // === KATEGORİ BAZINDA SATIŞ ===

        $categoryStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', 'paid')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Zaman verileri
        $currentDate = Carbon::now()->isoFormat('DD MMMM YYYY, dddd');
        $currentTime = Carbon::now()->format('H:i');

        // View'e gönderilecek data
        return view('admin.dashboard', compact(
            'totalTables', 'activeTables', 'todayOrders', 'pendingOrders',
            'todayRevenue', 'todayCustomers', 'monthlyRevenue', 'revenueGrowth',
            'averageOrderValue', 'popularProduct', 'popularProductQuantity', 'recentActivities',
            'activeUsers', 'lowStockProducts', 'completedOrders',
            'weeklyData', 'tableStatus', 'categoryStats',
            'currentDate', 'currentTime'
        ));
    }

    /**
     * Real-time dashboard stats endpoint for AJAX calls
     */
    public function dashboardStats()
    {
        $today = Carbon::today();
        
        // Temel istatistikler
        $stats = [
            'totalTables' => Table::count(),
            'activeTables' => Table::whereHas('orders', function ($query) {
                $query->whereIn('status', ['pending', 'preparing'])
                    ->whereDate('created_at', Carbon::today());
            })->count(),
            'todayOrders' => Order::whereDate('created_at', $today)->count(),
            'pendingOrders' => Order::whereIn('status', ['pending', 'preparing'])->count(),
            'todayRevenue' => Order::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'todayCustomers' => Order::whereDate('created_at', $today)
                ->distinct('table_id')
                ->count('table_id'),
            'completedOrders' => Order::whereDate('created_at', $today)
                ->where('status', 'delivered')
                ->count(),
            'lowStockProducts' => Product::where('stock_quantity', '<', 10)
                ->where('is_active', true)
                ->count(),
            'activeUsers' => User::where('last_activity', '>', Carbon::now()->subMinutes(30))
                ->count(),
            'currentTime' => Carbon::now()->format('H:i:s'),
            'lastUpdate' => Carbon::now()->format('d.m.Y H:i:s')
        ];

        // Son aktiviteler (sadece son 5 dakikadaki yeni aktiviteler)
        $recentActivities = Order::with(['table', 'orderItems.product'])
            ->where('updated_at', '>', Carbon::now()->subMinutes(5))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_name' => $order->table->name ?? 'Masa ' . $order->table_id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'time_ago' => $order->updated_at->diffForHumans(),
                    'items_count' => $order->orderItems->count(),
                    'updated_at' => $order->updated_at->toISOString()
                ];
            });

        $stats['recentActivities'] = $recentActivities;
        $stats['hasNewActivities'] = $recentActivities->count() > 0;

        return response()->json($stats);
    }

    /**
     * Get new notifications for real-time updates
     */
    public function getNotifications()
    {
        $notifications = [];
        
        // Yeni siparişler
        $newOrders = Order::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->count();
            
        if ($newOrders > 0) {
            $notifications[] = [
                'type' => 'order',
                'title' => 'Yeni Sipariş',
                'message' => "{$newOrders} yeni sipariş var",
                'time' => now()->format('H:i'),
                'icon' => 'bell'
            ];
        }
        
        // Düşük stok uyarıları - StockService kullan
        $stockService = app(\App\Services\StockService::class);
        $lowStockProducts = $stockService->checkAllLowStockProducts();
        
        if ($lowStockProducts->count() > 0) {
            $notifications[] = [
                'type' => 'stock',
                'title' => 'Düşük Stok Uyarısı',
                'message' => "{$lowStockProducts->count()} ürün düşük stokta",
                'time' => now()->format('H:i'),
                'icon' => 'exclamation-triangle'
            ];
        }
        
        // Kritik stok uyarıları
        $criticalStock = Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();
            
        if ($criticalStock > 0) {
            $notifications[] = [
                'type' => 'critical',
                'title' => 'Kritik Stok Uyarısı',
                'message' => "{$criticalStock} ürünün stoğu bitti",
                'time' => now()->format('H:i'),
                'icon' => 'x-circle'
            ];
        }
        
        return response()->json($notifications);
    }
}