<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // Người đánh giá
        $table->unsignedBigInteger('product_id'); // Sản phẩm được đánh giá
        $table->string('order_id'); // Mã đơn hàng (để tránh 1 đơn đánh giá 2 lần)
        $table->integer('rating')->default(5); // Số sao (1-5)
        $table->text('comment')->nullable(); // Nội dung chữ
        $table->string('image')->nullable(); // Ảnh đính kèm
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
