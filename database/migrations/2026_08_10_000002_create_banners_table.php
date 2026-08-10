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
        Schema::create('banners', function (Blueprint $table) {
            $table->id('banner_id');
            $table->string('title');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable()->default('Xem ngay combo');
            $table->string('button_link')->nullable()->default('/combo');
            $table->string('image_url')->nullable();
            $table->string('bg_gradient')->nullable()->default('from-espresso via-coral to-amber-600');
            $table->string('position')->default('combo_banner');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Chèn dữ liệu mẫu ban đầu cho banner Combo
        DB::table('banners')->insert([
            'title' => 'COMBO TIẾT KIỆM – UỐNG LÀ MÊ!',
            'badge' => 'Combo Tiết Kiệm Độc Quyền',
            'description' => 'Chọn ngay combo đồ uống & bánh ngọt yêu thích với giá ưu đãi cực sốc lên đến 25%.',
            'button_text' => 'Xem ngay combo',
            'button_link' => '/combo',
            'bg_gradient' => 'from-espresso via-coral to-amber-600',
            'position' => 'combo_banner',
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
        Schema::dropIfExists('banners');
    }
};
