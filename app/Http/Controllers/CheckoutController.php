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
        if (!$user) {
            return redirect()->route('cart.index')->with('login_required', 'Vui lòng đăng nhập tài khoản để tiến hành thanh toán và đặt hàng!');
        }
        
        // Xử lý cột address: Chuyển dữ liệu text thành mảng
        $addresses = [];
        if ($user && $user->address) {
            $decoded = json_decode($user->address, true);
            $addresses = is_array($decoded) ? $decoded : [$user->address];
        }

        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        $availableVouchers = (new CartController())->getApplicableVouchers($subTotal);

        // Tự động chọn và áp dụng mã hời nhất ĐỦ ĐIỀU KIỆN nếu chưa chọn mã nào VÀ chưa bấm hủy voucher
        if (!session()->has('voucher') && !session()->get('voucher_opt_out', false) && $subTotal > 0) {
            $bestEligible = $availableVouchers->firstWhere('is_eligible', true);
            if ($bestEligible) {
                session()->put('voucher', [
                    'voucher_id' => $bestEligible->voucher_id,
                    'code' => $bestEligible->code,
                    'discount_type' => $bestEligible->discount_type,
                    'discount_value' => $bestEligible->discount_value,
                    'discount_amount' => $bestEligible->discount_amount,
                    'min_order' => $bestEligible->min_order,
                    'auto_applied' => true
                ]);
            }
        }

        return view('checkout.index', compact('cart', 'user', 'addresses', 'availableVouchers'));
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

        if (!auth()->check()) {
            return redirect()->route('cart.index')->with('login_required', 'Vui lòng đăng nhập tài khoản để đặt hàng!');
        }

        // Tính tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        // Đọc voucher từ session
        $voucher = session()->get('voucher');
        $discountAmount = 0;
        $voucherId = null;
        if ($voucher) {
            $voucherId = $voucher['voucher_id'];
            $discountAmount = $voucher['discount_amount'];

            // Re-verify per-user usage limit right before order creation to prevent bypass
            $vDb = \DB::table('vouchers')->where('voucher_id', $voucherId)->first();
            if ($vDb) {
                $isPointsExchange = isset($vDb->is_points_exchange) ? (bool)$vDb->is_points_exchange : false;

                // Chỉ kiểm tra giới hạn lượt dùng usage_per_user cho mã công khai / mã tặng riêng (KHÔNG áp dụng cho Mã đổi điểm)
                if (!$isPointsExchange) {
                    $usagePerUser = isset($vDb->usage_per_user) ? $vDb->usage_per_user : 1;
                    if ($usagePerUser !== null && $usagePerUser > 0) {
                        $userId = auth()->id();
                        $customerPhone = $request->customer_phone ?? (auth()->check() ? auth()->user()->phone : '');
                        
                        $usedCount = 0;
                        if ($userId) {
                            $usedCount = \DB::table('orders')
                                ->where('voucher_id', $voucherId)
                                ->where('user_id', $userId)
                                ->where('status', '!=', 'cancelled')
                                ->count();
                        } elseif (!empty($customerPhone)) {
                            $usedCount = \DB::table('orders')
                                ->where('voucher_id', $voucherId)
                                ->where('customer_phone', $customerPhone)
                                ->where('status', '!=', 'cancelled')
                                ->count();
                        }

                        if ($usedCount >= $usagePerUser) {
                            session()->forget('voucher');
                            return redirect()->route('cart.index')->with('error', "Mã giảm giá này chỉ áp dụng tối đa {$usagePerUser} lần cho mỗi khách hàng. Bạn đã sử dụng mã này rồi!");
                        }
                    }
                }
            }
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        // Lưu vào Database
        $orderId = \DB::table('orders')->insertGetId([
            'user_id' => auth()->id(),
            'voucher_id' => $voucherId,
            'customer_name' => $request->customer_name ?? auth()->user()->name,
            'customer_phone' => $request->customer_phone ?? '',
            'shipping_address' => $request->shipping_address ?? '',
            'order_type' => $request->order_type,
            'table_number' => $request->table_number,
            'payment_method' => $request->payment_method,
            'total_amount' => $finalAmount,
            'discount_amount' => $discountAmount,
            'status' => 'pending', // Mặc định là Chờ xác nhận
            'items' => json_encode($cart, JSON_UNESCAPED_UNICODE), // Đóng gói nguyên cái giỏ hàng thành chuỗi JSON
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lưu chi tiết từng món vào bảng order_items chuẩn CSDL
        if (\Illuminate\Support\Facades\Schema::hasTable('order_items') && is_array($cart)) {
            foreach ($cart as $item) {
                \DB::table('order_items')->insert([
                    'order_id'   => $orderId,
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity'   => $item['quantity'] ?? 1,
                    'unit_price' => $item['price'] ?? 0,
                    'notes'      => json_encode([
                        'sugar_level' => $item['sugar_level'] ?? null,
                        'ice_level'   => $item['ice_level'] ?? null,
                        'toppings'    => $item['toppings'] ?? [],
                        'name'        => $item['name'] ?? null,
                        'size_name'   => $item['size_name'] ?? null
                    ], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Tăng lượt sử dụng của voucher & đánh dấu đã dùng trong user_vouchers
        if ($voucherId) {
            \DB::table('vouchers')->where('voucher_id', $voucherId)->increment('used_count');

            if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('user_vouchers')) {
                \DB::table('user_vouchers')
                    ->where('user_id', auth()->id())
                    ->where('voucher_id', $voucherId)
                    ->where('is_used', 0)
                    ->limit(1)
                    ->update(['is_used' => true, 'updated_at' => now()]);
            }
        }

        // Xóa giỏ hàng và voucher sau khi đặt xong
        session()->forget('cart');
        session()->forget('voucher');

        // Chuyển hướng nếu là thanh toán QR
        if ($request->payment_method === 'qr') {
            return redirect()->route('checkout.payment_qr', $orderId);
        }

        // Chuyển hướng thẳng sang trang Đơn hàng của tôi
        return redirect()->route('user.orders')->with('success', '🎉 Đặt hàng thành công! Vui lòng chờ quán xác nhận nhé.');
    }

    // 4. Hiển thị trang thanh toán QR
    public function paymentQr($id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('cart.index')->with('login_required', 'Vui lòng đăng nhập tài khoản!');
        }

        // Lấy thông tin đơn hàng
        $order = \DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng!');
        }

        return view('checkout.payment_qr', compact('order'));
    }

    // 5. Kiểm tra trạng thái đơn hàng (cho AJAX Polling)
    public function checkStatus($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = \DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(['status' => $order->status]);
    }

    // 6. Giả lập thanh toán thành công
    public function mockPay($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $affected = \DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'processing',
                'updated_at' => now(),
            ]);

        return response()->json(['success' => $affected > 0]);
    }
}