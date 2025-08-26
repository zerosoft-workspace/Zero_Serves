<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('waiter_calls', function (Blueprint $table) {
            // Update status enum to include new statuses
            $table->dropColumn('status');
        });
        
        Schema::table('waiter_calls', function (Blueprint $table) {
            $table->enum('status', ['new', 'responded', 'completed'])->default('new');
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('waiter_calls', function (Blueprint $table) {
            $table->dropForeign(['responded_by']);
            $table->dropColumn(['responded_at', 'completed_at', 'responded_by']);
            $table->dropColumn('status');
        });
        
        Schema::table('waiter_calls', function (Blueprint $table) {
            $table->enum('status', ['new', 'done'])->default('new');
        });
    }
};
