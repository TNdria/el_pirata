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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_at')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('block_reason')->nullable();
            $table->timestamp('unblocked_at')->nullable();
            $table->foreignId('unblocked_by')->nullable()->constrained('admins')->onDelete('set null');
            
            $table->index(['is_blocked', 'blocked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropForeign(['unblocked_by']);
            $table->dropColumn([
                'is_blocked',
                'blocked_at',
                'blocked_by',
                'block_reason',
                'unblocked_at',
                'unblocked_by'
            ]);
        });
    }
};

