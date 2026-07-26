<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffManagerController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. DỮ LIỆU TAB 1: DUYỆT LỊCH LÀM VIỆC
        // ==========================================
        $pendingShifts = DB::table('shift_registrations')
            ->join('users', 'shift_registrations.user_id', '=', 'users.user_id')
            ->select('shift_registrations.*', 'users.name')
            ->where('status', 'pending')
            ->orderBy('shift_date', 'asc')
            ->get();

        // ==========================================
        // 2. DỮ LIỆU TAB 2: TỔNG KẾT LƯƠNG THÁNG
        // ==========================================
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $hourlyRate = 22000; // 22k/giờ

        // Lấy tất cả nhân viên
        $staffs = User::where('role', 'staff')->get();
        $salaryData = [];

        foreach ($staffs as $staff) {
            // A. Tính giờ làm thực tế (từ Check-in / Check-out)
            $attendances = DB::table('attendances')
                ->where('user_id', $staff->user_id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->whereNotNull('check_out')
                ->get();

            $totalHours = 0;
            foreach ($attendances as $att) {
                $in = Carbon::parse($att->check_in);
                $out = Carbon::parse($att->check_out);
                $totalHours += $in->diffInMinutes($out) / 60;
            }
            $baseSalary = round($totalHours, 2) * $hourlyRate;

            // B. Tính hoa hồng (2% doanh thu chia đều)
            $shiftIds = DB::table('shift_user')
                ->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')
                ->where('shift_user.user_id', $staff->user_id)
                ->whereMonth('shifts.date', $month)
                ->whereYear('shifts.date', $year)
                ->pluck('shifts.id');

            $totalCommission = 0;
            foreach ($shiftIds as $sId) {
                $revenue = DB::table('orders')->where('shift_id', $sId)->where('status', 'completed')->sum('total_amount');
                $staffCount = DB::table('shift_user')->where('shift_id', $sId)->count();
                if ($staffCount > 0 && $revenue > 0) {
                    $totalCommission += ($revenue * 0.02) / $staffCount;
                }
            }

            // Đưa vào mảng hiển thị
            $salaryData[] = [
                'name' => $staff->name,
                'hours' => round($totalHours, 2),
                'base_salary' => $baseSalary,
                'commission' => $totalCommission,
                'total_salary' => $baseSalary + $totalCommission
            ];
        }

        // Sắp xếp ai lương cao lên đầu
        usort($salaryData, function($a, $b) {
            return $b['total_salary'] <=> $a['total_salary'];
        });

        return view('admin.users.manager', compact('pendingShifts', 'salaryData', 'month', 'year'));
    }

    // Hàm Xử lý Duyệt hoặc Từ chối lịch
    public function updateShiftStatus(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' hoặc 'rejected'
        DB::table('shift_registrations')->where('id', $id)->update([
            'status' => $status,
            'updated_at' => now()
        ]);

        $msg = $status == 'approved' ? 'Đã DUYỆT ca làm việc!' : 'Đã TỪ CHỐI ca làm việc!';
        return back()->with('success', $msg);
    }
}