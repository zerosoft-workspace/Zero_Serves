<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Stok takibi ürün bazında aç/kapat
            $table->boolean('is_stock_tracked')
                ->default(true)
                ->after('low_stock_threshold');

            // İyileştirme: Sık aranan sütunlara index (opsiyonel ama faydalı)
            if (!Schema::hasColumn('products', 'stock')) {
                // Not: 'stock' zaten var diye bekliyoruz; yoksa burada oluşturmayacağız.
                // Sadece bilgi amaçlı bırakıldı.
            }
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_stock_tracked');
        });
    }
};
