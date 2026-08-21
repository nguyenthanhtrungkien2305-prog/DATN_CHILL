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
            $subTotal += ($item['price'] + ($item['topping_total'] ?? 0)) * $item['quantity'];
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
            $totalAmount += ($item['price'] + ($item['topping_total'] ?? 0)) * $item['quantity'];
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
                $isPointsExchange = (!empty($vDb->is_points_exchange) && $vDb->is_points_exchange == 1) || (!empty($vDb->points_required) && $vDb->points_required > 0);
                $isAssignedUser = !empty($vDb->assigned_user_id);
                $userId = auth()->id();

                if ($isPointsExchange || $isAssignedUser) {
                    if (!$userId) {
                        session()->forget('voucher');
                        return redirect()->route('cart.index')->with('error', 'Vui lòng đăng nhập để sử dụng mã voucher từ điểm tích lũy!');
                    }

                    if ($isAssignedUser && $vDb->assigned_user_id != $userId) {
                        session()->forget('voucher');
                        return redirect()->route('cart.index')->with('error', 'Mã voucher này không dành cho tài khoản của bạn!');
                    }

                    $unusedCount = \DB::table('user_vouchers')
                        ->where('user_id', $userId)
                        ->where('voucher_id', $voucherId)
                        ->where('is_used', 0)
                        ->count();

                    if ($unusedCount <= 0) {
                        session()->forget('voucher');
                        return redirect()->route('cart.index')->with('error', 'Bạn chưa đổi mã voucher này từ điểm thưởng hoặc đã sử dụng hết lượt trong kho!');
                    }
                } else {
                    $usagePerUser = isset($vDb->usage_per_user) ? $vDb->usage_per_user : 1;
                    if ($usagePerUser !== null && $usagePerUser > 0) {
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

        // Xử lý Khấu trừ từ Ví Số Dư Hoàn Tiền nếu người dùng lựa chọn
        $walletDeduction = 0;
        if ($request->has('use_wallet_balance') && auth()->user()->wallet_balance > 0) {
            $walletBalance = (float)auth()->user()->wallet_balance;
            $walletDeduction = min($walletBalance, $finalAmount);

            if ($walletDeduction > 0) {
                // Khấu trừ số dư hoàn tiền của user
                \DB::table('users')->where('user_id', auth()->id())->decrement('wallet_balance', $walletDeduction);

                // Giảm bớt số tiền phải thanh toán
                $finalAmount = max(0, $finalAmount - $walletDeduction);
            }
        }

        // Lưu vào Database
        $orderId = \DB::table('orders')->insertGetId([
            'user_id' => auth()->check() ? auth()->id() : null,
            'customer_name' => $request->customer_name ?? $request->recipient_name ?? (auth()->check() ? auth()->user()->name : 'Khách Vãng Lai'),
            'customer_phone' => $request->customer_phone ?? $request->phone ?? (auth()->check() ? auth()->user()->phone : ''),
            'shipping_address' => $request->shipping_address ?? $request->address ?? '',
            'order_type' => $request->order_type ?? 'delivery',
            'table_number' => $request->table_number ?? null,
            'payment_method' => $request->payment_method ?? 'bank_transfer',
            'total_amount' => $finalAmount,
            'discount_amount' => $discountAmount,
            'used_wallet_amount' => $walletDeduction,
            'status' => ($walletDeduction > 0 && $finalAmount == 0) ? 'processing' : 'pending', // Nếu đã dùng ví trả hết 100% thì tự động duyệt sang Đang chuẩn bị
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

        // Chuyển hướng nếu là thanh toán QR VÀ số tiền còn phải trả > 0
        if ($request->payment_method === 'qr' && $finalAmount > 0) {
            return redirect()->route('checkout.payment_qr', $orderId);
        }

        $successMsg = ($walletDeduction > 0 && $finalAmount == 0)
            ? '🎉 Đặt hàng thành công! Đơn hàng đã được thanh toán 100% bằng Ví tiền hoàn của bạn.'
            : '🎉 Đặt hàng thành công! Vui lòng chờ quán xác nhận nhé.';

        // Chuyển hướng thẳng sang trang Đơn hàng của tôi
        return redirect()->route('user.orders')->with('success', $successMsg);
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

        // Nếu đơn hàng đã thanh toán xong (processing/completed) hoặc 0đ -> Chuyển thẳng về Đơn hàng của tôi
        if ($order->status === 'processing' || $order->status === 'completed' || $order->total_amount <= 0) {
            return redirect()->route('user.orders')->with('success', '🎉 Đơn hàng #' . $id . ' đã được thanh toán thành công!');
        }

        return view('checkout.payment_qr', compact('order'));
    }

    // 5. Kiểm tra trạng thái đơn hàng (cho AJAX Polling)
    public function checkStatus($id)
    {
        $userId = auth()->id() ?? (auth()->check() ? (auth()->user()->user_id ?? auth()->user()->id) : null);

        $order = \DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($userId && $order->user_id && $order->user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'status'  => $order->status
        ]);
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