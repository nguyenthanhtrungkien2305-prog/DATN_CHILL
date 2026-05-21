<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToppingController extends Controller
{
    // 1. Xem danh sách Topping
    public function index()
    {
        $toppings = DB::table('toppings')->orderBy('topping_id', 'desc')->paginate(10);
        return view('admin.toppings.index', compact('toppings'));
    }

    // 2. Hiện form Thêm mới
    public function create()
    {
        $allToppings = DB::table('toppings')->orderBy('topping_id', 'desc')->get();
        return view('admin.toppings.create');
    }

   // 3. Xử lý Lưu Topping mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // Thêm validation cho image
            'image' => 'nullable|string' 
        ], [
            'name.required' => 'Vui lòng nhập tên Topping',
            'price.required' => 'Vui lòng nhập giá tiền',
        ]);

        // Thêm vào database (Gồm cột image)
        DB::table('toppings')->insert([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $request->image, // <--- THÊM DÒNG NÀY
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
            // Thêm validation cho image
            'image' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên Topping',
            'price.required' => 'Vui lòng nhập giá tiền',
        ]);

        // Cập nhật database (Gồm cột image)
        DB::table('toppings')->where('topping_id', $id)->update([ // <--- topping_id
            'name' => $request->name,
            'price' => $request->price,
            'image' => $request->image, // <--- THÊM DÒNG NÀY
        ]);

        return redirect()->route('toppings.index')->with('success', 'Cập nhật Topping thành công!');
    }

    // 6. Xử lý Xóa
    public function destroy($id)
    {
        DB::table('toppings')->where('topping_id', $id)->delete();
        return back()->with('success', 'Đã xóa Topping thành công!');
    }
}