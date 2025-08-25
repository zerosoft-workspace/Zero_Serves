<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite için enum değerlerini güncelle
        if (DB::getDriverName() === 'sqlite') {
            // Mevcut tabloyu yedekle
            DB::statement('CREATE TABLE tables_backup AS SELECT * FROM tables');
            
            // Eski tabloyu sil
            Schema::dropIfExists('tables');
            
            // Yeni tablo oluştur
            Schema::create('tables', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->enum('status', [
                    'empty',
                    'occupied',
                    'reserved'
                ])->default('empty');
                $table->timestamps();
            });
            
            // Verileri geri yükle ve status değerlerini dönüştür
            DB::statement("
                INSERT INTO tables (id, name, token, status, created_at, updated_at)
                SELECT 
                    id, 
                    name, 
                    token, 
                    CASE 
                        WHEN status IN ('order_pending', 'preparing', 'delivered', 'paid') THEN 'occupied'
                        ELSE 'empty'
                    END as status,
                    created_at, 
                    updated_at 
                FROM tables_backup
            ");
            
            // Yedek tabloyu sil
            DB::statement('DROP TABLE tables_backup');
        } else {
            // MySQL/PostgreSQL için
            DB::statement("ALTER TABLE tables MODIFY COLUMN status ENUM('empty', 'occupied', 'reserved') DEFAULT 'empty'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite için geri alma
            DB::statement('CREATE TABLE tables_backup AS SELECT * FROM tables');
            Schema::dropIfExists('tables');
            
            Schema::create('tables', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->enum('status', [
                    'empty',
                    'order_pending',
                    'preparing',
                    'delivered',
                    'paid'
                ])->default('empty');
                $table->timestamps();
            });
            
            DB::statement("
                INSERT INTO tables (id, name, token, status, created_at, updated_at)
                SELECT 
                    id, 
                    name, 
                    token, 
                    CASE 
                        WHEN status = 'occupied' THEN 'order_pending'
                        ELSE status
                    END as status,
                    created_at, 
                    updated_at 
                FROM tables_backup
            ");
            
            DB::statement('DROP TABLE tables_backup');
        } else {
            DB::statement("ALTER TABLE tables MODIFY COLUMN status ENUM('empty', 'order_pending', 'preparing', 'delivered', 'paid') DEFAULT 'empty'");
        }
    }
};
