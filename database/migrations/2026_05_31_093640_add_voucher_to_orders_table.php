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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->nullable()->after('user_id');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('total_amount');
            
            $table->foreign('voucher_id')->references('voucher_id')->on('vouchers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['orders_voucher_id_foreign']);
            $table->dropColumn(['voucher_id', 'discount_amount']);
        });
    }
};
