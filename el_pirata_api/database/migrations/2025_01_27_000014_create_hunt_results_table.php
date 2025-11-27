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
        Schema::create('hunt_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hunting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rank');
            $table->integer('completed_enigmas');
            $table->integer('total_enigmas');
            $table->decimal('completion_percentage', 5, 2);
            $table->timestamp('first_enigma_completed_at')->nullable();
            $table->timestamp('last_enigma_completed_at')->nullable();
            $table->integer('total_time_seconds')->nullable();
            $table->decimal('prize_amount', 10, 2)->nullable();
            $table->enum('prize_status', ['pending', 'awarded', 'paid'])->default('pending');
            $table->timestamp('prize_awarded_at')->nullable();
            $table->foreignId('awarded_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['hunting_id', 'rank']);
            $table->index(['user_id', 'hunting_id']);
            $table->index(['prize_status', 'prize_awarded_at']);
            $table->unique(['hunting_id', 'user_id']); // Un seul résultat par utilisateur par chasse
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunt_results');
    }
};
