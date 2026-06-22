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
        
        $registrations = \Illuminate\Support\Facades\DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->orderBy('shift_date', 'desc')
            ->get();

        $totalHours = \Illuminate\Support\Facades\DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('shift_date', date('m'))
            ->sum('duration');

        // THUẬT TOÁN BƯỚC NHẢY THỜI GIAN (XỬ LÝ CUỐI TUẦN)
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        
        if ($now->dayOfWeek == \Carbon\Carbon::SUNDAY) {
            $startOfWeek = $now->copy()->next(\Carbon\Carbon::MONDAY);
            $isNextWeek = true; 
        } else {
            $startOfWeek = $now->copy()->startOfWeek(); 
            $isNextWeek = false;
        }
        
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $allWeekRegs = \Illuminate\Support\Facades\DB::table('shift_registrations')
            ->whereBetween('shift_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        $slotCounts = [];
        foreach($allWeekRegs as $reg) {
            $date = $reg->shift_date;
            $startH = (int)\Carbon\Carbon::parse($reg->start_time)->format('H');
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

        // ==========================================
        // [MỚI] THUẬT TOÁN TÍNH GIỜ LÀM THỰC TẾ
        // ==========================================
        $attendances = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $userId)
            ->whereNotNull('check_out') // Chỉ lấy những ca đã hoàn thành
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->get();

        $historyData = [];
        $realTotalMonth = 0;
        $realTotalWeek = 0;
        
        $startOfThisWeek = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->startOfWeek()->format('Y-m-d');
        $endOfThisWeek = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->endOfWeek()->format('Y-m-d');

        foreach($attendances as $att) {
            $in = \Carbon\Carbon::parse($att->check_in);
            $out = \Carbon\Carbon::parse($att->check_out);
            
            // Tính số phút / 60 để ra số giờ (Làm tròn 2 chữ số thập phân)
            $hours = round($in->diffInMinutes($out) / 60, 2);

            // Cộng dồn cho tháng hiện tại
            if ($in->month == $now->month && $in->year == $now->year) {
                $realTotalMonth += $hours;
            }
            // Cộng dồn cho tuần hiện tại
            if ($att->date >= $startOfThisWeek && $att->date <= $endOfThisWeek) {
                $realTotalWeek += $hours;
            }

            // Đưa vào mảng để hiển thị bảng chi tiết
            $historyData[] = [
                'date' => $in->format('d/m/Y'),
                'check_in' => $in->format('H:i'),
                'check_out' => $out->format('H:i'),
                'hours' => $hours
            ];
        }

        return view('staff.shift_register', compact(
            'registrations', 'totalHours', 'availableShifts', 'slotCounts', 'startOfWeek', 'isNextWeek',
            'historyData', 'realTotalMonth', 'realTotalWeek' // Truyền 3 biến mới ra View
        ));
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
    // 3. API XỬ LÝ CHECK-IN (Bắt đầu ca)
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
            return response()->json(['success' => false, 'message' => 'Bạn đang ở trong một ca làm việc rồi. Hãy kết ca cũ trước!']);
        }

        // 2. Lấy danh sách CÁC CA ĐƯỢC DUYỆT trong ngày hôm nay
        $todayShifts = \Illuminate\Support\Facades\DB::table('shift_registrations')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('shift_date', $now->format('Y-m-d'))
            ->get();

        if ($todayShifts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'TỪ CHỐI: Bạn không có lịch làm việc nào được duyệt trong ngày hôm nay!']);
        }

        // 3. [MỚI CHỐNG LỖI DB] Kiểm tra xem hôm nay đã hoàn thành xong việc chưa?
        $alreadyWorkedToday = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $userId)
            ->where('date', $now->format('Y-m-d'))
            ->whereNotNull('check_out')
            ->count();

        // Nếu số lần đã Check-out trong ngày >= số ca được duyệt hôm nay -> Chặn lại
        if ($alreadyWorkedToday >= $todayShifts->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã hoàn thành toàn bộ (' . $todayShifts->count() . ') ca làm việc của ngày hôm nay. Hãy nghỉ ngơi và quay lại vào ngày mai nhé!'
            ]);
        }

        // 4. KIỂM TRA KHUNG GIỜ THỰC TẾ (Code cũ giữ nguyên)
        $isValidTime = false;
        $currentTime = $now->format('H:i:s');
        $shiftTimeMessage = '';

        foreach ($todayShifts as $shift) {
            $startTime = \Carbon\Carbon::parse($shift->start_time);
            $endTime = $startTime->copy()->addHours($shift->duration);
            
            $shiftTimeMessage .= '[' . $startTime->format('H:i') . ' - ' . $endTime->format('H:i') . '] ';
            $allowedStartTime = $startTime->copy()->subMinutes(30)->format('H:i:s');
            $allowedEndTime = $endTime->format('H:i:s');

            if ($currentTime >= $allowedStartTime && $currentTime <= $allowedEndTime) {
                $isValidTime = true;
                break; 
            }
        }

        if (!$isValidTime) {
            return response()->json(['success' => false, 'message' => 'TỪ CHỐI: Chưa đến giờ làm việc hoặc đã hết ca! Ca của bạn hôm nay là: ' . $shiftTimeMessage . ' (Được vào ca sớm tối đa 30 phút)']);
        }

        // 5. Hoàn toàn hợp lệ -> Tạo bản ghi Check-in mới
        \Illuminate\Support\Facades\DB::table('attendances')->insert([
            'user_id' => $userId,
            'date' => $now->format('Y-m-d'),
            'check_in' => $now,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return response()->json(['success' => true, 'message' => 'Vào ca thành công! Hệ thống đã mở khóa các tính năng Bán hàng.']);
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