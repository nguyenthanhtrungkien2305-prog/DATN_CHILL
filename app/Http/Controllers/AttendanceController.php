<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ==========================================
    // 1. Hiển thị Trang Đăng ký & Bảng Lịch
    // ==========================================
    public function index()
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        
        $registrations = DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->orderBy('shift_date', 'desc')
            ->get();

        $totalHours = DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('shift_date', date('m'))
            ->sum('duration');

        // THUẬT TOÁN BƯỚC NHẢY THỜI GIAN (XỬ LÝ CUỐI TUẦN)
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        
        // Nếu qua ngày Thứ 7 (Tức là hôm nay là Chủ Nhật)
        if ($now->dayOfWeek == Carbon::SUNDAY) {
            $startOfWeek = $now->copy()->next(Carbon::MONDAY);
            $isNextWeek = true; 
        } else {
            $startOfWeek = $now->copy()->startOfWeek(); 
            $isNextWeek = false;
        }
        
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $allWeekRegs = DB::table('shift_registrations')
            ->whereBetween('shift_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        $slotCounts = [];
        foreach($allWeekRegs as $reg) {
            $date = $reg->shift_date;
            $startH = (int)Carbon::parse($reg->start_time)->format('H');
            $duration = (int)$reg->duration;
            
            $slotsCovered = $duration / 4;
            for($i = 0; $i < $slotsCovered; $i++) {
                $h = $startH + ($i * 4);
                if(!isset($slotCounts[$date][$h])) $slotCounts[$date][$h] = 0;
                $slotCounts[$date][$h]++; 
            }
        }

        $availableShifts = [
            '4_hours' => [
                ['name' => 'Ca Sáng', 'time' => '06:00 - 10:00', 'val' => '06:00|4'],
                ['name' => 'Ca Trưa', 'time' => '10:00 - 14:00', 'val' => '10:00|4'],
                ['name' => 'Ca Chiều', 'time' => '14:00 - 18:00', 'val' => '14:00|4'],
                ['name' => 'Ca Tối', 'time' => '18:00 - 22:00', 'val' => '18:00|4'],
            ],
            '8_hours' => [
                ['name' => 'Sáng - Trưa', 'time' => '06:00 - 14:00', 'val' => '06:00|8'],
                ['name' => 'Trưa - Chiều', 'time' => '10:00 - 18:00', 'val' => '10:00|8'],
                ['name' => 'Chiều - Tối', 'time' => '14:00 - 22:00', 'val' => '14:00|8'],
            ],
            '12_hours' => [
                ['name' => 'Sáng - Chiều', 'time' => '06:00 - 18:00', 'val' => '06:00|12'],
                ['name' => 'Trưa - Tối', 'time' => '10:00 - 22:00', 'val' => '10:00|12'],
            ]
        ];

        return view('staff.shift_register', compact('registrations', 'totalHours', 'availableShifts', 'slotCounts', 'startOfWeek', 'isNextWeek'));
    }

    // ==========================================
    // 2. Xử lý lưu đơn Đăng ký Ca & KHÓA NẾU ĐẦY
    // ==========================================
    public function storeRegistration(Request $request)
    {
        $request->validate([
            'shift_date' => 'required|date',
            'shift_select' => 'required|string'
        ]);

        $shiftDate = $request->shift_date;
        $parts = explode('|', $request->shift_select);
        $startTime = $parts[0];
        $duration = (int)$parts[1];
        $startH = (int)Carbon::parse($startTime)->format('H');

        $existingRegs = DB::table('shift_registrations')
            ->where('shift_date', $shiftDate)
            ->whereIn('status', ['approved', 'pending'])
            ->get();
            
        $slotsCovered = $duration / 4;
        for($i = 0; $i < $slotsCovered; $i++) {
            $currentSlotH = $startH + ($i * 4);
            $countInSlot = 0;
            foreach($existingRegs as $reg) {
                $rStart = (int)Carbon::parse($reg->start_time)->format('H');
                $rDur = (int)$reg->duration;
                if ($rStart <= $currentSlotH && ($rStart + $rDur) > $currentSlotH) {
                    $countInSlot++;
                }
            }
            
            if ($countInSlot >= 4) {
                return back()->with('error', "Rất tiếc! Khung giờ ".sprintf('%02d:00', $currentSlotH)." ngày " . Carbon::parse($shiftDate)->format('d/m/Y') . " đã đủ 4/4 nhân viên. Vui lòng chọn ca khác!")->with('active_tab', 'register');
            }
        }

        DB::table('shift_registrations')->insert([
            'user_id' => auth()->user()->user_id ?? auth()->id(),
            'shift_date' => $shiftDate,
            'start_time' => $startTime,
            'duration' => $duration,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã đăng ký ca làm thành công, đang chờ Quản lý duyệt!')->with('active_tab', 'register');
    }

    // ==========================================
    // 3. API XỬ LÝ CHECK-IN (Bắt đầu ca) - BẢN KIỂM TRA GIỜ NGHIÊM NGẶT
    // ==========================================
    public function checkIn(Request $request)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        $now = now('Asia/Ho_Chi_Minh');

        // 1. Kiểm tra xem có đang kẹt ca nào chưa kết thúc không
        $activeShift = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $userId)
            ->whereNull('check_out')
            ->first();

        if ($activeShift) {
            return response()->json([
                'success' => false, 
                'message' => 'Bạn đang ở trong một ca làm việc rồi. Hãy kết ca cũ trước!'
            ]);
        }

        // 2. Lấy danh sách CÁC CA ĐƯỢC DUYỆT trong ngày hôm nay
        $todayShifts = \Illuminate\Support\Facades\DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('shift_date', $now->format('Y-m-d'))
            ->get();

        // Nếu hôm nay không có lịch nào được duyệt
        if ($todayShifts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'TỪ CHỐI: Bạn không có lịch làm việc nào được duyệt trong ngày hôm nay!'
            ]);
        }

        // 3. KIỂM TRA KHUNG GIỜ THỰC TẾ
        $isValidTime = false;
        $currentTime = $now->format('H:i:s');
        $shiftTimeMessage = '';

        foreach ($todayShifts as $shift) {
            // Tính toán giờ bắt đầu và kết thúc của ca này
            $startTime = \Carbon\Carbon::parse($shift->start_time);
            $endTime = $startTime->copy()->addHours($shift->duration);
            
            // Lấy chuỗi hiển thị để báo lỗi nếu cần (VD: 12:00 - 15:00)
            $shiftTimeMessage .= '[' . $startTime->format('H:i') . ' - ' . $endTime->format('H:i') . '] ';

            // Cho phép check-in SỚM 30 PHÚT trước khi vào ca để chuẩn bị làm việc
            $allowedStartTime = $startTime->copy()->subMinutes(30)->format('H:i:s');
            
            // Muộn nhất là được check-in trước khi ca kết thúc
            $allowedEndTime = $endTime->format('H:i:s');

            // Nếu giờ hiện tại (currentTime) nằm lọt thỏm trong khoảng cho phép
            if ($currentTime >= $allowedStartTime && $currentTime <= $allowedEndTime) {
                $isValidTime = true;
                break; // Tìm thấy ca hợp lệ thì dừng vòng lặp luôn
            }
        }

        // Nếu duyệt qua hết các ca hôm nay mà không có ca nào khớp giờ
        if (!$isValidTime) {
            return response()->json([
                'success' => false,
                'message' => 'TỪ CHỐI: Chưa đến giờ làm việc hoặc đã hết ca! Ca của bạn hôm nay là: ' . $shiftTimeMessage . ' (Chỉ được phép vào ca sớm tối đa 30 phút)'
            ]);
        }

        // 4. Hoàn toàn hợp lệ -> Tạo bản ghi Check-in mới
        \Illuminate\Support\Facades\DB::table('attendances')->insert([
            'user_id' => $userId,
            'date' => $now->format('Y-m-d'),
            'check_in' => $now,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Vào ca thành công! Hệ thống đã mở khóa các tính năng Bán hàng.'
        ]);
    }
    // ==========================================
    // 4. API XỬ LÝ CHECK-OUT (Kết ca) - BẢN QUÉT SẠCH LỖI
    // ==========================================
    public function checkOut(Request $request)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        $now = now('Asia/Ho_Chi_Minh');

        // Tìm TẤT CẢ các ca đang mở của nhân viên này
        $openAttendances = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereNull('check_out');

        if ($openAttendances->exists()) {
            // Đóng toàn bộ các ca bị kẹt (update hàng loạt)
            $openAttendances->update([
                'check_out' => $now, 
                'updated_at' => $now
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Kết ca thành công! Hệ thống đã khóa quyền truy cập Bán hàng.'
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'LỖI: Bạn chưa Check-in hoặc đã Kết ca rồi!'
        ]);
    
    }
}