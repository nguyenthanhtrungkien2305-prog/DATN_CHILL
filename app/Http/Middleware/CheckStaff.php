<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước!');
        }

        // 2. Kiểm tra xem có đúng vai trò là 'staff' không
        if (Auth::user()->role !== 'staff') {
            // Nếu là admin thì có thể cho qua hoặc đá về trang admin tùy bạn, ở đây chặn tuyệt đối nếu không phải staff
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn không có quyền truy cập quầy POS!');
        }

        return $next($request);
    }
}