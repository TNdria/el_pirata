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
        Schema::create('terrain_validations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('terrain_code_id')->constrained('terrain_validation_codes')->onDelete('cascade');
            $table->decimal('user_latitude', 10, 8);
            $table->decimal('user_longitude', 11, 8);
            $table->integer('distance_meters'); // Distance calculée en mètres
            $table->boolean('is_valid')->default(false);
            $table->timestamp('validated_at');
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'terrain_code_id']);
            $table->index(['is_valid', 'validated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terrain_validations');
    }
};
