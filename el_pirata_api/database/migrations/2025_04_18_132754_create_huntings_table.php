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
        Schema::create('huntings', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->enum('type', ['digital', 'physic']);
            $table->text('category')->nullable();
            $table->date('start_date');
            $table->string('level');
            $table->float(column: 'prize_win');
            // $table->string(column: 'price');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huntings');
    }
};
