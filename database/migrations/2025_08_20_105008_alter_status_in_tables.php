<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', [
                'empty',
                'order_pending',
                'preparing',
                'delivered',
                'paid'
            ])->default('empty');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['empty', 'occupied', 'order_pending'])->default('empty');
        });
    }
};
