<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function show($slug)
    {
        // 1. Lấy thông tin sản phẩm chính
        $product = DB::table('products')->where('slug', $slug)->first();
        if (!$product) {
            abort(404);
        }

        // 2. XỬ LÝ ẢNH PHỤ (GALLERY)
        $extraImages = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('product_images')) {
            $extraImages = DB::table('product_images')
                ->where('product_id', $product->product_id)
                ->limit(3)
                ->pluck('image_url')
                ->toArray();
        }
            
        $gallery = array_merge([$product->image_url], $extraImages);
        $gallery = array_slice($gallery, 0, 4);

        // 3. Lấy biến thể (Size & Giá tương ứng)
        $variants = DB::table('product_variants')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
            ->where('product_id', $product->product_id)
            ->select('product_variants.*', 'sizes.name as size_name')
            ->orderBy('product_variants.price', 'asc')
            ->get();

        // 4. Lấy sản phẩm liên quan (Cùng danh mục)
        $relatedProducts = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->where('category_id', $product->category_id)
            ->where('products.product_id', '!=', $product->product_id)
            ->select('products.*', DB::raw('MIN(product_variants.price) as min_price'))
            ->groupBy('products.product_id', 'products.name', 'products.slug', 'products.description', 'products.status', 'products.image_url', 'products.category_id', 'products.created_at', 'products.updated_at')
            ->limit(4)
            ->get();

        // 5. Lấy Đánh Giá
        $reviews = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
            try {
                $reviews = \App\Models\Review::with('user')
                    ->where('product_id', $product->product_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $reviews = collect([]);
            }
        } 

        $categoryName = DB::table('categories')->where('category_id', $product->category_id)->value('name');
        $isBanhNgot = $categoryName && (str_contains(mb_strtolower($categoryName), 'bánh') || str_contains(mb_strtolower($categoryName), 'cake'));
        $isToppingCategory = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');

        // 6. LẤY TOPPING
        $toppings = collect([]);
        if (!$isBanhNgot && !$isToppingCategory) {
            $toppings = Cache::remember('active_toppings', 3600, function() {
                return DB::table('toppings')->where('status', 1)->get();
            });
        }

        return view('product.show', compact('product', 'variants', 'relatedProducts', 'reviews', 'toppings', 'gallery'));
    }

    public function index(Request $request)
    {
        $categories = Cache::remember('public_categories', 3600, function() {
            return DB::table('categories')->get();
        });

        $query = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select(
                'products.product_id', 
                'products.name', 
                'products.slug', 
                'products.description', 
                'products.status', 
                'products.image_url', 
                'products.category_id', 
                'products.created_at', 
                'products.updated_at', 
                DB::raw('MIN(product_variants.price) as price')
            )
            ->where('products.status', 1)
            ->groupBy(
                'products.product_id', 'products.name', 'products.slug', 
                'products.description', 'products.status', 'products.image_url', 
                'products.category_id', 'products.created_at', 'products.updated_at'
            );

        if ($request->filled('q')) {
            $query->where('products.name', 'LIKE', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('products.category_id', $request->category);
        }

        if ($request->filled('min_price')) {
            $query->having('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->having('price', '<=', $request->max_price);
        }

        if ($request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('products.created_at', 'desc');
        }

        $products = $query->paginate(9)->appends($request->all());

        return view('product.index', compact('products', 'categories'));
    }
}