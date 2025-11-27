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
        Schema::create('enigmas', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // titre
            $table->text('text_content'); // contenu_texte
            $table->text('media')->nullable(); // contenu_illustration
            $table->text('media_type')->nullable(); // contenu_illustration
            $table->integer('order')->default(1); // ordre
            $table->foreignId('hunting_id')->nullable()->constrained('huntings')->nullOnDelete();
            $table->string('response');
            $table->string('level'); // prerequis_enigme_id
            $table->boolean('is_active')->default(true); // active
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enigmas');
    }
};
