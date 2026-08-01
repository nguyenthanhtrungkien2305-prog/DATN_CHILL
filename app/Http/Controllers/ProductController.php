<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // 1. Lấy thông tin sản phẩm chính
        $product = DB::table('products')->where('slug', $slug)->first();
        if (!$product) {
            abort(404);
        }

        // ==========================================
        // 2. XỬ LÝ ẢNH PHỤ (GALLERY SẢN PHẨM - TỐI ĐA 4 ẢNH PHỤ)
        // ==========================================
        $extraImages = DB::table('product_images')
            ->where('product_id', $product->product_id)
            ->limit(4) // Tối đa 4 ảnh phụ
            ->pluck('image_url')
            ->toArray();
            
        // Đưa ảnh chính vào vị trí đầu tiên, gộp tối đa 4 ảnh phụ (tổng tối đa 5 ảnh)
        $gallery = array_values(array_unique(array_filter(array_merge([$product->image_url], $extraImages))));
        $gallery = array_slice($gallery, 0, 5);

        // 3. Lấy biến thể (Size & Giá tương ứng)
        $variants = DB::table('product_variants')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
            ->where('product_id', $product->product_id)
            ->select('product_variants.*', 'sizes.name as size_name')
            ->orderBy('product_variants.price', 'asc') // Sắp xếp giá từ thấp đến cao
            ->get();

        // 4. Lấy sản phẩm liên quan (Cùng danh mục)
        $relatedProducts = DB::table('products')
            ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->where('category_id', $product->category_id)
            ->where('products.product_id', '!=', $product->product_id)
            ->select('products.*', DB::raw('MIN(product_variants.price) as min_price'))
            ->groupBy('products.product_id', 'products.name', 'products.slug', 'products.description', 'products.status', 'products.image_url', 'products.category_id', 'products.created_at', 'products.updated_at')
            ->limit(4)
            ->get();

        // 5. Lấy Đánh Giá
        $reviews = \App\Models\Review::with('user')
            ->where('product_id', $product->product_id)
            ->orderBy('created_at', 'desc')
            ->get(); 

        $categoryName = DB::table('categories')->where('category_id', $product->category_id)->value('name');
        $isBanhNgot = $categoryName && (str_contains(mb_strtolower($categoryName), 'bánh') || str_contains(mb_strtolower($categoryName), 'cake'));
        $isToppingCategory = $categoryName && (str_contains(mb_strtolower($categoryName), 'topping') || str_contains(mb_strtolower($categoryName), 'kèm'));

        // Khởi tạo sẵn $toppings để luôn tồn tại biến truyền ra View
        $toppings = collect([]);

        // ==========================================
        // 6. LẤY SẢN PHẨM TỪ DANH MỤC TOPPING LÀM TOPPING ĂN KÈM
        // ==========================================
        $rawToppingsList = collect([]);
        if (!$isBanhNgot && !$isToppingCategory) {
            
            // Tìm tất cả ID danh mục thuộc loại Topping (Topping, Toppings, Đồ ăn kèm...)
            $toppingCategoryIds = DB::table('categories')
                ->where(function($q) {
                    $q->where('name', 'LIKE', '%topping%')
                      ->orWhere('name', 'LIKE', '%Topping%')
                      ->orWhere('name', 'LIKE', '%kèm%')
                      ->orWhere('name', 'LIKE', '%thạch%')
                      ->orWhere('name', 'LIKE', '%trân châu%');
                })
                ->pluck('category_id')
                ->toArray();

            if (!empty($toppingCategoryIds)) {
                $rawToppingsList = DB::table('products')
                    ->whereIn('category_id', $toppingCategoryIds)
                    ->where('status', 1)
                    ->get();
            }

            // Gộp thêm từ bảng toppings nếu có
            if (\Illuminate\Support\Facades\Schema::hasTable('toppings')) {
                $dbToppings = DB::table('toppings')->get();
                foreach ($dbToppings as $dt) {
                    $rawToppingsList->push((object)[
                        'product_id' => $dt->topping_id,
                        'name' => $dt->name,
                        'image_url' => $dt->image ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=200&auto=format&fit=crop',
                        'price' => (float)$dt->price
                    ]);
                }
            }

            // NẾU CƠ SỞ DỮ LIỆU CHƯA CÓ DANH MỤC TOPPING -> TỰ ĐỘNG KHỞI TẠO ĐỂ LUÔN CÓ DỮ LIỆU HIỂN THỊ
            if ($rawToppingsList->isEmpty()) {
                $toppingCatId = DB::table('categories')->where('name', 'LIKE', '%Topping%')->value('category_id');
                if (!$toppingCatId) {
                    $toppingCatId = DB::table('categories')->insertGetId([
                        'name' => 'Topping',
                        'description' => 'Topping ăn kèm đồ uống',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $sampleToppings = [
                    ['name' => 'Trân Châu Đen Hoàng Kim', 'price' => 10000, 'image_url' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=200&auto=format&fit=crop'],
                    ['name' => 'Kem Cheese Béo Ngậy', 'price' => 12000, 'image_url' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?q=80&w=200&auto=format&fit=crop'],
                    ['name' => 'Thạch Củ Năng Giòn Rụm', 'price' => 10000, 'image_url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=200&auto=format&fit=crop'],
                    ['name' => 'Pudding Trứng Mềm Mịn', 'price' => 10000, 'image_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=200&auto=format&fit=crop'],
                ];

                foreach ($sampleToppings as $st) {
                    $pId = DB::table('products')->insertGetId([
                        'category_id' => $toppingCatId,
                        'name' => $st['name'],
                        'price' => $st['price'],
                        'image_url' => $st['image_url'],
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $defaultSizeId = DB::table('sizes')->value('size_id') ?? 1;
                    DB::table('product_variants')->insert([
                        'product_id' => $pId,
                        'size_id' => $defaultSizeId,
                        'price' => $st['price']
                    ]);
                }

                $rawToppingsList = DB::table('products')
                    ->where('category_id', $toppingCatId)
                    ->where('status', 1)
                    ->get();
            }

            // Chuẩn hóa định dạng mảng $toppings cho View
            $toppings = $rawToppingsList->map(function($t) {
                $price = 0;
                if (isset($t->price) && $t->price > 0) {
                    $price = (float)$t->price;
                } else {
                    $minPrice = DB::table('product_variants')->where('product_id', $t->product_id ?? $t->topping_id)->min('price');
                    $price = $minPrice ? (float)$minPrice : 10000;
                }
                return (object)[
                    'topping_id' => $t->product_id ?? $t->topping_id ?? 1,
                    'name' => $t->name,
                    'image' => $t->image_url ?? $t->image ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=200&auto=format&fit=crop',
                    'price' => $price
                ];
            });
        }

        // Truyền thêm biến $gallery, $isBanhNgot, $isToppingCategory ra View
        return view('product.show', compact('product', 'variants', 'relatedProducts', 'reviews', 'toppings', 'gallery', 'isBanhNgot', 'isToppingCategory'));
    }

    public function index(Request $request)
    {
        // 1. Lấy danh sách danh mục để hiển thị ở Sidebar
        $categories = DB::table('categories')->get();

        // 2. Khởi tạo câu truy vấn cơ bản (Lấy sản phẩm + Giá nhỏ nhất)
        $query = DB::table('products')
            ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
            ->where('products.status', 1)
            ->groupBy(
                'products.product_id', 'products.name', 'products.slug', 
                'products.description', 'products.status', 'products.image_url', 
                'products.category_id', 'products.created_at', 'products.updated_at'
            );

        // 3. Xử lý Lọc theo Danh mục
        if ($request->filled('category')) {
            $query->where('products.category_id', $request->category);
        }

        // 4. Xử lý Lọc theo Giá (Dùng having vì price là cột tính toán từ hàm MIN)
        if ($request->filled('min_price')) {
            $query->having('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->having('price', '<=', $request->max_price);
        }

        // 5. Xử lý Sắp xếp
        if ($request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('products.created_at', 'desc'); // Mới nhất làm mặc định
        }

        // 6. Phân trang: Tối đa 9 sản phẩm/trang và giữ lại các tham số lọc trên URL
        $products = $query->paginate(9)->appends($request->all());

        return view('product.index', compact('products', 'categories'));
    }
}