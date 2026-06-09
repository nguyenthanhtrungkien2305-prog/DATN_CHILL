<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Đã gỡ bỏ lệnh ép buộc chuyển hướng ở đây!
        // Bây giờ Admin hay Staff ra trang chủ đều được thoải mái xem web.

        // Lấy 4 danh mục
        $categories = DB::table('categories')->limit(4)->get();

        // Lấy 8 sản phẩm (Kèm theo giá RẺ NHẤT từ bảng product_variants)
        $products = DB::table('products')
            ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
            ->where('products.status', 1)
            ->groupBy('products.product_id', 'products.name', 'products.slug', 'products.description', 'products.status', 'products.image_url', 'products.category_id', 'products.created_at', 'products.updated_at')
            ->limit(8)
            ->get();

        $reviews = []; 

        return view('home', compact('categories', 'products', 'reviews'));
    }
}