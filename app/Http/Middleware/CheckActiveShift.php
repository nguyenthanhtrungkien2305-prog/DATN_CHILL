<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckActiveShift
{
    public function handle(Request $request, Closure $next)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        
        // 1. Quản trị viên (Admin) -> Được phép ra vào POS tự do không cần Check-in
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // 2. Tự động kết ca nếu ca đã hết giờ làm việc
        if ($userId) {
            \App\Http\Controllers\AttendanceController::autoCheckOutExpiredShifts($userId);
        }

        // 3. Kiểm tra xem Nhân viên đã Check-in ca làm việc chưa
        $hasActiveShift = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereNull('check_out')
            ->exists();

        if (!$hasActiveShift) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => '🔒 CHƯA CHECK-IN: Bạn cần Check-in để truy cập quầy bán hàng!'], 403);
            }
            return redirect()->route('staff.shifts')->with('error', '🔒 CHƯA CHECK-IN: Vui lòng Check-in (khi có lịch duyệt) để vào quầy bán hàng POS!');
        }

        return $next($request);
    }
}