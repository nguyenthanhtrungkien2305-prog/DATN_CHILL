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
        
        // Tự động kết ca nếu đã hết giờ làm việc
        \App\Http\Controllers\AttendanceController::autoCheckOutExpiredShifts($userId);
        
        // KIỂM TRA NGHIÊM NGẶT: Phải có ca làm việc đang mở (chưa check out)
        $hasActiveShift = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereNull('check_out')
            ->exists();

        if (!$hasActiveShift) {
            // Nếu gọi qua API (Bấm hoàn thành đơn, v.v.)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => '⏰ HẾT GIỜ LÀM VIỆC: Bạn đã bị tự động Check-out!'], 403);
            }
            // Nếu gõ URL trực tiếp, đá văng ra ngoài
            return redirect()->route('staff.shifts')->with('error', '🔒 HẾT GIỜ LÀM VIỆC HOẶC CHƯA CHECK-IN! Hệ thống đã tự động kết ca.');
        }

        return $next($request);
    }
}