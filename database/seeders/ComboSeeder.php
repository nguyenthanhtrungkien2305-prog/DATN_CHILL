<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Combo;
use App\Models\ComboItem;

class ComboSeeder extends Seeder
{
    public function run(): void
    {
        if (Combo::count() === 0) {
            $products = DB::table('products')->pluck('product_id')->take(2);
            if (count($products) >= 2) {
                $combo = Combo::create([
                    'name' => 'Combo Bữa Sáng Chill Chill',
                    'slug' => 'combo-bua-sang-chill-chill',
                    'description' => 'Khởi đầu ngày mới tỉnh táo với 1 tách Cà Phê Moka đậm đà và 1 ly Trà Sữa thơm ngon.',
                    'original_price' => 75000,
                    'price' => 59000,
                    'status' => 1,
                    'image_url' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop'
                ]);

                ComboItem::create([
                    'combo_id' => $combo->combo_id,
                    'product_id' => $products[0],
                    'quantity' => 1,
                ]);

                ComboItem::create([
                    'combo_id' => $combo->combo_id,
                    'product_id' => $products[1],
                    'quantity' => 1,
                ]);
            }
        }
    }
}
