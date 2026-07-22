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
        // 2. [MỚI] XỬ LÝ ẢNH PHỤ (GALLERY - TỐI ĐA 4 ẢNH)
        // ==========================================
        // Giả định bạn có bảng `product_images` chứa các ảnh thêm của sản phẩm
        $extraImages = DB::table('product_images')
            ->where('product_id', $product->product_id)
            ->limit(3) // Lấy tối đa 3 ảnh phụ
            ->pluck('image_url')
            ->toArray();
            
        // Đưa ảnh chính vào mảng đầu tiên, sau đó gộp với các ảnh phụ
        $gallery = array_merge([$product->image_url], $extraImages);
        // Cắt mảng để đảm bảo dù lỗi cũng chỉ hiển thị tối đa 4 ảnh
        $gallery = array_slice($gallery, 0, 4);

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
        $isToppingCategory = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');

        // ==========================================
        // 6. [MỚI] LẤY TOPPING TỪ BẢNG SẢN PHẨM
        // ==========================================
        $toppings = collect([]);
        if (!$isBanhNgot && !$isToppingCategory) {
            
            // Tìm ID của Danh mục Topping
            $toppingCategory = DB::table('categories')
                ->where('name', 'LIKE', '%topping%')
                ->orWhere('name', 'LIKE', '%Topping%')
                ->first();

            if ($toppingCategory) {
                // Lấy các "sản phẩm" thuộc danh mục Topping này
                $toppings = DB::table('products')
                    ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                    ->where('products.category_id', $toppingCategory->category_id)
                    ->where('products.status', 1)
                    ->select(
                        // Dùng bí danh as để giao diện HTML cũ vẫn hiểu
                        'products.product_id as topping_id', 
                        'products.name', 
                        'products.image_url as image', 
                        DB::raw('MIN(product_variants.price) as price')
                    )
                    ->groupBy('products.product_id', 'products.name', 'products.image_url')
                    ->get();
            }
        }

        // Truyền thêm biến $gallery ra View
        return view('product.show', compact('product', 'variants', 'relatedProducts', 'reviews', 'toppings', 'gallery'));
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