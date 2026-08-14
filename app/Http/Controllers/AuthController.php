<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ==========================================
    // 1. XỬ LÝ ĐĂNG KÝ
    // ==========================================
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'register_identity' => 'required|string|min:3',
            'password' => 'required|string|min:6|confirmed', 
        ], [
            'register_identity.required' => 'Vui lòng nhập số điện thoại hoặc tài khoản.',
            'register_identity.min' => 'Tài khoản phải có ít nhất 3 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['register_error' => $validator->errors()->first()])->withInput();
        }

        $identity = $request->register_identity;
        $isPhone = preg_match('/^[0-9]+$/', $identity); 

        $exists = User::where('name', $identity)->orWhere('phone', $identity)->exists();
        if ($exists) {
            return back()->withErrors(['register_error' => 'Tên đăng nhập hoặc Số điện thoại đã tồn tại!'])->withInput();
        }

        $user = User::create([
            'name' => $isPhone ? 'User_' . $identity : $identity, 
            'phone' => $isPhone ? $identity : null,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'point' => 0,
        ]);

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            
            if ($user->role === 'staff') {
                return redirect()->route('staff.shifts');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/')->with('show_welcome_modal', 'Đăng ký thành công!');
        }
        return redirect('/')->with('show_welcome_modal', 'Đăng ký thành công!');
    }

    // ==========================================
    // 2. XỬ LÝ ĐĂNG NHẬP
    // ==========================================
    public function login(Request $request)
    {
        $request->validate([
            'login_identity' => 'required|string', 
            'password' => 'required'
        ], [
            'login_identity.required' => 'Vui lòng nhập tài khoản, số điện thoại hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $identity = $request->login_identity;
        $password = $request->password;

        $fieldType = 'name'; 
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email'; 
        } elseif (preg_match('/^[0-9]+$/', $identity)) {
            $fieldType = 'phone'; 
        }

        if (Auth::attempt([$fieldType => $identity, 'password' => $password], $request->has('remember'))) {
            $user = Auth::user();

            // Kiểm tra nếu tài khoản bị khóa
            if (!empty($user->is_locked)) {
                Auth::logout();
                return back()->withErrors(['login_error' => '🔒 Tài khoản của bạn đã bị KHÓA bởi Quản trị viên! Vui lòng liên hệ hỗ trợ.'])->withInput();
            }

            $userId = $user->user_id ?? $user->id;

            // Merge giỏ hàng khách vãng lai vào giỏ hàng tài khoản (Cache-based, 24h TTL)
            $guestToken = $request->cookie('cart_token');
            if ($guestToken) {
                $guestCartKey = 'cart:g:' . $guestToken;
                $guestCart    = Cache::get($guestCartKey, []);
                if (!empty($guestCart)) {
                    $userCartKey = 'cart:u:' . $userId;
                    $userCart    = Cache::get($userCartKey, []);
                    foreach ($guestCart as $key => $item) {
                        if (isset($userCart[$key])) {
                            $userCart[$key]['quantity'] += $item['quantity'];
                        } else {
                            $userCart[$key] = $item;
                        }
                    }
                    Cache::put($userCartKey, $userCart, now()->addHours(24));
                    Cache::forget($guestCartKey);
                }
            }

            if ($user->role === 'staff') {
                return redirect()->route('staff.shifts')->with('success', 'Đăng nhập thành công! Vui lòng Check-in để mở khóa POS.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['login_error' => 'Tài khoản hoặc mật khẩu không chính xác.']);
    }

    // ==========================================
    // 3. XỬ LÝ ĐĂNG XUẤT (CÓ TỰ ĐỘNG CHỐT CA)
    // ==========================================
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::user()->user_id ?? Auth::id();
            
            if (class_exists('\App\Http\Controllers\AttendanceController') && method_exists('\App\Http\Controllers\AttendanceController', 'autoCheckOutExpiredShifts')) {
                \App\Http\Controllers\AttendanceController::autoCheckOutExpiredShifts($userId);
            }

            \Illuminate\Support\Facades\DB::table('attendances')
                ->where('user_id', $userId)
                ->whereNull('check_out')
                ->update([
                    'check_out' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
        }

        $userId   = Auth::id();
        $userCart = $userId ? Cache::get('cart:u:' . $userId, []) : [];

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (!empty($userCart)) {
            $guestToken = $request->cookie('cart_token') ?: Str::uuid()->toString();
            Cache::put('cart:g:' . $guestToken, $userCart, now()->addHours(24));
            Cookie::queue('cart_token', $guestToken, 60 * 24);
        }

        return redirect('/dang-nhap')->with('success', 'Bạn đã đăng xuất an toàn!');
    }

    // ==========================================
    // 4. CÁC HÀM XỬ LÝ QUÊN MẬT KHẨU KẾT NỐI GMAIL
    // ==========================================
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Địa chỉ Email không hợp lệ.'
        ]);

        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'Không tìm thấy tài khoản nào gắn với địa chỉ Email này!')->withInput();
        }

        $token = \Illuminate\Support\Str::random(60);

        // Lưu vào bảng password_reset_tokens
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Thử gửi Email qua Mailer (Gmail SMTP)
        try {
            \Illuminate\Support\Facades\Mail::send('emails.reset_password', [
                'token' => $token,
                'user' => $user
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Khôi Phục Mật Khẩu Tài Khoản - Chill Chill Coffee');
            });

            return back()->with('success', "🎉 Liên kết khôi phục mật khẩu đã được gửi đến Gmail: {$email}. Vui lòng kiểm tra hộp thư!");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Reset Password Mail Error: " . $e->getMessage());
            
            // Nếu Mailer chưa cấu hình SMTP, trả về kèm liên kết trực tiếp để test tiện lợi
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
            return back()->with('success', "🎉 Đã tạo liên kết khôi phục cho Email {$email}!<br><br><a href='{$resetUrl}' class='inline-block px-4 py-2.5 bg-coral text-white rounded-full font-bold text-xs shadow-md hover:bg-[#d5523b] transition-all my-1'>👉 Bấm vào đây để Đặt lại mật khẩu ngay</a>");
        }
    }

    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset_password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.'
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->with('error', 'Mã Token khôi phục không hợp lệ hoặc đã được sử dụng!');
        }

        // Kiểm tra thời hạn 15 phút
        if (\Carbon\Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            return back()->with('error', 'Liên kết khôi phục đã hết hạn (quá 15 phút). Vui lòng yêu cầu lại!');
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Không tìm thấy tài khoản người dùng!');
        }

        // Đổi mật khẩu
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // Xóa Token đã dùng
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', '🎉 Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.');
    }
}