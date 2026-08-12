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
        // 1. Nếu chưa đăng nhập -> Đẩy ra trang Đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        // 2. Cho phép cả Nhân viên (staff) và Quản trị viên (admin)
        if (!in_array(Auth::user()->role, ['staff', 'admin'])) {
            return redirect('/')->with('error', 'Bạn không có quyền vào quầy thu ngân!');
        }

        // 3. Đúng là nhân viên -> Cho phép đi tiếp vào trang POS
        return $next($request);
    }
}