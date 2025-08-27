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
        $yesterday = $today->copy()->subDay();
        $startOfMonth = Carbon::now()->startOfMonth();

        // === OPTİMİZE EDİLMİŞ TEK QUERY İLE TEMEL İSTATİSTİKLER ===
        
        // Tüm order verilerini tek sorguda çek
        $orderStats = DB::table('orders')
            ->select([
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as today_orders'),
                DB::raw('COUNT(CASE WHEN status IN ("pending", "preparing") THEN 1 END) as pending_orders'),
                DB::raw('COUNT(CASE WHEN DATE(created_at) = ? AND status = "delivered" THEN 1 END) as completed_orders'),
                DB::raw('SUM(CASE WHEN DATE(created_at) = ? AND status = "paid" THEN total_amount ELSE 0 END) as today_revenue'),
                DB::raw('SUM(CASE WHEN DATE(created_at) = ? AND status = "paid" THEN total_amount ELSE 0 END) as yesterday_revenue'),
                DB::raw('SUM(CASE WHEN created_at >= ? AND status = "paid" THEN total_amount ELSE 0 END) as monthly_revenue'),
                DB::raw('COUNT(DISTINCT CASE WHEN DATE(created_at) = ? THEN table_id END) as today_customers')
            ])
            ->setBindings([$today, $today, $today, $yesterday, $startOfMonth, $today])
            ->first();

        // Masa istatistikleri - optimize edilmiş
        $tableStats = DB::table('tables as t')
            ->leftJoin('orders as o', function($join) use ($today) {
                $join->on('t.id', '=', 'o.table_id')
                     ->whereIn('o.status', ['pending', 'preparing'])
                     ->whereDate('o.created_at', $today);
            })
            ->select([
                DB::raw('COUNT(t.id) as total_tables'),
                DB::raw('COUNT(o.id) as active_tables'),
                DB::raw('COUNT(CASE WHEN t.status = "reserved" THEN 1 END) as reserved_tables')
            ])
            ->first();

        // Hesaplanan değerler
        $revenueGrowth = $orderStats->yesterday_revenue > 0
            ? round((($orderStats->today_revenue - $orderStats->yesterday_revenue) / $orderStats->yesterday_revenue) * 100, 1)
            : 0;

        $averageOrderValue = $orderStats->today_orders > 0
            ? round($orderStats->today_revenue / $orderStats->today_orders, 2)
            : 0;

        // === EN POPÜLER ÜRÜN - OPTİMİZE EDİLMİŞ ===
        $popularProductData = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->whereDate('o.created_at', $today)
            ->select('p.name', DB::raw('SUM(oi.quantity) as total_quantity'))
            ->groupBy('p.id', 'p.name')
            ->orderBy('total_quantity', 'desc')
            ->first();

        $popularProduct = $popularProductData ? $popularProductData->name : 'Veri yok';
        $popularProductQuantity = $popularProductData ? $popularProductData->total_quantity : 0;

        // === HAFTALIK VERİLER - TEK QUERY ===
        $weeklyData = DB::table('orders')
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as revenue')
            ])
            ->whereBetween('created_at', [$today->copy()->subDays(6), $today->copy()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Haftalık veri formatla
        $formattedWeeklyData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayData = $weeklyData->get($dateKey);
            
            $formattedWeeklyData->push([
                'date' => $date->format('d.m'),
                'day_name' => $date->isoFormat('dddd'),
                'revenue' => $dayData ? $dayData->revenue : 0,
                'orders' => $dayData ? $dayData->orders : 0
            ]);
        }

        // === SON AKTİVİTELER - EAGER LOADING ===
        $recentActivities = Order::with(['table:id,name', 'orderItems:order_id,product_id'])
            ->whereDate('created_at', $today)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'table_id', 'status', 'total_amount', 'created_at', 'updated_at'])
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

        // === SİSTEM DURUMU - TEK QUERY ===
        // last_activity kolonu yoksa geçici olarak tüm kullanıcıları say
        $activeUsers = DB::table('users')->count();
        $lowStockProducts = DB::table('products')
            ->where('stock_quantity', '<', 10)
            ->where('is_active', 1)
            ->count();

        // === SİPARİŞ DURUMU DAĞILIMI ===
        $orderStatusDistribution = DB::table('orders')
            ->whereDate('created_at', $today)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // === KATEGORİ BAZINDA SATIŞ ===
        $categoryStats = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->whereDate('o.created_at', $today)
            ->where('o.status', 'paid')
            ->select(
                'c.name as category_name',
                DB::raw('SUM(oi.quantity) as total_quantity'),
                DB::raw('SUM(oi.quantity * oi.price) as total_revenue')
            )
            ->groupBy('c.id', 'c.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Zaman verileri
        $currentDate = Carbon::now()->isoFormat('DD MMMM YYYY, dddd');
        $currentTime = Carbon::now()->format('H:i');

        // View'e gönderilecek data
        return view('admin.dashboard', compact(
            'orderStats', 'tableStats', 'revenueGrowth',
            'averageOrderValue', 'popularProduct', 'popularProductQuantity', 'recentActivities',
            'activeUsers', 'lowStockProducts', 'formattedWeeklyData', 'categoryStats',
            'orderStatusDistribution', 'currentDate', 'currentTime'
        ))->with([
            'totalTables' => $tableStats->total_tables,
            'activeTables' => $tableStats->active_tables,
            'todayOrders' => $orderStats->today_orders,
            'pendingOrders' => $orderStats->pending_orders,
            'todayRevenue' => $orderStats->today_revenue,
            'todayCustomers' => $orderStats->today_customers,
            'monthlyRevenue' => $orderStats->monthly_revenue,
            'completedOrders' => $orderStats->completed_orders,
            'weeklyData' => $formattedWeeklyData,
            'tableStatus' => [
                'empty' => $tableStats->total_tables - $tableStats->active_tables - $tableStats->reserved_tables,
                'occupied' => $tableStats->active_tables,
                'reserved' => $tableStats->reserved_tables
            ]
        ]);
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
            'activeUsers' => DB::table('users')->count(),
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