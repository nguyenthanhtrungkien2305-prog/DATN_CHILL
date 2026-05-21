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
    Schema::create('users', function (Blueprint $table) {
        $table->id('user_id'); 
        $table->string('name')->unique(); // Dùng làm Tên đăng nhập
        $table->string('email')->unique()->nullable(); // Cho phép để trống
        $table->string('password');
        $table->string('phone')->nullable();
        $table->string('role')->default('user');
        $table->integer('point')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
