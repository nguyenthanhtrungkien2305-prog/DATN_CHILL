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
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            
            // KIỂM TRA ROLE ĐỂ CHUYỂN HƯỚNG
            if ($user->role === 'admin') {
                // Nếu là Admin -> Cho thẳng vào trang Dashboard
                return redirect()->route('admin.dashboard');
            }
            
            // Nếu là User thường -> Đưa về trang chủ
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
        // Kiểm tra dữ liệu nhập vào
        $validator = Validator::make($request->all(), [
            'login_identity' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_identity.required' => 'Vui lòng nhập thông tin đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['login_error' => $validator->errors()->first()])->withInput();
        }

        $identity = $request->login_identity;

        // Tìm user bằng Tên hoặc SĐT
        $user = User::where('name', $identity)->orWhere('phone', $identity)->first();

        // Kiểm tra mật khẩu (Dùng Hash::check để so sánh mật khẩu mã hóa)
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            
            // Đăng nhập thành công, chuyển hướng về trang chủ
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        // Nếu sai tài khoản hoặc mật khẩu
        return back()->withErrors(['login_error' => 'Tài khoản hoặc mật khẩu không chính xác!'])->withInput();
    }

    // ==========================================
    // 3. XỬ LÝ ĐĂNG XUẤT
    // ==========================================
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}