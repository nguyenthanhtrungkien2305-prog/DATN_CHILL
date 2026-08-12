<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('button_secondary_text')->nullable()->after('button_link');
            $table->string('button_secondary_link')->nullable()->after('button_secondary_text');
        });

        // Seed Hero Section Banner cho Trang Chủ
        DB::table('banners')->insert([
            'title' => 'Thư giãn từng nét - Giao hòa cảm xúc',
            'badge' => 'Thưởng thức hương vị chuẩn Gu',
            'description' => 'Nơi dừng chân lý tưởng cho những tách cà phê nguyên chất đậm đà và ly trà sữa ngọt ngào. Gọi món ngay để nhận ưu đãi giao tận nơi!',
            'button_text' => 'Khám phá Menu ngay',
            'button_link' => '/san-pham',
            'button_secondary_text' => 'Món bán chạy',
            'button_secondary_link' => '#best-sellers',
            'position' => 'home_hero',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Promotion Voucher Banner cho Trang Chủ
        DB::table('banners')->insert([
            'title' => 'Giảm 20% toàn bộ đơn hàng!',
            'badge' => 'ƯU ĐÃI THÁNG 8',
            'description' => 'Nhập mã CHILL20 khi thanh toán online.',
            'button_text' => 'Đổi mã ngay',
            'button_link' => '/cart',
            'image_url' => 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop',
            'position' => 'home_promo',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['button_secondary_text', 'button_secondary_link']);
        });

        DB::table('banners')->whereIn('position', ['home_hero', 'home_promo'])->delete();
    }
};
