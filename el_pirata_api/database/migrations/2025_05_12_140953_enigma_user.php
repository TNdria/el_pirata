<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enigma_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enigma_id')->constrained()->onDelete('cascade');
            $table->timestamp('viewed_at')->nullable();      // temps de vue
            $table->timestamp('completed_at')->nullable();
            $table->string('unique_code')->nullable();
            $table->boolean(column: 'is_used')->default(false);
            $table->integer('attempts')->default(0);   // temps fini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enigma_user');
    }
};
