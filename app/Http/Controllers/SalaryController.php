<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $hourlyRate = 22000;

        // 1. TÍNH LƯƠNG CƠ BẢN (Dựa trên Số giờ Check-in/Check-out THỰC TẾ)
        $attendances = DB::table('attendances')
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('check_out')
            ->get();

        $totalHours = 0;
        foreach ($attendances as $att) {
            $in = Carbon::parse($att->check_in);
            $out = Carbon::parse($att->check_out);
            $totalHours += $in->diffInMinutes($out) / 60; // Đổi phút ra giờ
        }
        $totalHours = round($totalHours, 2); // VD: 8.5 giờ

        $baseSalary = $totalHours * $hourlyRate;

        // 2. TÍNH HOA HỒNG THÁNG HIỆN TẠI
        $shiftIds = DB::table('shift_user')->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')
            ->where('shift_user.user_id', $userId)->whereMonth('shifts.date', $month)->whereYear('shifts.date', $year)->pluck('shifts.id');

        $totalCommission = 0; $shiftDetails = [];
        foreach ($shiftIds as $sId) {
            $shiftInfo = DB::table('shifts')->where('id', $sId)->first();
            $revenue = DB::table('orders')->where('shift_id', $sId)->where('status', 'completed')->sum('total_amount');
            $staffCount = DB::table('shift_user')->where('shift_id', $sId)->count();
            
            if ($staffCount > 0 && $revenue > 0) {
                $myShare = ($revenue * 0.02) / $staffCount;
                $totalCommission += $myShare;
                $shiftDetails[] = [
                    'shift_name' => $shiftInfo->name, 'date' => $shiftInfo->date,
                    'revenue' => $revenue, 'staffCount' => $staffCount, 'pool' => $revenue * 0.02, 'myShare' => $myShare
                ];
            }
        }
        $finalSalary = $baseSalary + $totalCommission;
        usort($shiftDetails, function($a, $b) { return strtotime($b['date']) - strtotime($a['date']); });

        // 3. KIỂM TRA TRẠNG THÁI ĐÃ NHẬN LƯƠNG CHƯA (Của tháng này)
        $currentPayment = DB::table('salary_payments')->where('user_id', $userId)->where('month', $month)->where('year', $year)->first();
        $paymentStatus = $currentPayment ? $currentPayment->status : 'pending';

        // 4. LẤY LỊCH SỬ CÁC THÁNG ĐÃ LÀM
        $workedMonths = DB::table('shift_registrations')
            ->selectRaw('MONTH(shift_date) as m, YEAR(shift_date) as y')
            ->where('user_id', $userId)->where('status', 'approved')
            ->groupBy('y', 'm')->orderBy('y', 'desc')->orderBy('m', 'desc')->get();

        $paymentHistory = [];
        foreach($workedMonths as $wm) {
            $pay = DB::table('salary_payments')->where('user_id', $userId)->where('month', $wm->m)->where('year', $wm->y)->first();
            
            // Nếu Quản lý chưa tạo bảng thanh toán, tự động tính toán số tiền thực tế
            $calcTotal = 0;
            if (!$pay) {
                $tH = DB::table('shift_registrations')->where('user_id', $userId)->where('status', 'approved')->whereMonth('shift_date', $wm->m)->whereYear('shift_date', $wm->y)->where('shift_date', '<=', now()->format('Y-m-d'))->sum('duration');
                $sIds = DB::table('shift_user')->join('shifts', 'shifts.id', '=', 'shift_user.shift_id')->where('shift_user.user_id', $userId)->whereMonth('shifts.date', $wm->m)->whereYear('shifts.date', $wm->y)->pluck('shifts.id');
                $cComm = 0;
                foreach($sIds as $id) {
                    $r = DB::table('orders')->where('shift_id', $id)->where('status', 'completed')->sum('total_amount');
                    $c = DB::table('shift_user')->where('shift_id', $id)->count();
                    if($c > 0 && $r > 0) $cComm += ($r * 0.02) / $c;
                }
                $calcTotal = ($tH * $hourlyRate) + $cComm;
            }

            $paymentHistory[] = [
                'month_str' => sprintf('%02d/%d', $wm->m, $wm->y),
                'status' => $pay ? $pay->status : 'pending',
                'amount' => $pay ? $pay->total_amount : $calcTotal,
                'paid_at' => $pay ? $pay->paid_at : null
            ];
        }

        return view('staff.salary', compact('month', 'year', 'hourlyRate', 'totalHours', 'baseSalary', 'totalCommission', 'finalSalary', 'shiftDetails', 'paymentStatus', 'paymentHistory'));
    }
}