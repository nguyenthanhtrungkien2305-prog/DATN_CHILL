<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffManagerController extends Controller
{
    /**
     * Mức lương cơ bản theo giờ (mặc định 22.000đ/giờ)
     */
    const HOURLY_RATE = 22000;

    // 1. Giao diện chính Quản lý Nhân viên (Duyệt lịch & Bảng lương - Hỗ trợ lọc tìm kiếm)
    public function index(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));

        // A. TAB 1: ĐƠN ĐĂNG KÝ CA CHỜ DUYỆT (LỌC THEO TÊN NHÂN VIÊN)
        $pendingShiftsQuery = DB::table('shift_registrations')
            ->join('users', 'shift_registrations.user_id', '=', 'users.user_id')
            ->select('shift_registrations.*', 'users.name', 'users.phone')
            ->where('shift_registrations.status', 'pending');

        if (!empty($keyword)) {
            $pendingShiftsQuery->where(function($q) use ($keyword) {
                $q->where('users.name', 'like', "%{$keyword}%")
                  ->orWhere('users.phone', 'like', "%{$keyword}%");
            });
        }

        $pendingShifts = $pendingShiftsQuery->orderBy('shift_date', 'asc')->get();

        // B. TAB 2: BẢNG LƯƠNG & HOA HỒNG TỔNG QUAN (LỌC THEO TÊN/SĐỐ NHÂN VIÊN)
        $month = (int)$request->get('month', date('m'));
        $year = (int)$request->get('year', date('Y'));
        $hourlyRate = self::HOURLY_RATE;

        // Lấy danh sách nhân viên có vai trò 'staff'
        $staffQuery = User::where('role', 'staff');
        if (!empty($keyword)) {
            $staffQuery->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $staffs = $staffQuery->get();
        $salaryData = [];

        foreach ($staffs as $staff) {
            $stats = $this->calculateStaffStats($staff->user_id, $month, $year);

            $salaryData[] = [
                'user_id' => $staff->user_id,
                'name' => $staff->name,
                'phone' => $staff->phone,
                'email' => $staff->email,
                'hours' => $stats['monthly_hours'],
                'base_salary' => $stats['monthly_base_salary'],
                'commission' => $stats['monthly_commission'],
                'total_salary' => $stats['monthly_total_salary']
            ];
        }

        // Sắp xếp ai thu nhập cao hơn lên trước
        usort($salaryData, function($a, $b) {
            return $b['total_salary'] <=> $a['total_salary'];
        });

        return view('admin.users.manager', compact('pendingShifts', 'salaryData', 'month', 'year', 'staffs', 'keyword'));
    }

    // 2. Trang Chi tiết Nhân viên & Thống kê Giờ làm, Lương, Hoa hồng theo Ngày/Tháng/Năm
    public function detail(Request $request, $id)
    {
        $staff = User::where('user_id', $id)->firstOrFail();
        
        $month = (int)$request->get('month', date('m'));
        $year = (int)$request->get('year', date('Y'));
        $hourlyRate = self::HOURLY_RATE;

        $stats = $this->calculateStaffStats($staff->user_id, $month, $year);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'staff' => $staff,
                'month' => $month,
                'year' => $year,
                'stats' => $stats
            ]);
        }

        return view('admin.users.staff_detail', compact('staff', 'stats', 'month', 'year', 'hourlyRate'));
    }

    // 3. Xử lý Duyệt hoặc Từ chối lịch làm
    public function updateShiftStatus(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' hoặc 'rejected'
        DB::table('shift_registrations')->where('id', $id)->update([
            'status' => $status,
            'updated_at' => now()
        ]);

        $msg = $status == 'approved' ? 'Đã DUYỆT ca làm việc thành công!' : 'Đã TỪ CHỐI ca làm việc!';
        return back()->with('success', $msg);
    }

    /**
     * Hàm Helper tính toán Thống kê Giờ làm, Lương & Hoa hồng theo Ngày, Tháng, Năm của Nhân viên
     */
    protected function calculateStaffStats($userId, $month, $year)
    {
        $hourlyRate = self::HOURLY_RATE;

        // --- A. THỐNG KÊ THEO NGÀY TRONG THÁNG (DAILY BREAKDOWN) ---
        $attendances = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $dailyStats = [];
        $monthlyHours = 0;

        foreach ($attendances as $att) {
            $hours = 0;
            if ($att->check_in && $att->check_out) {
                $in = Carbon::parse($att->check_in);
                $out = Carbon::parse($att->check_out);
                $hours = round($in->diffInMinutes($out) / 60, 2);
            }
            $monthlyHours += $hours;

            $dayBaseSalary = round($hours * $hourlyRate);

            // Tính hoa hồng theo ca của ngày đó
            $dayCommission = 0;
            $shiftIds = DB::table('shift_user')
                ->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')
                ->where('shift_user.user_id', $userId)
                ->whereDate('shifts.date', $att->date)
                ->pluck('shifts.id');

            foreach ($shiftIds as $sId) {
                $revenue = DB::table('orders')->where('shift_id', $sId)->where('status', 'completed')->sum('total_amount');
                $staffCount = DB::table('shift_user')->where('shift_id', $sId)->count();
                if ($staffCount > 0 && $revenue > 0) {
                    $dayCommission += ($revenue * 0.02) / $staffCount;
                }
            }

            $dailyStats[] = [
                'date' => $att->date,
                'check_in' => $att->check_in ? Carbon::parse($att->check_in)->format('H:i:s d/m/Y') : 'Chưa có',
                'check_out' => $att->check_out ? Carbon::parse($att->check_out)->format('H:i:s d/m/Y') : 'Chưa chốt',
                'hours' => $hours,
                'base_salary' => $dayBaseSalary,
                'commission' => round($dayCommission),
                'total_income' => round($dayBaseSalary + $dayCommission)
            ];
        }

        $monthlyBaseSalary = round($monthlyHours * $hourlyRate);

        // --- B. TÍNH HOA HỒNG THÁNG ---
        $monthlyShiftIds = DB::table('shift_user')
            ->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')
            ->where('shift_user.user_id', $userId)
            ->whereMonth('shifts.date', $month)
            ->whereYear('shifts.date', $year)
            ->pluck('shifts.id');

        $monthlyCommission = 0;
        foreach ($monthlyShiftIds as $sId) {
            $revenue = DB::table('orders')->where('shift_id', $sId)->where('status', 'completed')->sum('total_amount');
            $staffCount = DB::table('shift_user')->where('shift_id', $sId)->count();
            if ($staffCount > 0 && $revenue > 0) {
                $monthlyCommission += ($revenue * 0.02) / $staffCount;
            }
        }
        $monthlyCommission = round($monthlyCommission);

        // --- C. THỐNG KÊ THEO NĂM (12 THÁNG TRONG NĂM) ---
        $yearlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $mAttendances = DB::table('attendances')
                ->where('user_id', $userId)
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->whereNotNull('check_out')
                ->get();

            $mHours = 0;
            foreach ($mAttendances as $att) {
                $in = Carbon::parse($att->check_in);
                $out = Carbon::parse($att->check_out);
                $mHours += $in->diffInMinutes($out) / 60;
            }
            $mHours = round($mHours, 2);
            $mBaseSalary = round($mHours * $hourlyRate);

            $mShiftIds = DB::table('shift_user')
                ->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')
                ->where('shift_user.user_id', $userId)
                ->whereMonth('shifts.date', $m)
                ->whereYear('shifts.date', $year)
                ->pluck('shifts.id');

            $mCommission = 0;
            foreach ($mShiftIds as $sId) {
                $revenue = DB::table('orders')->where('shift_id', $sId)->where('status', 'completed')->sum('total_amount');
                $staffCount = DB::table('shift_user')->where('shift_id', $sId)->count();
                if ($staffCount > 0 && $revenue > 0) {
                    $mCommission += ($revenue * 0.02) / $staffCount;
                }
            }
            $mCommission = round($mCommission);

            $yearlyStats[$m] = [
                'month' => $m,
                'hours' => $mHours,
                'base_salary' => $mBaseSalary,
                'commission' => $mCommission,
                'total_salary' => $mBaseSalary + $mCommission
            ];
        }

        return [
            'monthly_hours' => round($monthlyHours, 2),
            'monthly_base_salary' => $monthlyBaseSalary,
            'monthly_commission' => $monthlyCommission,
            'monthly_total_salary' => $monthlyBaseSalary + $monthlyCommission,
            'daily_stats' => $dailyStats,
            'yearly_stats' => $yearlyStats
        ];
    }
}