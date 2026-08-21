<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy 4 danh mục
        $categories = DB::table('categories')->limit(4)->get();

        // 2. Lấy MÓN NƯỚC BÁN CHẠY (Chỉ hiển thị Đồ Uống / Nước uống)
        $hasOrderItems = Schema::hasTable('order_items');

        $drinkQuery = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id');

        if ($hasOrderItems) {
            $drinkQuery->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
                ->select(
                    'products.*',
                    DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                );
        } else {
            $drinkQuery->select(
                'products.*',
                DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                DB::raw('0 as total_sold')
            );
        }

        $products = $drinkQuery
            ->where('products.status', 1)
            ->where(function($q) {
                $q->whereNull('categories.name')
                  ->orWhere(function($subQ) {
                      $subQ->where('categories.name', 'NOT LIKE', '%bánh%')
                           ->where('categories.name', 'NOT LIKE', '%topping%')
                           ->where('products.name', 'NOT LIKE', '%bánh%')
                           ->where('products.name', 'NOT LIKE', '%cake%')
                           ->where('products.name', 'NOT LIKE', '%croissant%')
                           ->where('products.name', 'NOT LIKE', '%tiramisu%')
                           ->where('products.name', 'NOT LIKE', '%mousse%')
                           ->where('products.name', 'NOT LIKE', '%cheesecake%')
                           ->where('products.name', 'NOT LIKE', '%pudding%')
                           ->where('products.name', 'NOT LIKE', '%panna cotta%');
                  });
            })
            ->groupBy(
                'products.product_id',
                'products.name',
                'products.slug',
                'products.description',
                'products.status',
                'products.image_url',
                'products.category_id',
                'products.created_at',
                'products.updated_at',
                'products.discount_percent',
                'products.is_featured'
            )
            ->orderBy($hasOrderItems ? 'total_sold' : 'products.created_at', 'desc')
            ->orderBy('products.created_at', 'desc')
            ->limit(8)
            ->get();

        // 2.2 Lấy 4 MÓN BÁNH BÁN CHẠY
        $cakeQuery = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id');

        if ($hasOrderItems) {
            $cakeQuery->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
                ->select(
                    'products.*',
                    DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                );
        } else {
            $cakeQuery->select(
                'products.*',
                DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                DB::raw('0 as total_sold')
            );
        }

        $cakeProducts = $cakeQuery
            ->where('products.status', 1)
            ->where(function($q) {
                $q->where('categories.name', 'LIKE', '%bánh%')
                  ->orWhere('products.name', 'LIKE', '%bánh%')
                  ->orWhere('products.name', 'LIKE', '%cake%')
                  ->orWhere('products.name', 'LIKE', '%croissant%')
                  ->orWhere('products.name', 'LIKE', '%tiramisu%')
                  ->orWhere('products.name', 'LIKE', '%mousse%');
            })
            ->groupBy(
                'products.product_id',
                'products.name',
                'products.slug',
                'products.description',
                'products.status',
                'products.image_url',
                'products.category_id',
                'products.created_at',
                'products.updated_at',
                'products.discount_percent',
                'products.is_featured'
            )
            ->orderBy($hasOrderItems ? 'total_sold' : 'products.created_at', 'desc')
            ->orderBy('products.created_at', 'desc')
            ->limit(4)
            ->get();

        if ($cakeProducts->count() < 4) {
            $existingIds = $cakeProducts->pluck('product_id')->toArray();
            $fillQuery = DB::table('products')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id');

            if ($hasOrderItems) {
                $fillQuery->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
                    ->select(
                        'products.*',
                        DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                        DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                    );
            } else {
                $fillQuery->select(
                    'products.*',
                    DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                    DB::raw('0 as total_sold')
                );
            }

            $moreProducts = $fillQuery
                ->where('products.status', 1)
                ->whereNotIn('products.product_id', $existingIds)
                ->groupBy(
                    'products.product_id',
                    'products.name',
                    'products.slug',
                    'products.description',
                    'products.status',
                    'products.image_url',
                    'products.category_id',
                    'products.created_at',
                    'products.updated_at',
                    'products.discount_percent',
                    'products.is_featured'
                )
                ->orderBy($hasOrderItems ? 'total_sold' : 'products.created_at', 'desc')
                ->limit(4 - $cakeProducts->count())
                ->get();

            $cakeProducts = $cakeProducts->concat($moreProducts);
        }

        // 3. Lấy 3 Combo sản phẩm active cho khối Combo trên trang chủ
        $combos = Combo::with('products')
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // 4. Lấy Sản phẩm nổi bật cho Hero Banner
        if (!Schema::hasColumn('products', 'is_featured')) {
            Schema::table('products', function ($table) {
                $table->boolean('is_featured')->default(0)->after('status');
            });
        }

        $featuredProduct = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
            ->where('products.status', 1)
            ->groupBy(
                'products.product_id',
                'products.name',
                'products.slug',
                'products.description',
                'products.status',
                'products.image_url',
                'products.category_id',
                'products.created_at',
                'products.updated_at'
            )
            ->orderBy('products.is_featured', 'desc')
            ->orderBy('products.created_at', 'desc')
            ->first();

        $reviews = []; 

        // 5. Lấy Banners năng động cho Trang Chủ (Hero Banner & Promotion Voucher Banner)
        $heroBanner = DB::table('banners')
            ->where('status', 1)
            ->where('position', 'home_hero')
            ->latest('banner_id')
            ->first();

        // Ưu tiên lấy Sản phẩm được chọn trực tiếp cho Hero Banner trong Admin
        if ($heroBanner && !empty($heroBanner->product_id)) {
            $selectedProduct = DB::table('products')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->select('products.*', DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
                ->where('products.product_id', $heroBanner->product_id)
                ->where('products.status', 1)
                ->groupBy(
                    'products.product_id',
                    'products.name',
                    'products.slug',
                    'products.description',
                    'products.status',
                    'products.image_url',
                    'products.category_id',
                    'products.created_at',
                    'products.updated_at'
                )
                ->first();

            if ($selectedProduct) {
                $featuredProduct = $selectedProduct;
            }
        }

        $promoBanner = DB::table('banners')
            ->where('status', 1)
            ->where('position', 'home_promo')
            ->latest('banner_id')
            ->first();

        return view('home', compact('categories', 'products', 'cakeProducts', 'combos', 'featuredProduct', 'reviews', 'heroBanner', 'promoBanner'));
    }

    /**
     * Trang riêng biệt dành cho Gói Combo Ưu Đãi (/combo)
     */
    public function comboIndex()
    {
        $combos = Combo::with('products')
            ->where('status', 1)
            ->orderBy('combo_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('combo.index', compact('combos'));
    }

    /**
     * Trang Xem Chi Tiết Gói Combo (/combo/{id})
     */
    public function comboShow($id)
    {
        $combo = Combo::with('products')->where('status', 1)->findOrFail($id);
        
        $otherCombos = Combo::with('products')
            ->where('status', 1)
            ->where('combo_id', '!=', $id)
            ->limit(4)
            ->get();

        if (!Schema::hasColumn('reviews', 'combo_id')) {
            Schema::table('reviews', function ($table) {
                $table->unsignedBigInteger('combo_id')->nullable()->after('product_id');
            });
        }

        // Lấy 100% ĐÁNH GIÁ THẬT từ bảng reviews trong CSDL
        $productIds = $combo->products->pluck('product_id')->toArray();
        $reviews = DB::table('reviews')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.user_id')
            ->select('reviews.*', 'users.name as user_name', 'users.avatar as user_avatar')
            ->where(function($query) use ($id, $productIds) {
                $query->where('reviews.combo_id', $id);
                if (!empty($productIds)) {
                    $query->orWhereIn('reviews.product_id', $productIds);
                }
            })
            ->latest('reviews.created_at')
            ->get();

        return view('combo.show', compact('combo', 'otherCombos', 'reviews'));
    }
}