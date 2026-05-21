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
        Schema::create('orders', function (Blueprint $table) {
    $table->id('order_id');
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('name');
    $table->string('phone');
    $table->text('address')->nullable();
    $table->string('order_type'); // dine_in, takeaway, delivery
    $table->string('table_number')->nullable();
    $table->string('status')->default('pending');
    $table->decimal('total_amount', 12, 2);
    $table->unsignedBigInteger('payment_method_id');
    $table->unsignedBigInteger('voucher_id')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
    $table->foreign('payment_method_id')->references('payment_id')->on('payment_methods');
    $table->foreign('voucher_id')->references('voucher_id')->on('vouchers')->onDelete('set null');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
