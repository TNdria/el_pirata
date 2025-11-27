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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_type_id')->constrained('payment_types');
            // Stripe transaction ID (e.g. pi_3RqCXR...)
            $table->string('stripe_transaction_id');

            // Amount paid (in cents, for Stripe) — e.g. 2500 = €25.00
            $table->decimal('amount_paid');

            // Associated "chasse" ID (internal reference in your system)
            $table->unsignedBigInteger('hunt_id');

            // Promo code used (nullable)
            $table->string('promo_code')->nullable();

            // Date and time when the transaction was completed
            $table->timestamp('paid_at')->nullable();

            // (Optional) User foreign key
            $table->unsignedBigInteger('user_id')->nullable();

            $table->enum('status', ['pending', 'validated', 'rejected', 'cancelled'])->default('pending');

            $table->timestamps();

            // Foreign keys (if you want to enable relationships)
            $table->foreign('hunt_id')->references('id')->on('huntings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
