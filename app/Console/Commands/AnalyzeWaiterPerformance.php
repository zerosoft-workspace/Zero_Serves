<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Table;
use App\Models\Order;
use App\Models\WaiterCall;

class AnalyzeWaiterPerformance extends Command
{
    protected $signature = 'waiter:analyze-performance';
    protected $description = 'Garson paneli performans analizi';

    public function handle()
    {
        $this->info('🔍 Garson Paneli Performans Analizi Başlatılıyor...');
        $this->newLine();

        // Query sayısını ölçmek için
        DB::enableQueryLog();
        $startTime = microtime(true);

        // Dashboard verilerini test et
        $this->testDashboardPerformance();
        
        // Masa detay sayfasını test et
        $this->testTableDetailPerformance();
        
        // Çağrılar sayfasını test et
        $this->testCallsPerformance();

        $endTime = microtime(true);
        $totalTime = round(($endTime - $startTime) * 1000, 2);
        $queries = DB::getQueryLog();
        
        $this->newLine();
        $this->info("📊 GENEL PERFORMANS RAPORU");
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['Toplam Süre', $totalTime . ' ms'],
                ['Toplam Query Sayısı', count($queries)],
                ['Ortalama Query Süresi', count($queries) > 0 ? round($totalTime / count($queries), 2) . ' ms' : '0 ms'],
            ]
        );

        // Cache durumu
        $this->analyzeCacheStatus();
        
        // Database istatistikleri
        $this->analyzeDatabaseStats();

        $this->newLine();
        $this->info('✅ Performans analizi tamamlandı!');
    }

    private function testDashboardPerformance()
    {
        $this->info('📋 Dashboard Performansı Test Ediliyor...');
        
        DB::flushQueryLog();
        $startTime = microtime(true);

        // Dashboard verilerini çek
        $tables = Table::query()
            ->select(['id', 'name', 'status', 'capacity'])
            ->with(['active_order' => function($q) {
                $q->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
                  ->whereNotIn('status', ['paid', 'canceled']);
            }])
            ->get();

        $activeCalls = WaiterCall::query()
            ->select(['id', 'table_id', 'status', 'created_at'])
            ->with(['table:id,name'])
            ->where('status', 'new')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $endTime = microtime(true);
        $queries = DB::getQueryLog();
        
        $this->table(
            ['Dashboard Metrik', 'Değer'],
            [
                ['Masa Sayısı', $tables->count()],
                ['Aktif Çağrı Sayısı', $activeCalls->count()],
                ['Query Sayısı', count($queries)],
                ['Süre', round(($endTime - $startTime) * 1000, 2) . ' ms'],
            ]
        );
    }

    private function testTableDetailPerformance()
    {
        $this->info('🍽️ Masa Detay Performansı Test Ediliyor...');
        
        // İlk masayı al
        $firstTable = Table::first();
        if (!$firstTable) {
            $this->warn('Test için masa bulunamadı.');
            return;
        }

        DB::flushQueryLog();
        $startTime = microtime(true);

        $table = Table::select(['id', 'name', 'status', 'capacity'])
            ->findOrFail($firstTable->id);

        $currentOrder = Order::query()
            ->select(['id', 'table_id', 'status', 'total_amount', 'created_at', 'updated_at'])
            ->where('table_id', $table->id)
            ->whereNotIn('status', ['paid', 'canceled'])
            ->latest('id')
            ->with([
                'items' => function($q) {
                    $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                },
                'items.product:id,name,price'
            ])
            ->first();

        $pastOrders = Order::query()
            ->select(['id', 'table_id', 'status', 'total_amount', 'created_at'])
            ->where('table_id', $table->id)
            ->where('status', 'paid')
            ->with([
                'items' => function($q) {
                    $q->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'line_total']);
                },
                'items.product:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $endTime = microtime(true);
        $queries = DB::getQueryLog();
        
        $this->table(
            ['Masa Detay Metrik', 'Değer'],
            [
                ['Test Masa', $table->name],
                ['Mevcut Sipariş', $currentOrder ? "#{$currentOrder->id}" : 'Yok'],
                ['Geçmiş Sipariş Sayısı', $pastOrders->count()],
                ['Query Sayısı', count($queries)],
                ['Süre', round(($endTime - $startTime) * 1000, 2) . ' ms'],
            ]
        );
    }

    private function testCallsPerformance()
    {
        $this->info('📞 Çağrılar Performansı Test Ediliyor...');
        
        DB::flushQueryLog();
        $startTime = microtime(true);

        $calls = WaiterCall::query()
            ->select(['id', 'table_id', 'status', 'message', 'created_at', 'responded_at', 'completed_at', 'responded_by'])
            ->with(['table:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $endTime = microtime(true);
        $queries = DB::getQueryLog();
        
        $this->table(
            ['Çağrılar Metrik', 'Değer'],
            [
                ['Toplam Çağrı', $calls->count()],
                ['Query Sayısı', count($queries)],
                ['Süre', round(($endTime - $startTime) * 1000, 2) . ' ms'],
            ]
        );
    }

    private function analyzeCacheStatus()
    {
        $this->info('💾 Cache Durumu Analizi...');
        
        $cacheKeys = [
            'waiter_dashboard_' . md5('_'),
            'waiter_table_1',
            'waiter_calls_page_1'
        ];
        
        $cacheData = [];
        foreach ($cacheKeys as $key) {
            $exists = Cache::has($key);
            $cacheData[] = [$key, $exists ? '✅ Var' : '❌ Yok'];
        }
        
        $this->table(['Cache Anahtarı', 'Durum'], $cacheData);
    }

    private function analyzeDatabaseStats()
    {
        $this->info('📈 Veritabanı İstatistikleri...');
        
        $stats = [
            ['Toplam Masa', Table::count()],
            ['Aktif Sipariş', Order::whereNotIn('status', ['paid', 'canceled'])->count()],
            ['Bugünkü Sipariş', Order::whereDate('created_at', today())->count()],
            ['Aktif Çağrı', WaiterCall::where('status', 'new')->count()],
            ['Toplam Çağrı', WaiterCall::count()],
        ];
        
        $this->table(['İstatistik', 'Değer'], $stats);
    }
}
