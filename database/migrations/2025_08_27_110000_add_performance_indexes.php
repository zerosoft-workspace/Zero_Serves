<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin panel performansı için kritik indexler
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Dashboard ve sipariş yönetimi için kritik indexler
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
            $table->index(['table_id', 'status'], 'idx_orders_table_status');
            $table->index(['created_at', 'status'], 'idx_orders_created_status');
            $table->index('updated_at', 'idx_orders_updated');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Sipariş detayları ve istatistikler için
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
            $table->index('product_id', 'idx_order_items_product');
        });

        Schema::table('products', function (Blueprint $table) {
            // Ürün listesi ve stok kontrolü için
            $table->index(['category_id', 'is_active'], 'idx_products_category_active');
            $table->index(['is_active', 'stock_quantity'], 'idx_products_active_stock');
            $table->index('stock_quantity', 'idx_products_stock');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Kategori listesi için
            $table->index('name', 'idx_categories_name');
        });

        Schema::table('tables', function (Blueprint $table) {
            // Masa yönetimi için
            $table->index(['is_active', 'status'], 'idx_tables_active_status');
            $table->index('name', 'idx_tables_name');
        });

        Schema::table('users', function (Blueprint $table) {
            // Aktif kullanıcı kontrolü için
            $table->index('last_activity', 'idx_users_last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status_created');
            $table->dropIndex('idx_orders_table_status');
            $table->dropIndex('idx_orders_created_status');
            $table->dropIndex('idx_orders_updated');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_product');
            $table->dropIndex('idx_order_items_product');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_active');
            $table->dropIndex('idx_products_active_stock');
            $table->dropIndex('idx_products_stock');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_name');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropIndex('idx_tables_active_status');
            $table->dropIndex('idx_tables_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_last_activity');
        });
    }
};
