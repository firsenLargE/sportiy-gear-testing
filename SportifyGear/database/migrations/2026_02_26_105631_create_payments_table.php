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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('method', ['cod', 'khalti', 'esewa', 'fonepay']);

            $table->string('transaction_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->json('payment_details')->nullable();

            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('NPR');

            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('user_id');

            $table->unique('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
