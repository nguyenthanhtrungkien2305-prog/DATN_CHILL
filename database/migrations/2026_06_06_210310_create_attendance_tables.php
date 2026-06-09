<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng lưu Đơn đăng ký ca làm của nhân viên
        Schema::create('shift_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            $table->date('shift_date'); // Đăng ký làm ngày nào
            $table->time('start_time'); // Mấy giờ bắt đầu
            $table->integer('duration'); // Kéo dài mấy tiếng (4, 8, 12)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trạng thái duyệt
            $table->timestamps();
        });

        // 2. Bảng Chấm công (Lưu giờ vào/ra thực tế & Lý do về sớm)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            $table->date('date');
            $table->dateTime('check_in')->nullable(); // Giờ bấm vô ca
            $table->dateTime('check_out')->nullable(); // Giờ bấm ra ca
            $table->dateTime('scheduled_end_time')->nullable(); // Giờ quy định phải ra ca
            $table->text('checkout_note')->nullable(); // LÝ DO VỀ SỚM NẰM Ở ĐÂY
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('shift_registrations');
    }
};