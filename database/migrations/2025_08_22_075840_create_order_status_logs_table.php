<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');

            // Nereden nereye (from nullable olabilir; ilk kayıt için)
            $table->string('from_status')->nullable();

            // Temel akış: pending → preparing → delivered → paid
            // (ileride canceled/refunded eklenebilir)
            $table->string('to_status');

            // Değiştiren kullanıcı (opsiyonel)
            $table->unsignedBigInteger('changed_by')->nullable();

            $table->timestamps();

            // İndeksler
            $table->index(['order_id']);
            $table->index(['to_status']);
            $table->index(['changed_by']);

            // Yabancı anahtarlar
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->cascadeOnDelete();

            $table->foreign('changed_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['changed_by']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['to_status']);
            $table->dropIndex(['changed_by']);
        });

        Schema::dropIfExists('order_status_logs');
    }
};
