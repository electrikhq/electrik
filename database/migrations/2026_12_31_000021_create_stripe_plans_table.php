<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stripe_product_id')->constrained('stripe_products')->cascadeOnDelete();
            $table->string('stripe_price_id')->unique();
            $table->string('name');
            $table->integer('price');
            $table->string('currency', 3)->default('usd');
            $table->string('interval')->default('month');
            $table->integer('interval_count')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_plans');
    }
};
