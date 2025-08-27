<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Table;
use App\Models\Order;
use App\Models\WaiterCall;

class DebugController extends Controller
{
    /**
     * Cache durumunu kontrol et
     */
    public function cacheStatus()
    {
        $cacheInfo = [
            'driver' => config('cache.default'),
            'store_type' => get_class(Cache::getStore()),
        ];
        
        // Waiter cache key'lerini kontrol et
        $waiterCacheKeys = [];
        
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $waiterCacheKeys = $redis->keys('*waiter*');
            }
        } catch (\Exception $e) {
            $waiterCacheKeys = ['Redis kullanılamıyor: ' . $e->getMessage()];
        }
        
        // Aktif siparişleri kontrol et
        $activeOrders = Order::with(['table:id,name'])
            ->whereNotIn('status', ['paid', 'canceled'])
            ->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Aktif çağrıları kontrol et
        $activeCalls = WaiterCall::with(['table:id,name'])
            ->where('status', 'new')
            ->select(['id', 'table_id', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Masa durumları
        $tables = Table::with(['active_order' => function($q) {
                $q->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
                  ->whereNotIn('status', ['paid', 'canceled']);
            }])
            ->select(['id', 'name', 'status'])
            ->get();
        
        return response()->json([
            'cache_info' => $cacheInfo,
            'waiter_cache_keys' => $waiterCacheKeys,
            'active_orders_count' => $activeOrders->count(),
            'active_orders' => $activeOrders,
            'active_calls_count' => $activeCalls->count(),
            'active_calls' => $activeCalls,
            'tables_with_orders' => $tables->filter(function($table) {
                return $table->active_order !== null;
            })->values(),
            'empty_tables' => $tables->filter(function($table) {
                return $table->active_order === null;
            })->values(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Cache'i manuel olarak temizle
     */
    public function clearCache(Request $request)
    {
        $type = $request->get('type', 'all');
        
        try {
            switch ($type) {
                case 'waiter':
                    $this->clearWaiterCache();
                    $message = 'Garson paneli cache\'i temizlendi';
                    break;
                    
                case 'all':
                    Cache::flush();
                    $message = 'Tüm cache temizlendi';
                    break;
                    
                default:
                    $message = 'Geçersiz cache tipi';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme hatası: ' . $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }
    
    /**
     * Garson cache'ini temizle
     */
    private function clearWaiterCache(): void
    {
        try {
            // Bilinen cache key'leri temizle
            $patterns = [
                'waiter_dashboard_',
                'waiter_table_',
                'waiter_calls_'
            ];
            
            foreach ($patterns as $pattern) {
                // Manuel olarak bilinen key'leri temizle
                for ($i = 1; $i <= 50; $i++) {
                    Cache::forget($pattern . $i);
                    Cache::forget($pattern . 'page_' . $i);
                }
                
                // MD5 hash'li key'leri temizle
                $hashKeys = [
                    md5('_'),
                    md5('pending_'),
                    md5('preparing_'),
                    md5('delivered_'),
                    md5('paid_'),
                ];
                
                foreach ($hashKeys as $hash) {
                    Cache::forget($pattern . $hash);
                }
            }
            
            // Redis kullanılıyorsa pattern matching ile temizle
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys('*waiter*');
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('Debug cache temizleme hatası: ' . $e->getMessage());
        }
    }
}
