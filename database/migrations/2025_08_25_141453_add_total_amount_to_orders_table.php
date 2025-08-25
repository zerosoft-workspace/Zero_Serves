<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'total_amount') && !Schema::hasColumn('orders', 'total_price')) {
            return;
        }
        // 1) total_amount kolonu yoksa ekle
        if (!Schema::hasColumn('orders', 'total_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                // ihtiyaca göre precision/scale'i ayarla
                $table->decimal('total_amount', 12, 2)->nullable()->after('id');
            });
        }

        // 2) Eski veriyi total_price'tan total_amount'a taşı (varsa)
        if (Schema::hasColumn('orders', 'total_price')) {
            DB::statement("
                UPDATE orders
                SET total_amount = total_price
                WHERE (total_amount IS NULL OR total_amount = 0)
                  AND total_price IS NOT NULL
            ");
        }

        // 3) Null kalmasın (opsiyonel)
        DB::statement("UPDATE orders SET total_amount = 0 WHERE total_amount IS NULL");
    }

    public function down(): void
    {
        // Geri al: total_amount'ı kaldır
        if (Schema::hasColumn('orders', 'total_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('total_amount');
            });
        }
    }
};
