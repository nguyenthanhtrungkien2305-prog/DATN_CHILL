<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VoucherController extends Controller
{
    /**
     * TỰ ĐỘNG XÓA TẤT CẢ VOUCHER ĐÃ QUÁ HẠN SỬ DỤNG (end_date < now())
     */
    public static function cleanupExpiredVouchers()
    {
        try {
            if (Schema::hasTable('vouchers')) {
                // Lấy danh sách ID voucher đã hết hạn
                $expiredIds = DB::table('vouchers')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<', now())
                    ->pluck('voucher_id');

                if ($expiredIds->isNotEmpty()) {
                    // Xóa các voucher đã lưu của user tương ứng với voucher hết hạn
                    if (Schema::hasTable('user_vouchers')) {
                        DB::table('user_vouchers')->whereIn('voucher_id', $expiredIds)->delete();
                    }
                    // Xóa voucher khỏi bảng vouchers
                    DB::table('vouchers')->whereIn('voucher_id', $expiredIds)->delete();
                }
            }
        } catch (\Exception $e) {
            // Silence if migration pending
        }
    }

    // 1. Danh sách Voucher
    public function index(Request $request)
    {
        self::cleanupExpiredVouchers();

        $search = $request->search;
        $query = DB::table('vouchers')
            ->leftJoin('users', 'vouchers.assigned_user_id', '=', 'users.user_id')
            ->select('vouchers.*', 'users.name as assigned_user_name', 'users.phone as assigned_user_phone')
            ->orderBy('vouchers.voucher_id', 'desc');

        if ($search) {
            $query->where('vouchers.code', 'like', '%' . $search . '%');
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
        self::cleanupExpiredVouchers();
        $users = DB::table('users')->select('user_id', 'name', 'phone', 'email')->orderBy('name', 'asc')->get();
        return view('admin.vouchers.create', compact('users'));
    }

    // 3. Xử lý thêm mới
    public function store(Request $request)
    {
        self::cleanupExpiredVouchers();

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'points_required' => 'nullable|integer|min:0',
            'is_points_exchange' => 'nullable|boolean',
            'assigned_user_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'min_order' => 'required|numeric|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá này đã tồn tại',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm',
            'min_order.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        ]);

        $assignedUserId = $request->input('assigned_user_id');
        $isPointsExchange = $request->has('is_points_exchange') ? (bool)$request->is_points_exchange : ($assignedUserId ? false : true);

        $voucherId = DB::table('vouchers')->insertGetId([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'points_required' => $request->input('points_required', 10),
            'is_points_exchange' => $isPointsExchange,
            'assigned_user_id' => $assignedUserId ?: null,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->input('usage_per_user', 1),
            'used_count' => 0,
            'min_order' => $request->min_order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Nếu là Voucher cá nhân dành riêng cho 1 khách hàng, tự động đưa thẳng vào Kho Voucher cá nhân của họ
        if ($assignedUserId) {
            DB::table('user_vouchers')->insert([
                'user_id' => $assignedUserId,
                'voucher_id' => $voucherId,
                'is_used' => false,
                'save_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('vouchers.index')->with('success', 'Thêm mã giảm giá mới thành công!');
    }

    // 4. Form chỉnh sửa
    public function edit($id)
    {
        self::cleanupExpiredVouchers();

        $voucher = DB::table('vouchers')->where('voucher_id', $id)->first();
        if (!$voucher) {
            return redirect()->route('vouchers.index')->with('error', 'Không tìm thấy mã giảm giá!');
        }

        $users = DB::table('users')->select('user_id', 'name', 'phone', 'email')->orderBy('name', 'asc')->get();

        return view('admin.vouchers.edit', compact('voucher', 'users'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $id)
    {
        self::cleanupExpiredVouchers();

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $id . ',voucher_id',
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'points_required' => 'nullable|integer|min:0',
            'is_points_exchange' => 'nullable|boolean',
            'assigned_user_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'min_order' => 'required|numeric|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá này đã tồn tại',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm',
            'min_order.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        ]);

        $assignedUserId = $request->input('assigned_user_id');
        $isPointsExchange = $request->has('is_points_exchange') ? (bool)$request->is_points_exchange : ($assignedUserId ? false : true);

        DB::table('vouchers')->where('voucher_id', $id)->update([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'points_required' => $request->input('points_required', 10),
            'is_points_exchange' => $isPointsExchange,
            'assigned_user_id' => $assignedUserId ?: null,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->input('usage_per_user', 1),
            'min_order' => $request->min_order,
            'updated_at' => now(),
        ]);

        if ($assignedUserId) {
            $exists = DB::table('user_vouchers')
                ->where('user_id', $assignedUserId)
                ->where('voucher_id', $id)
                ->exists();
            if (!$exists) {
                DB::table('user_vouchers')->insert([
                    'user_id' => $assignedUserId,
                    'voucher_id' => $id,
                    'is_used' => false,
                    'save_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('vouchers.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    // 6. Xử lý xóa
    public function destroy($id)
    {
        DB::table('vouchers')->where('voucher_id', $id)->delete();
        if (Schema::hasTable('user_vouchers')) {
            DB::table('user_vouchers')->where('voucher_id', $id)->delete();
        }
        return redirect()->route('vouchers.index')->with('success', 'Đã xóa mã giảm giá thành công!');
    }
}
