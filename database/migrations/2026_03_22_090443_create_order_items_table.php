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
        Schema::create('order_items', function (Blueprint $table) {
    $table->id('order_item_id');
    $table->unsignedBigInteger('order_id');
    $table->unsignedBigInteger('variant_id');
    $table->integer('quantity');
    $table->decimal('unit_price', 10, 2); // Trong ERD bạn ghi 2 lần unit_price, mình lấy 1 cái
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
    $table->foreign('variant_id')->references('variant_id')->on('product_variants');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
