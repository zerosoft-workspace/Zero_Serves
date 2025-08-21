<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Masa adı (ör: Masa 1)
            $table->string('token', 64)->unique(); // QR için benzersiz token
            $table->enum('status', [
                'empty',
                'order_pending',
                'preparing',
                'delivered',
                'paid'
            ])->default('empty'); // Masa/Sipariş durumu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
