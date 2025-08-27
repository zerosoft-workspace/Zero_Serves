<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\OrderManagementController;

class AnalyzePerformance extends Command
{
    protected $signature = 'admin:analyze-performance';
    protected $description = 'Admin panel performans analizi';

    public function handle()
    {
        $this->info('🚀 Admin Panel Performans Analizi Başlatılıyor...');
        $this->newLine();

        // Query sayacını sıfırla
        DB::enableQueryLog();

        $this->info('📊 Dashboard Performansı Test Ediliyor...');
        $startTime = microtime(true);
        
        // Dashboard controller test
        $adminController = new AdminController();
        $dashboardResponse = $adminController->dashboard();
        
        $dashboardTime = round((microtime(true) - $startTime) * 1000, 2);
        $dashboardQueries = count(DB::getQueryLog());
        
        $this->line("   ⏱️  Süre: {$dashboardTime}ms");
        $this->line("   🔍 Query Sayısı: {$dashboardQueries}");
        
        // Query log'u temizle
        DB::flushQueryLog();
        
        $this->newLine();
        $this->info('📦 Kategori Listesi Performansı...');
        $startTime = microtime(true);
        
        // Category controller test
        $categoryController = new CategoryController();
        $categoryResponse = $categoryController->index();
        
        $categoryTime = round((microtime(true) - $startTime) * 1000, 2);
        $categoryQueries = count(DB::getQueryLog());
        
        $this->line("   ⏱️  Süre: {$categoryTime}ms");
        $this->line("   🔍 Query Sayısı: {$categoryQueries}");
        
        DB::flushQueryLog();
        
        $this->newLine();
        $this->info('🛍️ Ürün Listesi Performansı...');
        $startTime = microtime(true);
        
        // Product controller test
        $productController = new ProductController();
        $productResponse = $productController->index();
        
        $productTime = round((microtime(true) - $startTime) * 1000, 2);
        $productQueries = count(DB::getQueryLog());
        
        $this->line("   ⏱️  Süre: {$productTime}ms");
        $this->line("   🔍 Query Sayısı: {$productQueries}");
        
        $this->newLine();
        $this->info('💾 Cache Durumu...');
        
        // Cache test
        $cacheKeys = [
            'admin.basic_stats',
            'admin.active_tables',
            'admin.low_stock_count'
        ];
        
        foreach ($cacheKeys as $key) {
            $cached = Cache::has($key) ? '✅ Var' : '❌ Yok';
            $this->line("   📋 {$key}: {$cached}");
        }
        
        $this->newLine();
        $this->info('📈 Veritabanı İstatistikleri...');
        
        // DB stats
        $tableStats = [
            'orders' => DB::table('orders')->count(),
            'order_items' => DB::table('order_items')->count(),
            'products' => DB::table('products')->count(),
            'categories' => DB::table('categories')->count(),
            'tables' => DB::table('tables')->count()
        ];
        
        foreach ($tableStats as $table => $count) {
            $this->line("   📊 {$table}: " . number_format($count) . " kayıt");
        }
        
        $this->newLine();
        $this->info('🎯 Performans Özeti:');
        $this->table(
            ['Sayfa', 'Süre (ms)', 'Query Sayısı', 'Durum'],
            [
                ['Dashboard', $dashboardTime, $dashboardQueries, $dashboardTime < 500 ? '✅ İyi' : '⚠️ Yavaş'],
                ['Kategoriler', $categoryTime, $categoryQueries, $categoryTime < 200 ? '✅ İyi' : '⚠️ Yavaş'],
                ['Ürünler', $productTime, $productQueries, $productTime < 300 ? '✅ İyi' : '⚠️ Yavaş']
            ]
        );
        
        $this->newLine();
        
        // Öneriler
        $this->info('💡 Optimizasyon Önerileri:');
        
        if ($dashboardQueries > 10) {
            $this->warn('   ⚠️  Dashboard\'da çok fazla query var, cache kullanımını artırın');
        }
        
        if ($dashboardTime > 500) {
            $this->warn('   ⚠️  Dashboard yavaş, veritabanı indexlerini kontrol edin');
        }
        
        if (!Cache::has('admin.basic_stats')) {
            $this->warn('   ⚠️  Temel istatistikler cache\'lenmemiş');
        }
        
        $this->info('✅ Performans analizi tamamlandı!');
        
        return 0;
    }
}
