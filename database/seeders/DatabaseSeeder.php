<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 4 Danh mục
        $categories = ['Cà phê Phin', 'Trà Trái Cây', 'Đá Xay', 'Bánh Ngọt'];
        $categoryImages = [
            'https://images.unsplash.com/photo-1544787210-282744e79c1d?w=400',
            'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400',
            'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400',
            'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400'
        ];

        foreach ($categories as $index => $cat) {
            DB::table('categories')->insert([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'image' => $categoryImages[$index],
                'created_at' => now(),
            ]);
        }

        // 2. Tạo 3 Size
        $sizes = ['Size S', 'Size M', 'Size L'];
        foreach ($sizes as $size) {
            DB::table('sizes')->insert(['name' => $size, 'created_at' => now()]);
        }

        // 3. Tạo 50 Sản phẩm & Biến thể của nó
        $productImages = [
            'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png',
            'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png',
            'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png',
            'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png'
        ];

        for ($i = 1; $i <= 50; $i++) {
            $productName = 'Món Ngon Chill Chill ' . $i;
            
            $productId = DB::table('products')->insertGetId([
                'category_id' => rand(1, 4),
                'name' => $productName,
                'slug' => Str::slug($productName) . '-' . time() . rand(10, 99), // Đảm bảo slug không trùng
                'description' => 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.',
                'status' => 1,
                'image_url' => $productImages[array_rand($productImages)],
                'created_at' => now(),
            ]);

            // Mỗi sản phẩm tự động có 2 biến thể (Size S và M) với giá ngẫu nhiên
            DB::table('product_variants')->insert([
                ['product_id' => $productId, 'size_id' => 1, 'price' => rand(30, 45) * 1000], // Giá từ 30k - 45k
                ['product_id' => $productId, 'size_id' => 2, 'price' => rand(50, 65) * 1000], // Giá từ 50k - 65k
            ]);
        }
    }
}