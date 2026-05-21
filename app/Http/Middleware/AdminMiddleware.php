<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
   public function handle(Request $request, Closure $next): Response
    {
        // Nếu là Admin thì cho qua
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Nếu là người dùng thường cố tình gõ link /admin -> Đá về trang chủ kèm biến cảnh báo
        return redirect('/')->with('access_denied', 'CẢNH BÁO: Bạn không có quyền truy cập vào khu vực Quản trị hệ thống!');
    }
}