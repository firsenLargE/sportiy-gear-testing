<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();

            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('address_id')->constrained()->cascadeOnDelete();

            $table->decimal('shipping_fee', 10, 2);

            $table->foreignId('status_id')->constrained('statuses');

            $table->string('order_number')->unique();

            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
