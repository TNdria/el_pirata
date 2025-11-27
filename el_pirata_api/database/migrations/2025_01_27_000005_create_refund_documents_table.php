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
        Schema::create('refund_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('refund_request_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->enum('type', ['medical_certificate', 'emergency_proof', 'technical_issue', 'other'])->default('other');
            $table->timestamps();

            $table->index('refund_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_documents');
    }
};

