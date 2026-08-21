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
    // QUẢN LÝ ĐỊA CHỈ NHẬN HÀNG
    // ==========================================
    public function addresses()
    {
        $user = Auth::user();
        $addresses = [];
        if ($user && $user->address) {
            $decoded = json_decode($user->address, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item)) {
                        $addresses[] = $item;
                    } else if (is_string($item)) {
                        $addresses[] = [
                            'name' => $user->name ?? '',
                            'phone' => $user->phone ?? '',
                            'district' => '',
                            'ward' => '',
                            'street' => $item,
                            'full_address' => $item
                        ];
                    }
                }
            } else if (is_string($user->address)) {
                $addresses[] = [
                    'name' => $user->name ?? '',
                    'phone' => $user->phone ?? '',
                    'district' => '',
                    'ward' => '',
                    'street' => $user->address,
                    'full_address' => $user->address
                ];
            }
        }

        return view('user.address', compact('user', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'phone.regex' => 'Số điện thoại không hợp lệ (gồm 10 chữ số bắt đầu bằng đầu số VN).',
            'district.required' => 'Vui lòng chọn Quận/Huyện tại TP.HCM.',
            'street.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        $user = Auth::user();
        $inputPhone = trim($request->phone);

        // Kiểm tra OTP nếu số điện thoại nhận hàng mới khác với SĐT tài khoản
        if ($inputPhone !== $user->phone) {
            $verifiedPhone = session('verified_phone');
            $isVerified = session('phone_otp_verified') && ($verifiedPhone === $inputPhone);
            if (!$isVerified) {
                return back()->with('error', 'Số điện thoại mới (' . $inputPhone . ') chưa được xác thực bằng mã OTP. Vui lòng xác thực mã OTP trước khi lưu!')->withInput();
            }
        }

        $addresses = [];
        if ($user->address) {
            $decoded = json_decode($user->address, true);
            $addresses = is_array($decoded) ? $decoded : [$user->address];
        }

        if (count($addresses) >= 5) {
            return back()->with('error', 'Bạn chỉ được lưu tối đa 5 địa chỉ nhận hàng!');
        }

        $name = trim($request->name);
        $district = trim($request->district);
        $ward = trim($request->ward ?? '');
        $street = trim($request->street);

        $fullParts = [];
        if ($street) $fullParts[] = $street;
        if ($ward) $fullParts[] = $ward;
        if ($district) $fullParts[] = $district;
        $fullParts[] = 'TP. Hồ Chí Minh';
        $fullAddressText = implode(', ', $fullParts);

        $addressObj = [
            'name' => $name,
            'phone' => $inputPhone,
            'district' => $district,
            'ward' => $ward,
            'street' => $street,
            'full_address' => $fullAddressText
        ];

        $addresses[] = $addressObj;

        DB::table('users')->where('user_id', $user->user_id)->update([
            'address' => json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        return back()->with('success', '🎉 Đã thêm địa chỉ nhận hàng mới thành công!');
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'phone.regex' => 'Số điện thoại không hợp lệ (gồm 10 chữ số bắt đầu bằng đầu số VN).',
            'district.required' => 'Vui lòng chọn Quận/Huyện tại TP.HCM.',
            'street.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        $user = Auth::user();
        $index = (int)$request->index;
        $inputPhone = trim($request->phone);

        if ($user->address) {
            $addresses = json_decode($user->address, true);
            if (is_array($addresses) && isset($addresses[$index])) {
                $oldAddr = $addresses[$index];
                $oldPhone = is_array($oldAddr) ? ($oldAddr['phone'] ?? '') : '';

                // Kiểm tra OTP nếu SĐT mới khác SĐT tài khoản VÀ khác SĐT cũ của địa chỉ này
                if ($inputPhone !== $user->phone && $inputPhone !== $oldPhone) {
                    $verifiedPhone = session('verified_phone');
                    $isVerified = session('phone_otp_verified') && ($verifiedPhone === $inputPhone);
                    if (!$isVerified) {
                        return back()->with('error', 'Số điện thoại mới (' . $inputPhone . ') chưa được xác thực bằng mã OTP. Vui lòng xác thực mã OTP trước khi cập nhật!')->withInput();
                    }
                }

                $name = trim($request->name);
                $district = trim($request->district);
                $ward = trim($request->ward ?? '');
                $street = trim($request->street);

                $fullParts = [];
                if ($street) $fullParts[] = $street;
                if ($ward) $fullParts[] = $ward;
                if ($district) $fullParts[] = $district;
                $fullParts[] = 'TP. Hồ Chí Minh';
                $fullAddressText = implode(', ', $fullParts);

                $addressObj = [
                    'name' => $name,
                    'phone' => $inputPhone,
                    'district' => $district,
                    'ward' => $ward,
                    'street' => $street,
                    'full_address' => $fullAddressText
                ];

                $addresses[$index] = $addressObj;

                DB::table('users')->where('user_id', $user->user_id)->update([
                    'address' => json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                return back()->with('success', '🎉 Cập nhật địa chỉ nhận hàng thành công!');
            }
        }

        return back()->with('error', 'Không thể tìm thấy địa chỉ cần sửa!');
    }

    public function deleteAddress(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $user = Auth::user();
        $index = (int)$request->index;

        if ($user->address) {
            $addresses = json_decode($user->address, true);
            if (is_array($addresses) && isset($addresses[$index])) {
                unset($addresses[$index]);
                DB::table('users')->where('user_id', $user->user_id)->update([
                    'address' => json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                return back()->with('success', '🗑️ Đã xóa địa chỉ thành công!');
            }
        }

        return back()->with('error', 'Không thể xóa địa chỉ này!');
    }

    public function setDefaultAddress(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $user = Auth::user();
        $index = (int)$request->index;

        if ($user->address) {
            $addresses = json_decode($user->address, true);
            if (is_array($addresses) && isset($addresses[$index])) {
                $targetAddress = $addresses[$index];
                unset($addresses[$index]);
                array_unshift($addresses, $targetAddress);

                DB::table('users')->where('user_id', $user->user_id)->update([
                    'address' => json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                return back()->with('success', '⭐ Đã đặt làm địa chỉ mặc định thành công!');
            }
        }

        return back()->with('error', 'Thao tác không hợp lệ!');
    }

    // ==========================================
    // HỦY ĐƠN HÀNG
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

        return back()->with('success', '🎉 Đơn hàng #' . $id . ' đã được hủy thành công!');
    }
}