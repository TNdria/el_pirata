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
        Schema::table('huntings', function (Blueprint $table) {
            //
            $table->string('title')->after('id'); // tu peux changer la position si besoin
            $table->string('limit_place')->nullable()->after('title');
        });
    }


    public function down(): void
    {
        Schema::table('huntings', function (Blueprint $table) {
            //
            $table->dropColumn(['title', 'limit_place']);

        });
    }
};
