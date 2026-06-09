<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // 1. NẾU LÀ NHÂN VIÊN (STAFF) -> GIỮ LẠI Ở QUẦY POS
                if (Auth::user()->role === 'staff') {
                    return redirect()->route('staff.pos');
                }
                
                // 2. NẾU LÀ ADMIN -> ĐƯA VỀ TRANG QUẢN TRỊ (Nếu sau này bạn làm)
                if (Auth::user()->role === 'admin') {
                    return redirect('/admin/dashboard');
                }

                // 3. KHÁCH HÀNG BÌNH THƯỜNG -> MỚI ĐÁ VỀ TRANG CHỦ
                return redirect('/'); // (Hoặc RouteServiceProvider::HOME)
            }
        }

        return $next($request);
    }
}
