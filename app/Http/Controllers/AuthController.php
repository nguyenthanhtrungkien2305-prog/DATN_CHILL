<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ==========================================
    // 1. XỬ LÝ ĐĂNG KÝ
    // ==========================================
    public function register(Request $request)
    {
        $identity = $request->register_identity ?? $request->name ?? $request->email ?? $request->phone;
        if (!$request->has('register_identity') && $identity) {
            $request->merge(['register_identity' => $identity]);
        }

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

        $exists = User::where('name', $identity)->orWhere('phone', $identity)->orWhere('email', $identity)->exists();
        if ($exists) {
            return back()->withErrors(['register_error' => 'Tên đăng nhập, Email hoặc Số điện thoại đã tồn tại!'])->withInput();
        }

        $user = User::create([
            'name' => $isPhone ? 'User_' . $identity : $identity,
            'email' => filter_var($identity, FILTER_VALIDATE_EMAIL) ? $identity : ($request->email ?? null),
            'phone' => $isPhone ? $identity : ($request->phone ?? null),
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
            
            return redirect('/')->with('show_post_register_popup', true)->with('success', 'Đăng ký tài khoản thành công!');
        }

        return back()->withErrors(['register_error' => 'Đăng ký thất bại, vui lòng thử lại.']);
    }

    // ==========================================
    // 2. XỬ LÝ ĐĂNG NHẬP
    // ==========================================
    public function login(Request $request)
    {
        $identity = $request->login_identity ?? $request->email ?? $request->phone ?? $request->name;
        if (!$request->has('login_identity') && $identity) {
            $request->merge(['login_identity' => $identity]);
        }

        $request->validate([
            'login_identity' => 'required|string', 
            'password' => 'required'
        ], [
            'login_identity.required' => 'Vui lòng nhập tài khoản, số điện thoại hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $identity = trim($identity);
        $password = trim($request->password);

        $user = User::where('name', $identity)
            ->orWhere('phone', $identity)
            ->orWhere('email', $identity)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // Kiểm tra nếu tài khoản bị khóa
            if (!empty($user->is_locked)) {
                return back()->withErrors(['login_error' => '🔒 Tài khoản của bạn đã bị KHÓA bởi Quản trị viên! Vui lòng liên hệ hỗ trợ.'])->withInput();
            }

            Auth::login($user, $request->has('remember'));

            if ($user->role === 'staff') {
                return redirect()->route('staff.shifts')->with('success', 'Đăng nhập thành công! Vui lòng Check-in để mở khóa POS.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['login_error' => 'Tài khoản hoặc mật khẩu không chính xác.']);
    }

    // ==========================================
    // 3. XỬ LÝ ĐĂNG XUẤT (CÓ TỰ ĐỘNG CHỐT CA)
    // ==========================================
    public function logout(\Illuminate\Http\Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::user()->user_id ?? Auth::id();
            
            // 1. Tự động kết ca nếu ca đã quá giờ làm quy định
            \App\Http\Controllers\AttendanceController::autoCheckOutExpiredShifts($userId);

            // 2. Ép Check-out an toàn các ca còn lại nếu người dùng chủ động đăng xuất
            \Illuminate\Support\Facades\DB::table('attendances')
                ->where('user_id', $userId)
                ->whereNull('check_out')
                ->update([
                    'check_out' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dang-nhap')->with('success', 'Đã đăng xuất và hệ thống đã tự động chốt ca an toàn!');
    }

    // ==========================================
    // 4. XỬ LÝ GỬI VÀ XÁC NHẬN OTP SMS CHO SỐ ĐIỆN THOẠI
    // ==========================================
    public function sendPhoneOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/']
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ (phải gồm 10 chữ số bắt đầu bằng đầu số VN).'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $phone = $request->phone;
        
        // Kiểm tra SĐT đã tồn tại chưa nếu là luồng đăng ký
        if ($request->check_exists && User::where('phone', $phone)->exists()) {
            return response()->json(['success' => false, 'message' => 'Số điện thoại này đã được sử dụng cho một tài khoản khác!'], 400);
        }

        $otp = (string)rand(100000, 999999);

        // Lưu thông tin OTP vào Session (hết hạn sau 5 phút)
        session([
            'phone_otp' => $otp,
            'pending_phone' => $phone,
            'phone_otp_expires_at' => now()->addMinutes(5)
        ]);

        // Gửi OTP qua SMS Service (TextBee / Twilio / Log System)
        \App\Services\SmsService::sendOtp($phone, $otp);

        $hasTextBee = !empty(env('TEXTBEE_API_KEY'));
        $msg = $hasTextBee 
            ? "Mã OTP (6 chữ số) đã được gửi qua SMS đến số điện thoại {$phone}!"
            : "Mã OTP (6 chữ số) đã được tạo! (Mã thử nghiệm: {$otp})";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'demo_otp' => !$hasTextBee ? $otp : null
        ]);
    }

    public function verifyPhoneOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6'
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP.',
            'otp_code.size' => 'Mã OTP phải gồm 6 chữ số.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $sessionOtp = session('phone_otp');
        $pendingPhone = session('pending_phone');
        $expiresAt = session('phone_otp_expires_at');

        if (!$sessionOtp || !$pendingPhone || !$expiresAt) {
            return response()->json(['success' => false, 'message' => 'Mã OTP chưa được khởi tạo. Vui lòng yêu cầu gửi lại mã!'], 400);
        }

        if (now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn (5 phút). Vui lòng yêu cầu gửi lại mã mới!'], 400);
        }

        if ($request->otp_code !== (string)$sessionOtp) {
            return response()->json(['success' => false, 'message' => 'Mã OTP không chính xác. Vui lòng kiểm tra lại!'], 400);
        }

        // Đánh dấu đã xác thực thành công số điện thoại
        session(['phone_otp_verified' => true, 'verified_phone' => $pendingPhone]);

        // NẾU NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (Cập nhật từ trang Hồ sơ) -> Cập nhật trực tiếp CSDL
        if (Auth::check()) {
            $userId = Auth::user()->user_id ?? Auth::id();
            \Illuminate\Support\Facades\DB::table('users')->where('user_id', $userId)->update([
                'phone' => $pendingPhone,
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '🎉 Xác thực & Cập nhật Số điện thoại thành công!',
            'phone' => $pendingPhone
        ]);
    }

    // ==========================================
    // 5. XỬ LÝ GỬI VÀ XÁC NHẬN OTP QUÊN MẬT KHẨU QUA GMAIL
    // ==========================================
    public function sendForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $email = $request->email;
        $user = User::where('email', $email)->orWhere(function($query) use ($email) {
            $query->where('name', $email)->whereNotNull('email');
        })->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email này chưa được đăng ký trong hệ thống.'], 404);
        }

        $otp = (string)rand(100000, 999999);

        // Lưu OTP vào Session
        session([
            'forgot_email' => $user->email,
            'forgot_otp' => $otp,
            'forgot_otp_expires_at' => now()->addMinutes(5)
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtpMail($otp, $user->name));

            return response()->json([
                'success' => true,
                'message' => "Mã OTP xác minh đã được gửi đến email: {$user->email}. Vui lòng kiểm tra hộp thư!"
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi email OTP Quên Mật Khẩu: ' . $e->getMessage());

            // Backup gửi log nếu mail gặp sự cố kết nối
            return response()->json([
                'success' => true,
                'message' => "Mã OTP đã được tạo (Mã thử nghiệm: {$otp}). Lưu ý: Cần kiểm tra lại kết nối Mail.",
                'demo_otp' => $otp
            ]);
        }
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP.',
            'otp_code.size' => 'Mã OTP gồm 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải từ 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $forgotEmail = session('forgot_email');
        $forgotOtp = session('forgot_otp');
        $expiresAt = session('forgot_otp_expires_at');

        if (!$forgotEmail || !$forgotOtp || !$expiresAt) {
            return response()->json(['success' => false, 'message' => 'Mã OTP chưa được gửi hoặc phiên làm việc đã hết hạn. Vui lòng thử lại!'], 400);
        }

        if (now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn (5 phút). Vui lòng yêu cầu gửi lại mã mới!'], 400);
        }

        if ($request->otp_code !== (string)$forgotOtp) {
            return response()->json(['success' => false, 'message' => 'Mã OTP không chính xác. Vui lòng kiểm tra lại Email!'], 400);
        }

        // Đổi mật khẩu trong CSDL
        $user = User::where('email', $forgotEmail)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa Session OTP
        session()->forget(['forgot_email', 'forgot_otp', 'forgot_otp_expires_at']);

        return response()->json([
            'success' => true,
            'message' => '🎉 Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.'
        ]);
    }
}