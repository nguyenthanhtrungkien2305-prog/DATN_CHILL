<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng lương chốt
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Trạng thái: pending (chờ), paid (đã trả)
            $table->dateTime('paid_at')->nullable(); // Trả lúc mấy giờ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};