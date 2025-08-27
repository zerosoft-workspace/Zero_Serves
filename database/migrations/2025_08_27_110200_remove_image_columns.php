<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove image column from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        // Remove image column from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back image column to products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        // Add back image column to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
        });
    }
};
