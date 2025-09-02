<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('people');
            $table->text('admin_note')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('admin_note');
            $table->unsignedBigInteger('status_updated_by')->nullable()->after('status_updated_at');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_note', 'status_updated_at', 'status_updated_by']);
        });
    }
};
