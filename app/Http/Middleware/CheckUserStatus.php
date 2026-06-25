<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu người dùng đã đăng nhập VÀ trạng thái tài khoản KHÁC 1 (bị khóa)
        if (Auth::check() && Auth::user()->status != 1) {
            Auth::logout(); // Ép buộc đăng xuất

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Đá về trang login kèm thông báo lỗi
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa bới Admin!');
        }

        return $next($request);
    }
}
