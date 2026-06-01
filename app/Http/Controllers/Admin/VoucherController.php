<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    // 1. Danh sách Voucher
    public function index(Request $request)
    {
        $search = $request->search;
        $query = DB::table('vouchers')->orderBy('voucher_id', 'desc');

        if ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        }

        $vouchers = $query->paginate(10);
        if ($search) {
            $vouchers->appends(['search' => $search]);
        }

        return view('admin.vouchers.index', compact('vouchers'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.vouchers.create');
    }

    // 3. Xử lý thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'min_order' => 'required|numeric|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá này đã tồn tại',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm',
            'min_order.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        ]);

        DB::table('vouchers')->insert([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'used_count' => 0,
            'min_order' => $request->min_order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('vouchers.index')->with('success', 'Thêm mã giảm giá mới thành công!');
    }

    // 4. Form chỉnh sửa
    public function edit($id)
    {
        $voucher = DB::table('vouchers')->where('voucher_id', $id)->first();
        if (!$voucher) {
            return redirect()->route('vouchers.index')->with('error', 'Không tìm thấy mã giảm giá!');
        }

        return view('admin.vouchers.edit', compact('voucher'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $id . ',voucher_id',
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'min_order' => 'required|numeric|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá này đã tồn tại',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm',
            'min_order.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        ]);

        DB::table('vouchers')->where('voucher_id', $id)->update([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'min_order' => $request->min_order,
            'updated_at' => now(),
        ]);

        return redirect()->route('vouchers.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    // 6. Xử lý xóa
    public function destroy($id)
    {
        DB::table('vouchers')->where('voucher_id', $id)->delete();
        return redirect()->route('vouchers.index')->with('success', 'Đã xóa mã giảm giá thành công!');
    }
}
