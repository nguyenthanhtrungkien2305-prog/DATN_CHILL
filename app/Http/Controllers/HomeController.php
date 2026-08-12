<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy 4 danh mục
        $categories = Cache::remember('home_categories', 600, function () {
            return DB::table('categories')->limit(4)->get();
        });

        // 2. Lấy MÓN BÁN CHẠY (Kiểm tra an toàn cấu trúc CSDL)
        $hasOrderItems = Schema::hasTable('order_items');

        if ($hasOrderItems) {
            $products = DB::table('products')
                ->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->select(
                    'products.*',
                    DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                )
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
                ->orderBy('total_sold', 'desc')
                ->orderBy('products.created_at', 'desc')
                ->limit(8)
                ->get();
        } else {
            $products = DB::table('products')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->select(
                    'products.*',
                    DB::raw('COALESCE(MIN(product_variants.price), 0) as price'),
                    DB::raw('0 as total_sold')
                )
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
                ->orderBy('products.created_at', 'desc')
                ->limit(8)
                ->get();
        }

        // 3. Lấy các Combo sản phẩm active cho khối Combo trên trang chủ
        $combos = Combo::with('products')
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
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

        return view('home', compact('categories', 'products', 'combos', 'featuredProduct', 'reviews', 'heroBanner', 'promoBanner'));
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