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
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id('cart_item_id');
                $table->unsignedBigInteger('cart_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->text('toppings')->nullable();
                $table->string('ice_level')->default('100');
                $table->string('sugar_level')->default('100');
                $table->timestamps();

                $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
