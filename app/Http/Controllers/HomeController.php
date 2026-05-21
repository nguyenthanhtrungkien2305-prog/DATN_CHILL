<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy 4 danh mục
        $categories = DB::table('categories')->limit(4)->get();

        // 2. Lấy 8 sản phẩm (Kèm theo giá RẺ NHẤT từ bảng product_variants)
        $products = DB::table('products')
            ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
            ->where('products.status', 1)
            ->groupBy('products.product_id', 'products.name', 'products.slug', 'products.description', 'products.status', 'products.image_url', 'products.category_id', 'products.created_at', 'products.updated_at')
            ->limit(8)
            ->get();

        // (Tạm thời bỏ phần reviews vì ERD mới không có bảng reviews)
        $reviews = []; 

        return view('home', compact('categories', 'products', 'reviews'));
    }
}