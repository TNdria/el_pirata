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
        Schema::create('tiebreaker_participations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tiebreaker_challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->integer('response_time_seconds')->nullable();
            $table->integer('rank')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['tiebreaker_challenge_id', 'user_id']);
            $table->index(['is_correct', 'answered_at']);
            $table->index('rank');
            $table->unique(['tiebreaker_challenge_id', 'user_id']); // Un seul essai par utilisateur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiebreaker_participations');
    }
};
