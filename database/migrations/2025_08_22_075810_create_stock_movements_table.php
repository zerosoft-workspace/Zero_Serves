<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // İlgili ürün
            $table->unsignedBigInteger('product_id');

            // Artış/Azalış türü
            $table->enum('type', ['increase', 'decrease']);

            // Miktar (negatif değer istemiyoruz)
            $table->unsignedInteger('qty');

            // Neden (sipariş, iade, manuel düzeltme vs.)
            $table->enum('reason', ['order', 'refund', 'manual', 'adjustment'])->default('order');

            // Referans kayıt (opsiyonel) → esneklik için tip + id
            $table->string('ref_type')->nullable(); // örn: 'order', 'order_item'
            $table->unsignedBigInteger('ref_id')->nullable();

            // İşlemi yapan kullanıcı (opsiyonel)
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // İndeksler
            $table->index(['product_id']);
            $table->index(['ref_type', 'ref_id']);
            $table->index(['created_by']);
            $table->index(['type']);
            $table->index(['reason']);

            // Yabancı anahtarlar
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->restrictOnDelete();

            // Kullanıcı silinirse iz kaydı null olsun (log korunur)
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['created_by']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['ref_type', 'ref_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['type']);
            $table->dropIndex(['reason']);
        });

        Schema::dropIfExists('stock_movements');
    }
};
