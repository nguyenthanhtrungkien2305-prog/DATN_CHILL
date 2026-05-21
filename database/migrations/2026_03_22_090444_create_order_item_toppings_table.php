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
        Schema::create('order_item_toppings', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('order_item_id');
    $table->unsignedBigInteger('topping_id');
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('order_item_id')->references('order_item_id')->on('order_items')->onDelete('cascade');
    $table->foreign('topping_id')->references('topping_id')->on('toppings');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_toppings');
    }
};
