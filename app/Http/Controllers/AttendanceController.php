<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 1. Hiển thị Trang Đăng ký Ca làm & Bảng Tổng hợp
    public function index()
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        
        // Lịch sử đăng ký
        $registrations = DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->orderBy('shift_date', 'desc')
            ->get();

        // TÍNH TỔNG SỐ GIỜ LÀM ĐƯỢC DUYỆT TRONG THÁNG HIỆN TẠI
        $totalHours = DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('shift_date', date('m'))
            ->sum('duration');

        // DANH SÁCH 9 CA LÀM THEO QUY ĐỊNH (06:00 - 22:00)
        $availableShifts = [
            '4_hours' => [
                ['name' => 'Ca 1 (Sáng)', 'start' => '06:00', 'end' => '10:00', 'val' => '06:00|4'],
                ['name' => 'Ca 2 (Trưa)', 'start' => '10:00', 'end' => '14:00', 'val' => '10:00|4'],
                ['name' => 'Ca 3 (Chiều)', 'start' => '14:00', 'end' => '18:00', 'val' => '14:00|4'],
                ['name' => 'Ca 4 (Tối)', 'start' => '18:00', 'end' => '22:00', 'val' => '18:00|4'],
            ],
            '8_hours' => [
                ['name' => 'Sáng - Trưa', 'start' => '06:00', 'end' => '14:00', 'val' => '06:00|8'],
                ['name' => 'Trưa - Chiều', 'start' => '10:00', 'end' => '18:00', 'val' => '10:00|8'],
                ['name' => 'Chiều - Tối', 'start' => '14:00', 'end' => '22:00', 'val' => '14:00|8'],
            ],
            '12_hours' => [
                ['name' => 'Sáng - Chiều', 'start' => '06:00', 'end' => '18:00', 'val' => '06:00|12'],
                ['name' => 'Trưa - Tối', 'start' => '10:00', 'end' => '22:00', 'val' => '10:00|12'],
            ]
        ];

        return view('staff.shift_register', compact('registrations', 'totalHours', 'availableShifts'));
    }

    // 2. Xử lý lưu đơn Đăng ký Ca (Đã cập nhật)
    public function storeRegistration(Request $request)
    {
        $request->validate([
            'shift_date' => 'required|date',
            'shift_select' => 'required|string' // Lấy value dạng "Giờ_bắt_đầu|Số_tiếng"
        ]);

        // Cắt chuỗi để lấy thời gian và độ dài ca
        $parts = explode('|', $request->shift_select);
        $startTime = $parts[0];
        $duration = $parts[1];

        DB::table('shift_registrations')->insert([
            'user_id' => auth()->user()->user_id ?? auth()->id(),
            'shift_date' => $request->shift_date,
            'start_time' => $startTime,
            'duration' => $duration,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã đăng ký ca làm thành công, đang chờ Quản lý duyệt!');
    }

    // 3. API XỬ LÝ CHECK-OUT (Bắt lỗi về sớm 3h30p - Giữ nguyên như cũ)
    public function checkOut(Request $request)
    {
        $now = now('Asia/Ho_Chi_Minh');
        $userId = auth()->user()->user_id ?? auth()->id();

        $attendance = DB::table('attendances')->where('user_id', $userId)->whereDate('date', $now->format('Y-m-d'))->whereNull('check_out')->first();
        if (!$attendance) return response()->json(['success' => false, 'message' => 'Lỗi: Bạn chưa Check-in!']);

        $scheduledEnd = Carbon::parse($attendance->scheduled_end_time);
        
        if ($request->has('reason') && !empty($request->reason)) {
            DB::table('attendances')->where('id', $attendance->id)->update(['check_out' => $now, 'checkout_note' => $request->reason, 'updated_at' => $now]);
            return response()->json(['success' => true, 'message' => 'Đã ghi nhận lý do. Chào tạm biệt!']);
        }

        if ($now->lessThan($scheduledEnd)) {
            $diffInMinutes = $now->diffInMinutes($scheduledEnd); 
            if ($diffInMinutes >= 210) { 
                return response()->json(['require_reason' => true, 'message' => "Bạn đang rời ca sớm " . floor($diffInMinutes/60) . "h" . ($diffInMinutes%60) . "m. Hệ thống yêu cầu lý do!"]);
            }
        }

        DB::table('attendances')->where('id', $attendance->id)->update(['check_out' => $now, 'updated_at' => $now]);
        return response()->json(['success' => true, 'message' => 'Check-out thành công. Nghỉ ngơi nhé!']);
    }
    // API XỬ LÝ CHECK-IN (Bắt đầu ca)
    public function checkIn(Request $request)
    {
        $now = now('Asia/Ho_Chi_Minh');
        $userId = auth()->user()->user_id ?? auth()->id();

        // 1. Kiểm tra xem hôm nay đã Check-in chưa
        $exists = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereDate('date', $now->format('Y-m-d'))
            ->whereNull('check_out')
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Bạn đã Check-in rồi, đang trong ca làm việc!']);
        }

        // 2. Lưu vào bảng Attendances
        DB::table('attendances')->insert([
            'user_id' => $userId,
            'date' => $now->format('Y-m-d'),
            'check_in' => $now,
            // Tạm tính giờ kết thúc ca là 4 tiếng sau (Bạn có thể map với bảng Đăng ký ca sau)
            'scheduled_end_time' => $now->copy()->addHours(4), 
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // 3. Gắn tên nhân viên vào Ca 4 tiếng hiện tại (Để chia hoa hồng)
        $shiftIndex = floor($now->hour / 4) + 1;
        $startHour = ($shiftIndex - 1) * 4;
        $startTime = sprintf('%02d:00:00', $startHour);
        $endTime = ($startHour + 4 == 24) ? '23:59:59' : sprintf('%02d:00:00', $startHour + 4);
        
        $shift = \App\Models\Shift::firstOrCreate(
            ['date' => $now->format('Y-m-d'), 'start_time' => $startTime],
            [
                'name' => "Ca $shiftIndex (" . sprintf('%02d:00', $startHour) . " - " . sprintf('%02d:00', $startHour + 4 > 23 ? 0 : $startHour + 4) . ")",
                'end_time' => $endTime
            ]
        );

        if (!$shift->users->contains($userId)) {
            $shift->users()->attach($userId);
        }

        return response()->json(['success' => true, 'message' => 'Check-in thành công! Bắt đầu tính giờ làm.']);
    }
}