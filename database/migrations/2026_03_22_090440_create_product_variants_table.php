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
        Schema::create('product_variants', function (Blueprint $table) {
    $table->id('variant_id');
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('size_id');
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
    $table->foreign('size_id')->references('size_id')->on('sizes')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
