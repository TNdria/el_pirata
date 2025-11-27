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
        Schema::create('tiebreaker_challenges', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hunting_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('question');
            $table->string('correct_answer');
            $table->text('hints')->nullable();
            $table->integer('time_limit_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['hunting_id', 'is_active']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiebreaker_challenges');
    }
};
