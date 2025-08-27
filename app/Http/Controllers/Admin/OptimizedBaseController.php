<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin panel için optimize edilmiş base controller
 * Query caching ve performans optimizasyonları içerir
 */
abstract class OptimizedBaseController extends Controller
{
    /**
     * Cache süreleri (dakika)
     */
    protected const CACHE_SHORT = 5;    // 5 dakika - sık değişen veriler
    protected const CACHE_MEDIUM = 30;  // 30 dakika - orta sıklıkta değişen veriler
    protected const CACHE_LONG = 120;   // 2 saat - az değişen veriler

    /**
     * Cache'li query çalıştırma
     */
    protected function cacheQuery(string $key, callable $query, int $minutes = self::CACHE_SHORT)
    {
        return Cache::remember($key, $minutes * 60, $query);
    }

    /**
     * Admin dashboard için optimize edilmiş temel istatistikler
     */
    protected function getBasicStats()
    {
        return $this->cacheQuery('admin.basic_stats', function() {
            return DB::table('orders')
                ->select([
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_orders'),
                    DB::raw('COUNT(CASE WHEN status = "preparing" THEN 1 END) as preparing_orders'),
                    DB::raw('SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as total_revenue')
                ])
                ->first();
        }, self::CACHE_SHORT);
    }

    /**
     * Aktif masa sayısını cache'li olarak getir
     */
    protected function getActiveTablesCount()
    {
        return $this->cacheQuery('admin.active_tables', function() {
            return DB::table('tables as t')
                ->leftJoin('orders as o', function($join) {
                    $join->on('t.id', '=', 'o.table_id')
                         ->whereIn('o.status', ['pending', 'preparing'])
                         ->whereDate('o.created_at', today());
                })
                ->count('o.id');
        }, self::CACHE_SHORT);
    }

    /**
     * Düşük stoklu ürün sayısını cache'li olarak getir
     */
    protected function getLowStockCount()
    {
        return $this->cacheQuery('admin.low_stock_count', function() {
            return DB::table('products')
                ->whereRaw('stock_quantity <= min_stock_level')
                ->where('is_active', true)
                ->count();
        }, self::CACHE_MEDIUM);
    }

    /**
     * Cache temizleme metodları
     */
    protected function clearOrderCache()
    {
        Cache::forget('admin.basic_stats');
        Cache::forget('admin.active_tables');
    }

    protected function clearProductCache()
    {
        Cache::forget('admin.low_stock_count');
    }

    /**
     * Bulk cache temizleme
     */
    protected function clearAllAdminCache()
    {
        $keys = [
            'admin.basic_stats',
            'admin.active_tables', 
            'admin.low_stock_count',
            'admin.categories_with_count',
            'admin.products_list'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
