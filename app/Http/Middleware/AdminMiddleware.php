<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
   public function handle(Request $request, Closure $next)
{
    // Kiểm tra xem đã đăng nhập chưa
    if (!auth()->check()) {
        return redirect('/dang-nhap');
    }

    // Kiểm tra role có phải là admin không
    if (auth()->user()->role !== 'admin') {
        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Admin!');
    }

    return $next($request);
}
}