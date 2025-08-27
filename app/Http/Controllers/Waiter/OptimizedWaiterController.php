<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class OptimizedWaiterController extends Controller
{
    /**
     * Cache süreleri (dakika)
     */
    protected const CACHE_SHORT = 2;    // 2 dakika - sık değişen veriler
    protected const CACHE_MEDIUM = 10;  // 10 dakika - orta sıklıkta değişen veriler
    protected const CACHE_LONG = 30;    // 30 dakika - az değişen veriler

    /**
     * Cache anahtarı ön ekleri
     */
    protected const CACHE_PREFIX_TABLES = 'waiter_tables_';
    protected const CACHE_PREFIX_CALLS = 'waiter_calls_';
    protected const CACHE_PREFIX_ORDERS = 'waiter_orders_';

    /**
     * Cache'li sorgu çalıştırma
     */
    protected function cacheQuery(string $key, callable $callback, int $minutes = self::CACHE_SHORT)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Masa listesi cache'ini temizle
     */
    protected function clearTablesCache(): void
    {
        Cache::forget(self::CACHE_PREFIX_TABLES . 'dashboard');
        Cache::forget(self::CACHE_PREFIX_TABLES . 'list');
    }

    /**
     * Çağrı cache'ini temizle
     */
    protected function clearCallsCache(): void
    {
        Cache::forget(self::CACHE_PREFIX_CALLS . 'active');
        Cache::forget(self::CACHE_PREFIX_CALLS . 'list');
    }

    /**
     * Sipariş cache'ini temizle
     */
    protected function clearOrderCache(int $tableId): void
    {
        Cache::forget(self::CACHE_PREFIX_ORDERS . "table_{$tableId}");
        Cache::forget(self::CACHE_PREFIX_ORDERS . "current_{$tableId}");
        Cache::forget(self::CACHE_PREFIX_ORDERS . "past_{$tableId}");
        
        // Masa listesi cache'ini de temizle çünkü sipariş durumu değişti
        $this->clearTablesCache();
    }

    /**
     * Tüm waiter cache'ini temizle
     */
    protected function clearAllWaiterCache(): void
    {
        $this->clearTablesCache();
        $this->clearCallsCache();
        
        // Order cache'leri için pattern kullan
        $keys = Cache::getRedis()->keys(self::CACHE_PREFIX_ORDERS . '*');
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }
}
