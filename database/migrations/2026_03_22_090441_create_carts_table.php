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
       Schema::create('carts', function (Blueprint $table) {
    $table->id('cart_id');
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('variant_id');
    $table->integer('quantity');
    $table->text('notes')->nullable(); // Ghi chú (ít đá, nhiều đường...)
    $table->timestamps();

    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
    $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
