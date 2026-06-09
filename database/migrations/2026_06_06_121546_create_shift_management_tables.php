<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tạo bảng Ca làm việc
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->date('date'); 
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });

        // 2. Tạo bảng trung gian (ĐÃ FIX LỖI KHÓA NGOẠI)
        Schema::create('shift_user', function (Blueprint $table) {
            $table->id();
            // Nối với bảng shifts (Mặc định bảng này có cột id)
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            
            // Nối với bảng users (Chỉ định rõ cột khóa chính là user_id)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });

        // 3. Thêm cột shift_id vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('user_id')->constrained('shifts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
        Schema::dropIfExists('shift_user');
        Schema::dropIfExists('shifts');
    }
};