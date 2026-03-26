<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();

            // Link to district
            $table->foreignId('district_id')
                ->constrained()
                ->cascadeOnDelete();

            // Shipping fee for this district
            $table->decimal('shipping_fee', 10, 2)->default(0);

            // Whether shipping is available in this district
            $table->boolean('is_active')->default(false);

            $table->timestamps();

            // Only one shipping zone per district
            $table->unique('district_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};
