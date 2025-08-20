<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) SQLite kısıtından dolayı önce nullable ekleyelim
        Schema::table('tables', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->after('name');
        });

        // 2) Mevcut kayıtlara token yazalım
        $rows = DB::table('tables')->whereNull('token')->get(['id']);
        foreach ($rows as $row) {
            DB::table('tables')
                ->where('id', $row->id)
                ->update(['token' => bin2hex(random_bytes(16))]);
        }

        // 3) Unique index ekleyelim
        Schema::table('tables', function (Blueprint $table) {
            $table->unique('token');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn('token');
        });
    }
};
