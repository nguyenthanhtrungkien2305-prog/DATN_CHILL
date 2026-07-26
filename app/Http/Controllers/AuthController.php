<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
use Illuminate\Support\Str;
=======
>>>>>>> main

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
            
            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
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

<<<<<<< HEAD
        // 3. Thực hiện đăng nhập dựa trên loại dữ liệu vừa nhận diện
        if (\Illuminate\Support\Facades\Auth::attempt([$fieldType => $identity, 'password' => $password], $request->has('remember'))) {
            $user = \Illuminate\Support\Facades\Auth::user();

            if ($user->is_locked) {
                \Illuminate\Support\Facades\Auth::logout();
                return back()->withErrors(['login_error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);
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
=======
        if (Auth::attempt([$fieldType => $identity, 'password' => $password], $request->has('remember'))) {
            $user = Auth::user();
>>>>>>> main

            if ($user->role === 'staff') {
                // ĐÃ XÓA SẠCH CODE "LÉN" CHECK-IN Ở ĐÂY.
                // Bây giờ đăng nhập xong sẽ bị đá ra trang Lịch, phải tự tay bấm Check-in!
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
    public function logout(\Illuminate\Http\Request $request)
    {
<<<<<<< HEAD
        // 1. Đọc giỏ hàng tài khoản từ Cache trước khi đăng xuất
        $userId   = Auth::id();
        $userCart = $userId ? Cache::get('cart:u:' . $userId, []) : [];

        // 2. Đăng xuất & invalidate session (chống session fixation)
=======
        if (Auth::check()) {
            $userId = Auth::user()->user_id ?? Auth::id();
            
            // Ép Check-out an toàn trước khi thoát
            \Illuminate\Support\Facades\DB::table('attendances')
                ->where('user_id', $userId)
                ->whereNull('check_out')
                ->update([
                    'check_out' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
        }

>>>>>>> main
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

<<<<<<< HEAD
        // 3. Chuyển giỏ hàng sang guest cart với cookie mới 24h
        if (!empty($userCart)) {
            $guestToken = $request->cookie('cart_token') ?: Str::uuid()->toString();
            Cache::put('cart:g:' . $guestToken, $userCart, now()->addHours(24));
            Cookie::queue('cart_token', $guestToken, 60 * 24); // 24 giờ
        }

        return redirect('/dang-nhap')->with('success', 'Bạn đã đăng xuất an toàn!');
=======
        return redirect('/dang-nhap')->with('success', 'Đã đăng xuất và hệ thống đã tự động chốt ca an toàn!');
>>>>>>> main
    }
}