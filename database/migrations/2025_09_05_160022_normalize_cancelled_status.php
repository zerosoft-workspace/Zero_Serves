<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // 1) Mevcut değerleri normalize et (orders)
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'status')) {
            DB::table('orders')->where('status', 'canceled')->update(['status' => 'cancelled']);
        }

        // 2) order_items sadece "status" kolonu VARSA normalize et (sizde yoksa bu blok atlanır)
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'status')) {
            DB::table('order_items')->where('status', 'canceled')->update(['status' => 'cancelled']);
        }

        // 3) SQLite: orders tablosundaki CHECK'i 'cancelled' olacak şekilde yeniden oluştur
        if ($driver === 'sqlite') {
            $this->sqliteRewriteStatusCheck(
                table: 'orders',
                allowed: ['pending', 'preparing', 'delivered', 'paid', 'cancelled', 'refunded'] // yeni liste
            );

            // order_items için STATUS kolonu yoksa hiç dokunma; varsa CHECK'i de güncelle
            if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'status')) {
                $this->sqliteRewriteStatusCheck(
                    table: 'order_items',
                    allowed: ['pending', 'preparing', 'delivered', 'paid', 'cancelled']
                );
            }
        }
        // MySQL/PGSQL kullanıyorsanız, burada ENUM/CHECK değiştirme komutları eklenebilir.
        // (Sizde SQLite olduğu için gerek yok.)
    }

    public function down(): void
    {
        // Geri alma: cancelled -> canceled (opsiyonel)
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'status')) {
            DB::table('orders')->where('status', 'cancelled')->update(['status' => 'canceled']);
        }
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'status')) {
            DB::table('order_items')->where('status', 'cancelled')->update(['status' => 'canceled']);
        }
    }

    /**
     * SQLite'ta bir tablonun CREATE TABLE SQL'ini okuyup,
     * status CHECK listesini verilen allowed listeyle değiştirerek tabloyu güvenle yeniden oluşturur.
     */
    private function sqliteRewriteStatusCheck(string $table, array $allowed): void
    {
        // Mevcut CREATE TABLE SQL'ini al
        $createSql = DB::table('sqlite_master')
            ->where('type', 'table')
            ->where('name', $table)
            ->value('sql');

        if (!$createSql) {
            return;
        }

        // status CHECK(...) kısmını yeni liste ile değiştir
        $pattern = "/CHECK\\s*\\(\\s*status\\s+IN\\s*\\(([^)]*)\\)\\s*\\)/i";
        $replacement = "CHECK (status IN ('" . implode("','", $allowed) . "'))";
        $newSql = preg_replace($pattern, $replacement, $createSql);

        // Eğer CHECK bulunamadıysa (bazı şemalarda olmayabilir), dokunmadan çık
        if (!$newSql) {
            return;
        }

        // Yeni tablo adı
        $newTable = $table . '_new';

        // CREATE TABLE satırındaki tablo adını _new ile değiştir (çift/tek tırnaklı varyantları da kapsa)
        $newSql = str_replace("CREATE TABLE \"$table\"", "CREATE TABLE \"$newTable\"", $newSql);
        $newSql = str_replace("CREATE TABLE '$table'", "CREATE TABLE '$newTable'", $newSql);
        $newSql = str_replace("CREATE TABLE $table", "CREATE TABLE $newTable", $newSql);

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement($newSql);

        // Ortak kolon listesini çıkar ve veriyi taşı
        $cols = collect(DB::select("PRAGMA table_info('$table')"))->pluck('name')->implode(',');
        DB::statement("INSERT INTO \"$newTable\" ($cols) SELECT $cols FROM \"$table\"");

        Schema::drop($table);
        Schema::rename($newTable, $table);
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
