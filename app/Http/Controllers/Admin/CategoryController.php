<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    // 1. Hiển thị danh sách danh mục
    public function index()
    {
        $categories = DB::table('categories')->orderBy('category_id', 'desc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Form thêm danh mục (Sẽ làm ở bước sau)
    public function create()
    {
        return view('admin.categories.create');
    }

   // 3. Xử lý lưu danh mục mới
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string' 
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục'
        ]);

        // Tự động tạo slug từ tên danh mục
        $slug = Str::slug($request->name);

        // Thêm vào database
        DB::table('categories')->insert([
            'name' => $request->name,
            'slug' => $slug, // <--- THÊM DÒNG NÀY ĐỂ LƯU SLUG
            'image' => $request->image,
        ]);

        Cache::forget('home_categories');
        Cache::forget('public_categories');

        // Điều hướng về trang danh sách kèm thông báo
        return redirect()->route('categories.index')->with('success', 'Thêm danh mục mới thành công!');
    }
    // 4. Form sửa danh mục (Sẽ làm ở bước sau)
    public function edit($id)
    {
        $category = DB::table('categories')->where('category_id', $id)->first();
        return view('admin.categories.edit', compact('category'));
    }

    // 5. Xử lý cập nhật danh mục
    public function update(Request $request, $id)
    {
        // Kiểm tra dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục'
        ]);

        // Cập nhật lại slug nếu tên thay đổi
        $slug = Str::slug($request->name);

        // Cập nhật database
        DB::table('categories')->where('category_id', $id)->update([
            'name' => $request->name,
            'slug' => $slug, // <--- THÊM DÒNG NÀY ĐỂ LƯU SLUG
            'image' => $request->image,
        ]);

        Cache::forget('home_categories');
        Cache::forget('public_categories');

        // Điều hướng về trang danh sách
        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    // 6. Xử lý XÓA danh mục
    public function destroy($id)
    {
        // Kiểm tra xem danh mục này có đang chứa sản phẩm nào không
        $productCount = DB::table('products')->where('category_id', $id)->count();
        
        if ($productCount > 0) {
            return back()->with('error', 'Không thể xóa! Danh mục này đang chứa ' . $productCount . ' sản phẩm. Vui lòng chuyển các sản phẩm sang danh mục khác trước.');
        }

        // Nếu trống thì cho phép xóa
        DB::table('categories')->where('category_id', $id)->delete();
        
        Cache::forget('home_categories');
        Cache::forget('public_categories');

        return redirect()->route('categories.index')->with('success', 'Đã xóa danh mục thành công!');
    }
}