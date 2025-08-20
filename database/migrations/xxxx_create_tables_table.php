<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Masa 1, Masa 2 gibi
            $table->string('qr_code_path')->nullable(); // QR kod görsel yolu
            $table->enum('status', ['empty', 'occupied', 'order_pending'])
                ->default('empty'); // Masa durumu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
