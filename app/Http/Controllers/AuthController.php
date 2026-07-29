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
                return redirect()->route('staff.shifts'); // Đổi hướng về trang Lịch để họ tự Check-in
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/')->with('success', 'Đăng nhập thành công!');
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

            if ($user->role === 'staff') {
                // ĐÃ XÓA SẠCH CODE "LÉN" CHECK-IN Ở ĐÂY.
                // Bây giờ đăng nhập xong sẽ bị đá ra trang Lịch, phải tự tay bấm Check-in!
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
}