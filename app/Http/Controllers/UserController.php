<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // 1. Hiển thị trang tài khoản
    public function profile()
    {
        // Lấy thông tin user đang đăng nhập
        $user = Auth::user(); 
        return view('user.profile', compact('user'));
    }

    // 2. Xử lý lưu thông tin và avatar
   public function updateProfile(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // File ảnh, tối đa 2MB
        ]);

        $user = auth()->user(); // Hoặc DB::table('users')->where('user_id', $id)->first() tùy logic của bạn
        
        // 2. Mảng dữ liệu cần update (Số điện thoại chỉ được cập nhật thông qua xác thực SMS OTP)
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address, // Lưu địa chỉ
            'updated_at' => now(),
        ];

        // 3. XỬ LÝ LƯU ẢNH AVATAR VĨNH VIỄN
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Tạo tên file ngẫu nhiên để không bị trùng (vd: 1690000000_avatar.jpg)
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Di chuyển file ảnh vào thư mục public/uploads/avatars
            $file->move(public_path('uploads/avatars'), $filename);
            
            // Cập nhật đường dẫn vào mảng để lưu xuống Database
            $updateData['avatar'] = 'uploads/avatars/' . $filename;
        }

        // 4. Update vào Database
        \DB::table('users')->where('user_id', $user->user_id)->update($updateData);

        return back()->with('success', 'Đã cập nhật hồ sơ thành công!');
    }
    // ==========================================
    // HIỂN THỊ LỊCH SỬ ĐƠN HÀNG (CÓ BỘ LỌC)
    // ==========================================
    public function orders(\Illuminate\Http\Request $request)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        // 1. Tính toán các số liệu thống kê cho Dashboard đơn hàng
        $totalOrdersCount = \Illuminate\Support\Facades\DB::table('orders')->where('user_id', $userId)->count();
        $processingOrdersCount = \Illuminate\Support\Facades\DB::table('orders')->where('user_id', $userId)->whereIn('status', ['pending', 'processing'])->count();
        $completedOrdersCount = \Illuminate\Support\Facades\DB::table('orders')->where('user_id', $userId)->where('status', 'completed')->count();
        $userPoints = auth()->user()->point ?? \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->value('point') ?? 0;

        // 2. Khởi tạo câu query cơ bản (chỉ lấy đơn của user này)
        $query = \Illuminate\Support\Facades\DB::table('orders')
                    ->where('user_id', $userId);

        // 3. Nếu khách hàng có chọn Ngày -> Lọc theo ngày
        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }

        // 4. Nếu khách hàng có chọn Trạng thái -> Lọc theo trạng thái
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        // 5. Thực thi câu lệnh và lấy dữ liệu
        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('user.orders', compact('orders', 'totalOrdersCount', 'processingOrdersCount', 'completedOrdersCount', 'userPoints'));
    }  

    // ==========================================
    // ==========================================
    // XỬ LÝ GỬI ĐÁNH GIÁ (REVIEW)
    // ==========================================
    public function submitReview(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'product_id' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:5120' // Tối đa 5MB
        ]);

        $userId = auth()->user()->user_id ?? auth()->id();

        // Kiểm tra xem khách đã đánh giá món này trong đơn này chưa
        $exists = \Illuminate\Support\Facades\DB::table('reviews')
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này trong đơn hàng rồi!');
        }

        // Xử lý upload ảnh (Lưu vào storage/app/public/reviews)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        // Lưu vào Database
        \Illuminate\Support\Facades\DB::table('reviews')->insert([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath ? '/storage/' . $imagePath : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Tuyệt vời! Đánh giá của bạn đã được ghi nhận.');
    }
    // Hàm hiển thị trang chi tiết đơn hàng
    public function show($order_id)
    {
        // Lấy thông tin đơn hàng
        $order = \Illuminate\Support\Facades\DB::table('orders')
            ->where('order_id', $order_id)
            ->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng');
        }

        return view('user.order_detail', compact('order'));
    }

    // ==========================================
    // TRANG TÍCH ĐIỂM VÀ ĐỔI VOUCHER
    // ==========================================
    public function points()
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        // Tự động xóa tất cả các voucher đã quá hạn sử dụng
        \App\Http\Controllers\Admin\VoucherController::cleanupExpiredVouchers();

        $user = \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->first();
        $userPhone = $user->phone ?? null;

        // 1. Đảm bảo bảng vouchers có các cột cần thiết
        if (!\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'points_required')) {
            \Illuminate\Support\Facades\Schema::table('vouchers', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('points_required')->default(10);
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('vouchers', 'is_points_exchange')) {
            \Illuminate\Support\Facades\Schema::table('vouchers', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->boolean('is_points_exchange')->default(true);
                $table->unsignedBigInteger('assigned_user_id')->nullable();
            });
        }

        // 2. Lấy danh sách các đơn hàng ĐÃ HOÀN THÀNH (khớp theo user_id HOẶC Số điện thoại)
        $completedOrders = \Illuminate\Support\Facades\DB::table('orders')
            ->where(function ($q) use ($userId, $userPhone) {
                $q->where('user_id', $userId);
                if (!empty($userPhone)) {
                    $q->orWhere('customer_phone', $userPhone);
                }
            })
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. TÍNH TOÁN & ĐỒNG BỘ ĐIỂM TÍCH LŨY THỰC TẾ REALTIME
        $totalSpent = $completedOrders->sum('total_amount');
        $earnedPoints = (int)floor($totalSpent / 10000);

        $spentPoints = (int)\Illuminate\Support\Facades\DB::table('user_vouchers')
            ->join('vouchers', 'user_vouchers.voucher_id', '=', 'vouchers.voucher_id')
            ->where('user_vouchers.user_id', $userId)
            ->sum('vouchers.points_required');

        $calculatedBalance = max(0, $earnedPoints - $spentPoints);

        // Cập nhật số điểm thực tế về CSDL để đồng bộ 100%
        \Illuminate\Support\Facades\DB::table('users')
            ->where('user_id', $userId)
            ->update(['point' => $calculatedBalance]);

        $user = \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->first();

        // Lấy số đơn hàng đang xử lý (Pending/Processing)
        $pendingOrdersCount = \Illuminate\Support\Facades\DB::table('orders')
            ->where(function ($q) use ($userId, $userPhone) {
                $q->where('user_id', $userId);
                if (!empty($userPhone)) {
                    $q->orWhere('customer_phone', $userPhone);
                }
            })
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        // 4. Lấy danh sách Voucher ĐỔI ĐIỂM (công khai trên sàn đổi điểm)
        $availableVouchers = \Illuminate\Support\Facades\DB::table('vouchers')
            ->where(function ($query) {
                $query->where('is_points_exchange', 1)->orWhereNull('is_points_exchange');
            })
            ->whereNull('assigned_user_id')
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        foreach ($availableVouchers as $v) {
            if (empty($v->points_required) || $v->points_required <= 0) {
                $v->points_required = $v->discount_type === 'percent' ? 20 : max(10, (int)floor($v->discount_value / 1000));
            }
        }

        // 5. Lấy kho voucher của người dùng này
        $rawMyVouchers = \Illuminate\Support\Facades\DB::table('user_vouchers')
            ->join('vouchers', 'user_vouchers.voucher_id', '=', 'vouchers.voucher_id')
            ->where('user_vouchers.user_id', $userId)
            ->select('user_vouchers.*', 'vouchers.code', 'vouchers.discount_type', 'vouchers.discount_value', 'vouchers.min_order', 'vouchers.end_date', 'vouchers.assigned_user_id')
            ->orderBy('user_vouchers.id', 'desc')
            ->get();

        $groupedVouchers = [];
        foreach ($rawMyVouchers as $mv) {
            $key = $mv->voucher_id;
            if (!isset($groupedVouchers[$key])) {
                $mv->available_quantity = 0;
                $mv->used_quantity = 0;
                $groupedVouchers[$key] = $mv;
            }
            if ($mv->is_used) {
                $groupedVouchers[$key]->used_quantity++;
            } else {
                $groupedVouchers[$key]->available_quantity++;
            }
        }
        $myVouchers = collect(array_values($groupedVouchers));

        // 6. Lấy lịch sử đổi Voucher bằng điểm của người dùng
        $redeemHistory = \Illuminate\Support\Facades\DB::table('user_vouchers')
            ->join('vouchers', 'user_vouchers.voucher_id', '=', 'vouchers.voucher_id')
            ->where('user_vouchers.user_id', $userId)
            ->select(
                'user_vouchers.*',
                'vouchers.code',
                'vouchers.discount_type',
                'vouchers.discount_value',
                'vouchers.points_required',
                'vouchers.min_order'
            )
            ->orderBy('user_vouchers.id', 'desc')
            ->get();

        foreach ($redeemHistory as $rh) {
            if (empty($rh->points_required) || $rh->points_required <= 0) {
                $rh->points_required = $rh->discount_type === 'percent' ? 20 : max(10, (int)floor($rh->discount_value / 1000));
            }
        }

        return view('user.points', compact('user', 'availableVouchers', 'myVouchers', 'completedOrders', 'redeemHistory', 'pendingOrdersCount'));
    }

    // ==========================================
    // XỬ LÝ ĐỔI VOUCHER BẰNG ĐIỂM TÍCH LŨY
    // ==========================================
    public function redeemVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|integer|exists:vouchers,voucher_id'
        ]);

        $userId = auth()->user()->user_id ?? auth()->id();
        $user = \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->first();
        $voucher = \Illuminate\Support\Facades\DB::table('vouchers')->where('voucher_id', $request->voucher_id)->first();

        if (!$user || !$voucher) {
            return back()->with('error', 'Không tìm thấy thông tin hợp lệ!');
        }

        $pointsNeeded = $voucher->points_required ?? ($voucher->discount_type === 'percent' ? 20 : max(10, (int)floor($voucher->discount_value / 1000)));

        if ($user->point < $pointsNeeded) {
            return back()->with('error', 'Điểm tích lũy của bạn không đủ để đổi mã này! Cần ' . $pointsNeeded . ' điểm, bạn đang có ' . $user->point . ' điểm.');
        }

        // Trừ điểm user & Thêm vào user_vouchers
        \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->decrement('point', $pointsNeeded);

        \Illuminate\Support\Facades\DB::table('user_vouchers')->insert([
            'user_id' => $userId,
            'voucher_id' => $voucher->voucher_id,
            'is_used' => false,
            'save_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', '🎉 Chúc mừng! Bạn đã đổi thành công mã voucher ' . $voucher->code . ' (-' . $pointsNeeded . ' điểm).');
    }

    // ==========================================
    // HIỂN THỊ TRANG ĐỔI MẬT KHẨU
    // ==========================================
    public function changePassword()
    {
        $user = Auth::user();
        return view('user.change_password', compact('user'));
    }

    // ==========================================
    // XỬ LÝ ĐỔI MẬT KHẨU
    // ==========================================
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.'
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác!']);
        }

        \Illuminate\Support\Facades\DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
                'updated_at' => now()
            ]);

        return back()->with('success', '🎉 Đổi mật khẩu thành công! Mật khẩu mới của bạn đã được cập nhật.');
    }

    // ==========================================
    // HIỂN THỊ TRANG VÍ SỐ DƯ HOÀN TIỀN
    // ==========================================
    public function wallet()
    {
        $user = Auth::user();
        
        // Lấy danh sách các đơn hàng đã hủy hoặc được hoàn tiền
        $refundedOrders = DB::table('orders')
            ->where('user_id', $user->user_id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('user.wallet', compact('user', 'refundedOrders'));
    }

    // ==========================================
    // HỦY ĐƠN HÀNG VÀ TỰ ĐỘNG HOÀN TIỀN VÀO VÍ
    // ==========================================
    public function cancelOrder(Request $request, $id)
    {
        $user = Auth::user();
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã bị hủy trước đó!');
        }

        if (in_array($order->status, ['completed', 'shipping'])) {
            return back()->with('error', 'Đơn hàng đang giao hoặc đã hoàn thành không thể hủy!');
        }

        $oldStatus = $order->status;

        // Cập nhật trạng thái đơn hàng sang cancelled
        DB::table('orders')->where('order_id', $id)->update([
            'status' => 'cancelled',
            'updated_at' => now()
        ]);

        // Hoàn trả 1 lượt voucher về kho user_vouchers nếu có sử dụng
        if (!empty($order->voucher_id) && !empty($user->user_id) && \Illuminate\Support\Facades\Schema::hasTable('user_vouchers')) {
            DB::table('user_vouchers')
                ->where('user_id', $user->user_id)
                ->where('voucher_id', $order->voucher_id)
                ->where('is_used', 1)
                ->limit(1)
                ->update(['is_used' => 0, 'updated_at' => now()]);

            DB::table('vouchers')->where('voucher_id', $order->voucher_id)->decrement('used_count');
        }

        // TÍNH TOÁN HOÀN TIỀN CẢ VÍ LẪN TIỀN MẶT/QR ĐÃ THANH TOÁN
        $usedWallet = (float)($order->used_wallet_amount ?? 0);
        $isPaid = ($oldStatus === 'processing' || $order->payment_method === 'qr');
        $cashPaid = $isPaid ? (float)$order->total_amount : 0;

        $totalRefund = $usedWallet + $cashPaid;
        $refundMsg = 'Đã hủy đơn hàng #' . $id . ' thành công!';

        if ($totalRefund > 0) {
            DB::table('users')->where('user_id', $user->user_id)->increment('wallet_balance', $totalRefund);
            $refundMsg = '🎉 Đơn hàng #' . $id . ' đã được hủy! Số tiền ' . number_format($totalRefund, 0, ',', '.') . 'đ (bao gồm tiền ví đã khấu trừ) đã được tự động hoàn về Ví nằm ở mục Tiền hoàn trong menu Tài khoản của bạn.';
        }

        return back()->with('success', $refundMsg);
    }
}