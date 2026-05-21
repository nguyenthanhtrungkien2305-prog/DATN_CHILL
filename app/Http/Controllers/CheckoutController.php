<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // 1. Hiển thị trang Thanh toán
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $user = auth()->user(); // Lấy thông tin user đang đăng nhập
        
        // Xử lý cột address: Chuyển dữ liệu text thành mảng
        $addresses = [];
        if ($user && $user->address) {
            $decoded = json_decode($user->address, true);
            // Nếu parse JSON thành công thì lấy, không thì coi như nó là 1 chuỗi text bình thường
            $addresses = is_array($decoded) ? $decoded : [$user->address];
        }

        return view('checkout.index', compact('cart', 'user', 'addresses'));
    }

    // 2. Thêm địa chỉ mới trực tiếp tại trang Checkout (AJAX)
    public function addAddress(Request $request)
    {
        $user = auth()->user();
        $newAddress = $request->new_address;

        $addresses = [];
        if ($user->address) {
            $decoded = json_decode($user->address, true);
            $addresses = is_array($decoded) ? $decoded : [$user->address];
        }

        // Chặn nếu đã lưu đủ 4 địa chỉ
        if (count($addresses) >= 4) {
            return response()->json(['success' => false, 'message' => 'Bạn chỉ được lưu tối đa 4 địa chỉ!']);
        }

        // Thêm địa chỉ mới vào mảng
        $addresses[] = $newAddress;

        // Lưu mảng ngược lại vào Database dưới dạng JSON
        DB::table('users')->where('user_id', $user->user_id)->update([
            'address' => json_encode($addresses, JSON_UNESCAPED_UNICODE)
        ]);

        return response()->json(['success' => true, 'message' => 'Đã thêm địa chỉ mới!']);
    }
    // Xóa địa chỉ (AJAX)
    public function deleteAddress(Request $request)
    {
        $user = auth()->user();
        $index = $request->index; // Vị trí địa chỉ trong mảng (0, 1, 2, 3)

        if ($user && $user->address) {
            $addresses = json_decode($user->address, true);
            
            // Nếu parse thành mảng thành công và vị trí đó tồn tại
            if (is_array($addresses) && isset($addresses[$index])) {
                unset($addresses[$index]); // Xóa khỏi mảng
                $addresses = array_values($addresses); // Đánh lại số thứ tự index (0,1,2)

                // Cập nhật lại Database
                DB::table('users')->where('user_id', $user->user_id)->update([
                    'address' => json_encode($addresses, JSON_UNESCAPED_UNICODE)
                ]);

                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Không thể xóa địa chỉ này!']);
    }

    // 3. Xử lý Đặt hàng (Sẽ code chi tiết ở bước sau)
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        // Lưu vào Database
        \DB::table('orders')->insert([
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name ?? auth()->user()->name,
            'customer_phone' => $request->customer_phone ?? '',
            'shipping_address' => $request->shipping_address ?? '',
            'order_type' => $request->order_type,
            'table_number' => $request->table_number,
            'payment_method' => $request->payment_method,
            'total_amount' => $totalAmount,
            'status' => 'pending', // Mặc định là Chờ xác nhận
            'items' => json_encode($cart, JSON_UNESCAPED_UNICODE), // Đóng gói nguyên cái giỏ hàng thành chuỗi JSON
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Xóa giỏ hàng sau khi đặt xong
        session()->forget('cart');

        // Chuyển hướng thẳng sang trang Đơn hàng của tôi
        return redirect()->route('user.orders')->with('success', '🎉 Đặt hàng thành công! Vui lòng chờ quán xác nhận nhé.');
    }
}