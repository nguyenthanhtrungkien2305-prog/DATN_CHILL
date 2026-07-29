<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Xóa file ảnh khỏi Storage/Public để tránh rác bộ nhớ
     */
    protected function deleteImageFile(?string $imageUrl): void
    {
        if (empty($imageUrl)) {
            return;
        }

        if (Str::startsWith($imageUrl, ['http://', 'https://']) && !Str::contains($imageUrl, request()->getHost())) {
            return;
        }

        $relativePath = ltrim($imageUrl, '/');
        if (Str::startsWith($relativePath, 'storage/')) {
            $relativePath = Str::after($relativePath, 'storage/');
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $publicPath = public_path(ltrim($imageUrl, '/'));
        if (file_exists($publicPath) && is_file($publicPath)) {
            @unlink($publicPath);
        }
    }

    // 1. Hiển thị danh sách danh mục
    public function index()
    {
        $categories = DB::table('categories')->orderBy('category_id', 'desc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Form thêm danh mục
    public function create()
    {
        return view('admin.categories.create');
    }

   // 3. Xử lý lưu danh mục mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string' 
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục'
        ]);

        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $imageUrl = '/storage/' . $request->file('image_file')->store('categories', 'public');
        }

        $slug = Str::slug($request->name);

        DB::table('categories')->insert([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imageUrl,
        ]);

        return redirect()->route('categories.index')->with('success', 'Thêm danh mục mới thành công!');
    }

    // 4. Form sửa danh mục
    public function edit($id)
    {
        $category = DB::table('categories')->where('category_id', $id)->first();
        return view('admin.categories.edit', compact('category'));
    }

    // 5. Xử lý cập nhật danh mục
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục'
        ]);

        $category = DB::table('categories')->where('category_id', $id)->first();
        if (!$category) abort(404);

        $imageUrl = $request->image;

        if ($request->hasFile('image_file')) {
            $this->deleteImageFile($category->image);
            $imageUrl = '/storage/' . $request->file('image_file')->store('categories', 'public');
        } elseif ($imageUrl && $imageUrl !== $category->image) {
            $this->deleteImageFile($category->image);
        }

        $slug = Str::slug($request->name);

        DB::table('categories')->where('category_id', $id)->update([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imageUrl,
        ]);

        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    // 6. Xử lý XÓA danh mục
    public function destroy($id)
    {
        $productCount = DB::table('products')->where('category_id', $id)->count();
        
        if ($productCount > 0) {
            return back()->with('error', 'Không thể xóa! Danh mục này đang chứa ' . $productCount . ' sản phẩm. Vui lòng chuyển các sản phẩm sang danh mục khác trước.');
        }

        $category = DB::table('categories')->where('category_id', $id)->first();
        if ($category) {
            // Xóa file ảnh danh mục
            $this->deleteImageFile($category->image);
            // Xóa danh mục trong DB
            DB::table('categories')->where('category_id', $id)->delete();
        }

        return redirect()->route('categories.index')->with('success', 'Đã xóa danh mục và ảnh đính kèm thành công!');
    }
}