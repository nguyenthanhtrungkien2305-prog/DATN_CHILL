<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $pendingOrders = DB::table('orders')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $toppings = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->where('categories.name', 'LIKE', '%topping%')
            ->get()
            ->keyBy('product_id');

        return view('staff.orders.index', compact('pendingOrders', 'toppings')); // Bạn nhớ đổi tên view cho khớp nếu dùng new_orders.blade.php
    }

    public function storeApi(Request $request)
    {
        try {
            $orderId = DB::table('orders')->insertGetId([
                'customer_name' => $request->customer_name ?? 'Khách Vãng Lai',
                'shipping_address' => $request->order_note,
                'total_amount' => $request->total_amount,
                'items' => json_encode($request->items, JSON_UNESCAPED_UNICODE),
                'status' => 'pending',
                'order_type' => 'pos',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công!',
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * XỬ LÝ HOÀN THÀNH ĐƠN HÀNG VÀ GHI NHẬN HOA HỒNG
     */
    public function complete($id)
    {
        try {
            $now = now('Asia/Ho_Chi_Minh');
            $today = $now->format('Y-m-d');

            // 1. Tìm Ca làm việc hiện tại của nhân viên để cộng Hoa hồng
            $activeShift = DB::table('shifts')
                ->join('shift_user', 'shifts.id', '=', 'shift_user.shift_id')
                ->where('shift_user.user_id', auth()->id())
                ->where('shifts.date', $today)
                ->select('shifts.id')
                ->orderBy('shifts.start_time', 'desc')
                ->first();

            $updateData = [
                'status' => 'completed',
                'updated_at' => $now
            ];

            // 2. Gán ID Ca và Nhân viên vào Đơn hàng
            if ($activeShift) {
                $updateData['shift_id'] = $activeShift->id;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'staff_id')) {
                $updateData['staff_id'] = auth()->id();
            }

            // 3. Cập nhật Database
            DB::table('orders')
                ->where('order_id', $id)
                ->update($updateData);

            // 4. Trả về tín hiệu cho màn hình Giao diện
            return response()->json([
                'success' => true,
                'message' => 'Đã hoàn thành đơn hàng!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi DB: ' . $e->getMessage()
            ], 500);
        }
    }
}