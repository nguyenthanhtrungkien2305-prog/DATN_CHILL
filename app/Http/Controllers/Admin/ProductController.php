<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
   // 1. Hiển thị danh sách sản phẩm (Có tích hợp Tìm kiếm)
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ URL (nếu có)
        $search = $request->search;

        // Xây dựng câu truy vấn cơ bản (Chưa lấy dữ liệu vội)
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
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
                'categories.name as category_name',
                DB::raw('COALESCE(MIN(product_variants.price), products.price) as price')
            )
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
                'categories.name',
                'products.price'
            )
            ->orderBy('products.product_id', 'desc');

        // Nếu người dùng có nhập từ khóa tìm kiếm
        if ($search) {
            $query->where('products.name', 'like', '%' . $search . '%')
                  ->orWhere('products.product_id', $search); // Cho phép tìm theo cả ID sản phẩm
        }

        // Thực thi truy vấn và phân trang
        $products = $query->paginate(10);

        // Giữ lại từ khóa trên thanh URL khi người dùng bấm chuyển trang (Page 2, Page 3...)
        if ($search) {
            $products->appends(['search' => $search]);
        }

        return view('admin.products.index', compact('products'));
    }

    // 2. Form thêm sản phẩm (Sẽ làm ở bước sau)
   public function create()
    {
        // 1. Lấy danh sách danh mục
        $categories = DB::table('categories')->get();
        
        // 2. Lấy tất cả topping để hiển thị ra cho Admin chọn (ĐÂY LÀ DÒNG BẠN ĐANG THIẾU)
        $allToppings = DB::table('toppings')->orderBy('topping_id', 'desc')->get(); 
        
        // 3. Nhớ nhét 'allToppings' vào compact nhé!
        return view('admin.products.create', compact('categories', 'allToppings'));
    }

    // 3. Xử lý lưu sản phẩm mới
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image_url' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'price.required' => 'Vui lòng nhập giá sản phẩm',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0',
            'image_url.url' => 'Link hình ảnh không hợp lệ'
        ]);

        // Tự động tạo slug từ tên sản phẩm (thêm time() để đảm bảo không bị trùng)
        $slug = Str::slug($request->name) . '-' . time();

        // Thêm dữ liệu vào bảng products
        // Thay chữ insert thành insertGetId và gán nó vào biến $product_id
        $product_id = DB::table('products')->insertGetId([
            'name' => $request->name,
            'price' => $request->price,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'status' => $request->status,
            'image_url' => $request->image_url,
            'created_at' => now(),
            'updated_at' => now(), // Bỏ ->toDateTimeString() đi cho an toàn nhé
        ]);

        // ==========================================
        // XỬ LÝ LƯU TOPPING CHO SẢN PHẨM MỚI
        // ==========================================
        if ($request->has('toppings')) {
            $insertData = [];
            foreach ($request->toppings as $topping_id) {
                $insertData[] = [
                    'product_id' => $product_id, // Sử dụng cái ID vừa lấy được ở trên
                    'topping_id' => $topping_id
                ];
            }
            DB::table('product_topping')->insert($insertData);
        }

        // Chuyển hướng về trang danh sách
        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm mới thành công!');
    }

    // 4. Form sửa sản phẩm (Sẽ làm ở bước sau)
    public function edit($id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        $categories = DB::table('categories')->get();
        // Lấy tất cả topping
        $allToppings = DB::table('toppings')->orderBy('topping_id', 'desc')->get();
        
        // Lấy các Topping ĐÃ ĐƯỢC CHỌN của riêng sản phẩm này từ bảng trung gian
        $selectedToppings = DB::table('product_topping')
            ->where('product_id', $id)
            ->pluck('topping_id') // Chỉ lấy mảng các ID
            ->toArray();
            return view('admin.products.edit', compact('product', 'categories', 'allToppings', 'selectedToppings'));
    }

    // 5. Xử lý cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        // Kiểm tra dữ liệu đầu vào (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image_url' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'price.required' => 'Vui lòng nhập giá sản phẩm',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0',
            'image_url.url' => 'Link hình ảnh không hợp lệ'
        ]);

        // Cập nhật vào database
        DB::table('products')->where('product_id', $id)->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'status' => $request->status,
            'image_url' => $request->image_url,
            'updated_at' => now(),
        ]);

        // ==========================================
        // DÁN CHÍNH XÁC ĐOẠN XỬ LÝ TOPPING NÀY VÀO:
        // ==========================================
        
        // 1. Dùng biến $id để xóa các topping cũ
        DB::table('product_topping')->where('product_id', $id)->delete();

        // 2. Thêm topping mới nếu có tick chọn
        if ($request->has('toppings')) {
            $insertData = [];
            foreach ($request->toppings as $topping_id) {
                $insertData[] = [
                    'product_id' => $id, // Chỗ này cũng dùng biến $id nhé!
                    'topping_id' => $topping_id
                ];
            }
            DB::table('product_topping')->insert($insertData);
        }

        // ==========================================

        // Cuối cùng là chuyển hướng về trang danh sách
        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
    // 6. Xử lý XÓA sản phẩm
    public function destroy($id)
    {
        // Nhờ lúc làm ERD có 'onDelete cascade', xóa Product sẽ tự động xóa luôn Biến thể (Variants)
        DB::table('products')->where('product_id', $id)->delete();
        
        return redirect()->route('products.index')->with('success', 'Đã xóa sản phẩm thành công!');
    }
}