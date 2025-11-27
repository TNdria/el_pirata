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
        Schema::create('activities_logs', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->bigInteger("admin_id");
            $table->bigInteger("table_id");
            $table->text("table_type");
            $table->text("ip_address");
            $table->text("user_agent");
            $table->text("action");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities_logs');
    }
};
