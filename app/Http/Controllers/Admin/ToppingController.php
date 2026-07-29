<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ToppingController extends Controller
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

    // 1. Xem danh sách Topping
    public function index()
    {
        $toppings = DB::table('toppings')->orderBy('topping_id', 'desc')->paginate(10);
        return view('admin.toppings.index', compact('toppings'));
    }

    // 2. Hiện form Thêm mới
    public function create()
    {
        return view('admin.toppings.create');
    }

   // 3. Xử lý Lưu Topping mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string' 
        ], [
            'name.required' => 'Vui lòng nhập tên Topping',
            'price.required' => 'Vui lòng nhập giá tiền',
        ]);

        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $imageUrl = '/storage/' . $request->file('image_file')->store('toppings', 'public');
        }

        DB::table('toppings')->insert([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imageUrl,
        ]);

        return redirect()->route('toppings.index')->with('success', 'Thêm Topping mới thành công!');
    }

    // 4. Hiện form Sửa
    public function edit($id)
    {
        $topping = DB::table('toppings')->where('topping_id', $id)->first();
        return view('admin.toppings.edit', compact('topping'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên Topping',
            'price.required' => 'Vui lòng nhập giá tiền',
        ]);

        $topping = DB::table('toppings')->where('topping_id', $id)->first();
        $imageUrl = $request->image;

        if ($request->hasFile('image_file')) {
            if ($topping) $this->deleteImageFile($topping->image);
            $imageUrl = '/storage/' . $request->file('image_file')->store('toppings', 'public');
        } elseif ($topping && $imageUrl && $imageUrl !== $topping->image) {
            $this->deleteImageFile($topping->image);
        }

        DB::table('toppings')->where('topping_id', $id)->update([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imageUrl,
        ]);

        return redirect()->route('toppings.index')->with('success', 'Cập nhật Topping thành công!');
    }

    // 6. Xử lý Xóa
    public function destroy($id)
    {
        $topping = DB::table('toppings')->where('topping_id', $id)->first();
        if ($topping) {
            $this->deleteImageFile($topping->image);
            DB::table('toppings')->where('topping_id', $id)->delete();
        }
        return back()->with('success', 'Đã xóa Topping thành công!');
    }
}