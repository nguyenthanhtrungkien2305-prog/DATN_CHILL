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
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // File ảnh, tối đa 2MB
        ]);

        $user = auth()->user(); // Hoặc DB::table('users')->where('user_id', $id)->first() tùy logic của bạn
        
        // 2. Mảng dữ liệu cần update
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
    public function cancelOrder($id)
    {
        $userId = auth()->user()->user_id ?? auth()->id();

        // Dùng DB::table thay vì App\Models\Order để đồng bộ với code của bạn
        $order = \Illuminate\Support\Facades\DB::table('orders')
                    ->where('order_id', $id)
                    ->where('user_id', $userId)
                    ->first();

        // Nếu không tìm thấy đơn hàng của người này
        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        // Chỉ cho phép hủy khi đơn hàng đang ở trạng thái chờ xác nhận
        if ($order->status == 'pending') {
            \Illuminate\Support\Facades\DB::table('orders')
                ->where('order_id', $id)
                ->update([
                    'status' => 'canceled', 
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
                
            return back()->with('success', 'Đã hủy đơn hàng #' . $id . ' thành công.');
        }

        return back()->with('error', 'Không thể hủy đơn hàng này do quán đã bắt đầu chuẩn bị đồ uống!');
    }
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

        // 2. Lấy danh sách Voucher ĐỔI ĐIỂM (công khai trên sàn đổi điểm)
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

        // 3. Lấy kho voucher của người dùng này (gồm voucher đổi điểm + voucher cá nhân được gán)
        $rawMyVouchers = \Illuminate\Support\Facades\DB::table('user_vouchers')
            ->join('vouchers', 'user_vouchers.voucher_id', '=', 'vouchers.voucher_id')
            ->where('user_vouchers.user_id', $userId)
            ->select('user_vouchers.*', 'vouchers.code', 'vouchers.discount_type', 'vouchers.discount_value', 'vouchers.min_order', 'vouchers.end_date', 'vouchers.assigned_user_id')
            ->orderBy('user_vouchers.id', 'desc')
            ->get();

        // Gom nhóm theo voucher_id để đếm số lượng khả dụng (x1, x2, x3...)
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

        // 4. Lấy lịch sử tích điểm từ các đơn hàng hoàn thành
        $completedOrders = \Illuminate\Support\Facades\DB::table('orders')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Lấy lịch sử đổi Voucher bằng điểm của người dùng
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

        return view('user.points', compact('user', 'availableVouchers', 'myVouchers', 'completedOrders', 'redeemHistory'));
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
}