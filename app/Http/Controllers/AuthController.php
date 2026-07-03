<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Thêm thư viện này

class AuthController extends Controller
{
    // ==========================================
    // 1. XỬ LÝ ĐĂNG KÝ
    // ==========================================
    public function register(Request $request)
    {
        // Bước 1: Kiểm tra dữ liệu nhập vào (Validation thủ công)
        $validator = Validator::make($request->all(), [
            'register_identity' => 'required|string|min:3',
            'password' => 'required|string|min:6|confirmed', // confirmed yêu cầu phải có ô password_confirmation
        ], [
            'register_identity.required' => 'Vui lòng nhập số điện thoại hoặc tài khoản.',
            'register_identity.min' => 'Tài khoản phải có ít nhất 3 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.'
        ]);

        // Nếu người dùng nhập sai (mật khẩu ngắn, không khớp...), gom lỗi và trả về đúng biến 'register_error'
        if ($validator->fails()) {
            return back()->withErrors(['register_error' => $validator->errors()->first()])->withInput();
        }

        $identity = $request->register_identity;
        $isPhone = preg_match('/^[0-9]+$/', $identity); // Kiểm tra xem có phải toàn số (SĐT) không

        // Bước 2: Kiểm tra xem tên đăng nhập hoặc sđt đã tồn tại chưa trong Database
        $exists = User::where('name', $identity)->orWhere('phone', $identity)->exists();
        if ($exists) {
            return back()->withErrors(['register_error' => 'Tên đăng nhập hoặc Số điện thoại đã tồn tại!'])->withInput();
        }

        // Bước 3: Tạo tài khoản mới
        $user = User::create([
            'name' => $isPhone ? 'User_' . $identity : $identity, 
            'phone' => $isPhone ? $identity : null,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'point' => 0,
        ]);

        // Bước 4: Đăng nhập ngay lập tức cho người dùng
        // Kiểm tra mật khẩu (Dùng Hash::check)
        // Bên trong hàm login của AuthController.php
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            
            // Nếu là staff -> Vô POS
            if ($user->role === 'staff') {
                return redirect()->route('staff.pos');
            }

            // NẾU LÀ ADMIN -> BAY THẲNG VÀO TRANG QUẢN TRỊ
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }
        // Bước 5: Chuyển về trang chủ kèm theo tín hiệu để bật Pop-up Chào mừng
        return redirect('/')->with('show_welcome_modal', 'Đăng ký thành công!');
    }

    // ==========================================
    // 2. XỬ LÝ ĐĂNG NHẬP
    // ==========================================
    public function login(Request $request)
    {
        // 1. Chỉ yêu cầu nhập identity (có thể là email, sđt hoặc tên) và mật khẩu
        $request->validate([
            'login_identity' => 'required|string', // Lưu ý: Sửa trường name trong thẻ <input> HTML của bạn thành 'login_identity'
            'password' => 'required'
        ], [
            'login_identity.required' => 'Vui lòng nhập tài khoản, số điện thoại hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $identity = $request->login_identity;
        $password = $request->password;

        // 2. Tự động kiểm tra xem người dùng đang nhập cái gì
        $fieldType = 'name'; // Mặc định là tên tài khoản
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email'; // Nếu giống định dạng a@b.com thì là email
        } elseif (preg_match('/^[0-9]+$/', $identity)) {
            $fieldType = 'phone'; // Nếu toàn số thì là số điện thoại
        }

        // 3. Thực hiện đăng nhập dựa trên loại dữ liệu vừa nhận diện
        if (\Illuminate\Support\Facades\Auth::attempt([$fieldType => $identity, 'password' => $password], $request->has('remember'))) {
            $user = \Illuminate\Support\Facades\Auth::user();

            if ($user->is_locked) {
                \Illuminate\Support\Facades\Auth::logout();
                return back()->withErrors(['login_error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);
            }

            $userId = $user->user_id ?? $user->id; 

            // NẾU LÀ NHÂN VIÊN -> TỰ ĐỘNG CHECK-IN
            if ($user->role === 'staff') {
                $now = now('Asia/Ho_Chi_Minh');
                
                // Ghi nhận Attendance (Chấm công)
                $attendanceExists = \Illuminate\Support\Facades\DB::table('attendances')
                    ->where('user_id', $userId)
                    ->whereDate('date', $now->format('Y-m-d'))
                    ->whereNull('check_out')
                    ->exists();

                if (!$attendanceExists) {
                    \Illuminate\Support\Facades\DB::table('attendances')->insert([
                        'user_id' => $userId,
                        'date' => $now->format('Y-m-d'),
                        'check_in' => $now,
                        'scheduled_end_time' => $now->copy()->addHours(4), 
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }

                // Gắn vào Ca hiện hành
                $shiftIndex = floor($now->hour / 4) + 1;
                $startHour = ($shiftIndex - 1) * 4;
                $startTime = sprintf('%02d:00:00', $startHour);
                $endTime = ($startHour + 4 >= 24) ? '23:59:59' : sprintf('%02d:00:00', $startHour + 4);

                $shift = \App\Models\Shift::firstOrCreate(
                    ['date' => $now->format('Y-m-d'), 'start_time' => $startTime],
                    [
                        'name' => "Ca $shiftIndex (" . sprintf('%02d:00', $startHour) . " - " . sprintf('%02d:00', $startHour + 4 > 23 ? 0 : $startHour + 4) . ")",
                        'end_time' => $endTime
                    ]
                );

                if (!$shift->users->contains($userId)) {
                    $shift->users()->attach($userId);
                }

                return redirect()->route('staff.pos')->with('success', 'Đăng nhập & Check-in ca làm thành công!');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['login_error' => 'Tài khoản hoặc mật khẩu không chính xác.']);
    }
   // ==========================================
    // 3. XỬ LÝ ĐĂNG XUẤT
    // ==========================================
    public function logout(\Illuminate\Http\Request $request)
    {
        // 1. Đăng xuất tài khoản
        Auth::logout();

        // 2. Xóa sạch mọi rác trong phiên làm việc (Session) để chống kẹt lỗi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. Đá về trang đăng nhập
        return redirect('/dang-nhap')->with('success', 'Bạn đã đăng xuất ca làm việc an toàn!');
    }
}