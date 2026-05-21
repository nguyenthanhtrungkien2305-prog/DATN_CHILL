<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        // 1. Dọn dẹp sạch sẽ theo đúng thứ tự: Cháu -> Cha -> Ông
        Schema::dropIfExists('order_item_toppings'); 
        Schema::dropIfExists('order_items');         
        Schema::dropIfExists('orders');              

        // 2. Tạo bảng orders mới (Giữ nguyên đoạn code bên dưới của bạn)
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();
            
            $table->string('order_type')->default('delivery');
            $table->integer('table_number')->nullable();
            
            $table->string('payment_method')->default('cash');
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending');
            
            $table->json('items'); 
            
            $table->timestamps();
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
