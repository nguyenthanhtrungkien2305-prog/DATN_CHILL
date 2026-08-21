<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * Tỷ lệ hoa hồng toàn hệ thống: 2% tổng giá trị đơn hàng hoàn thành
     */
    const COMMISSION_RATE = 0.02;

    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->user_id ?? $user->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        // Kiểu lọc: 'day' (ngày), 'week' (tuần), 'month' (tháng)
        $filterType = $request->get('type', 'day');
        if (!in_array($filterType, ['day', 'week', 'month'])) {
            $filterType = 'day';
        }

        // Ngày chọn cho chế độ ngày
        $selectedDate = $request->get('date', $now->format('Y-m-d'));
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $carbonDate = $now->copy();
            $selectedDate = $carbonDate->format('Y-m-d');
        }

        // Tuần chọn cho chế độ tuần
        $selectedWeekDate = $request->get('week_date', $now->format('Y-m-d'));
        try {
            $carbonWeek = Carbon::parse($selectedWeekDate);
        } catch (\Exception $e) {
            $carbonWeek = $now->copy();
            $selectedWeekDate = $carbonWeek->format('Y-m-d');
        }
        $startOfWeek = $carbonWeek->copy()->startOfWeek(); // Thứ 2
        $endOfWeek = $carbonWeek->copy()->endOfWeek(); // Chủ nhật

        // Tháng & Năm cho chế độ tháng
        $selectedMonth = (int)$request->get('month', $now->month);
        $selectedYear = (int)$request->get('year', $now->year);
        if ($selectedMonth < 1 || $selectedMonth > 12) $selectedMonth = $now->month;
        if ($selectedYear < 2020 || $selectedYear > 2030) $selectedYear = $now->year;

        // Dữ liệu tổng hợp theo từng chế độ
        $dayData = [];
        $weekData = [];
        $monthData = [];

        // 1. TÍNH TOÁN THEO NGÀY (DAY)
        if ($filterType === 'day') {
            $dayData = $this->calculateDayCommission($userId, $selectedDate);
        }
        // 2. TÍNH TOÁN THEO TUẦN (WEEK)
        elseif ($filterType === 'week') {
            $weekData = $this->calculateWeekCommission($userId, $startOfWeek, $endOfWeek);
        }
        // 3. TÍNH TOÁN THEO THÁNG (MONTH)
        elseif ($filterType === 'month') {
            $monthData = $this->calculateMonthCommission($userId, $selectedMonth, $selectedYear);
        }

        // Xác định ca hiện tại của thời điểm này (cho thông tin Real-time)
        $shiftIndex = floor($now->hour / 4) + 1;
        $startHour = ($shiftIndex - 1) * 4;
        $startTime = sprintf('%02d:00:00', $startHour);
        $endTime = ($startHour + 4 == 24) ? '23:59:59' : sprintf('%02d:00:00', $startHour + 4);

        $currentShift = Shift::firstOrCreate(
            ['date' => $now->format('Y-m-d'), 'start_time' => $startTime],
            [
                'name' => "Ca $shiftIndex (" . sprintf('%02d:00', $startHour) . " - " . sprintf('%02d:00', $startHour + 4 > 23 ? 0 : $startHour + 4) . ")",
                'end_time' => $endTime
            ]
        );
        $currentShift->load('users');
        $currentStaffCount = $currentShift->users->count();
        $isUserInCurrentShift = $currentShift->users->contains($userId);

        $currentShiftRevenue = DB::table('orders')
            ->where('shift_id', $currentShift->id)
            ->where('status', 'completed')
            ->sum('total_amount');
        $currentShiftPool = $currentShiftRevenue * self::COMMISSION_RATE;
        $currentMyCommission = ($isUserInCurrentShift && $currentStaffCount > 0) ? ($currentShiftPool / $currentStaffCount) : 0;

        return view('staff.commission', compact(
            'filterType',
            'selectedDate',
            'selectedWeekDate',
            'startOfWeek',
            'endOfWeek',
            'selectedMonth',
            'selectedYear',
            'dayData',
            'weekData',
            'monthData',
            'currentShift',
            'currentStaffCount',
            'isUserInCurrentShift',
            'currentShiftRevenue',
            'currentShiftPool',
            'currentMyCommission'
        ));
    }

    /**
     * Tính toán hoa hồng chi tiết theo Ngày
     */
    private function calculateDayCommission($userId, $date)
    {
        $shifts = Shift::with('users')
            ->where('date', $date)
            ->orderBy('start_time', 'asc')
            ->get();

        $shiftsList = [];
        $totalDayRevenue = 0;
        $totalMyCommission = 0;
        $totalDayOrders = 0;
        $productsSold = [];
        $workedShiftsCount = 0;

        foreach ($shifts as $shift) {
            $staffCount = $shift->users->count();
            $isUserInShift = $shift->users->contains($userId);

            $orders = DB::table('orders')
                ->where('shift_id', $shift->id)
                ->where('status', 'completed')
                ->get();

            $shiftRevenue = (float)$orders->sum('total_amount');
            $shiftOrdersCount = $orders->count();
            $shiftPool = $shiftRevenue * self::COMMISSION_RATE; // 2% tổng đơn
            $myCommission = ($isUserInShift && $staffCount > 0) ? ($shiftPool / $staffCount) : 0;

            if ($isUserInShift) {
                $workedShiftsCount++;
                $totalMyCommission += $myCommission;
            }
            $totalDayRevenue += $shiftRevenue;
            $totalDayOrders += $shiftOrdersCount;

            // Thống kê từng món bán được
            foreach ($orders as $order) {
                $items = json_decode($order->items, true);
                if (is_array($items)) {
                    $rawItemsTotal = 0;
                    foreach ($items as $item) {
                        $rawItemsTotal += ((float)($item['price'] ?? 0)) * ((int)($item['quantity'] ?? 1));
                    }
                    $orderTotal = (float)$order->total_amount;
                    $discountRatio = ($rawItemsTotal > 0 && $orderTotal > 0) ? ($orderTotal / $rawItemsTotal) : 1;

                    foreach ($items as $item) {
                        $pName = $item['name'] ?? 'Sản phẩm';
                        $pId = !empty($item['productId']) ? $item['productId'] : (!empty($item['product_id']) ? $item['product_id'] : (!empty($item['combo_id']) ? 'combo_' . $item['combo_id'] : md5($pName)));
                        $pQty = (int)($item['quantity'] ?? 1);
                        $pPrice = (float)($item['price'] ?? 0);
                        $pTotal = round($pQty * $pPrice * $discountRatio);

                        if (!isset($productsSold[$pId])) {
                            $productsSold[$pId] = [
                                'name' => $pName,
                                'quantity' => 0,
                                'total' => 0,
                                'commission_pool' => 0
                            ];
                        }
                        $productsSold[$pId]['quantity'] += $pQty;
                        $productsSold[$pId]['total'] += $pTotal;
                        $productsSold[$pId]['commission_pool'] += $pTotal * self::COMMISSION_RATE;
                    }
                }
            }

            $shiftsList[] = [
                'id' => $shift->id,
                'name' => $shift->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'staff_count' => $staffCount,
                'staff_names' => $shift->users->pluck('name')->toArray(),
                'is_user_in_shift' => $isUserInShift,
                'revenue' => $shiftRevenue,
                'orders_count' => $shiftOrdersCount,
                'commission_pool' => $shiftPool,
                'my_commission' => $myCommission,
            ];
        }

        // Sắp xếp món theo doanh thu cao nhất
        uasort($productsSold, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return [
            'date' => $date,
            'shifts' => $shiftsList,
            'total_revenue' => $totalDayRevenue,
            'total_orders' => $totalDayOrders,
            'total_my_commission' => $totalMyCommission,
            'total_pool' => $totalDayRevenue * self::COMMISSION_RATE,
            'worked_shifts_count' => $workedShiftsCount,
            'products_sold' => $productsSold
        ];
    }

    /**
     * Tính toán hoa hồng chi tiết theo Tuần
     */
    private function calculateWeekCommission($userId, Carbon $startOfWeek, Carbon $endOfWeek)
    {
        $daysList = [];
        $totalWeekRevenue = 0;
        $totalWeekCommission = 0;
        $totalWeekOrders = 0;
        $totalWorkedShifts = 0;
        $workedDaysCount = 0;
        $allWeekShifts = [];

        $dayNames = [
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
            7 => 'Chủ Nhật',
        ];

        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $dateStr = $currentDay->format('Y-m-d');
            $dayOfWeek = $currentDay->dayOfWeekIso; // 1-7

            $shifts = Shift::with('users')
                ->where('date', $dateStr)
                ->orderBy('start_time', 'asc')
                ->get();

            $dayRevenue = 0;
            $dayMyCommission = 0;
            $dayOrdersCount = 0;
            $dayWorkedShifts = 0;
            $dayShiftsDetail = [];

            foreach ($shifts as $shift) {
                $staffCount = $shift->users->count();
                $isUserInShift = $shift->users->contains($userId);

                $orders = DB::table('orders')
                    ->where('shift_id', $shift->id)
                    ->where('status', 'completed')
                    ->get();

                $shiftRevenue = (float)$orders->sum('total_amount');
                $shiftOrders = $orders->count();
                $shiftPool = $shiftRevenue * self::COMMISSION_RATE;
                $myCommission = ($isUserInShift && $staffCount > 0) ? ($shiftPool / $staffCount) : 0;

                if ($isUserInShift) {
                    $dayWorkedShifts++;
                    $totalWorkedShifts++;
                    $dayMyCommission += $myCommission;
                    $dayRevenue += $shiftRevenue;
                }

                $dayOrdersCount += $shiftOrders;

                $shiftItem = [
                    'id' => $shift->id,
                    'date' => $dateStr,
                    'day_name' => $dayNames[$dayOfWeek],
                    'name' => $shift->name,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'staff_count' => $staffCount,
                    'staff_names' => $shift->users->pluck('name')->toArray(),
                    'is_user_in_shift' => $isUserInShift,
                    'revenue' => $shiftRevenue,
                    'orders_count' => $shiftOrders,
                    'commission_pool' => $shiftPool,
                    'my_commission' => $myCommission,
                ];

                $dayShiftsDetail[] = $shiftItem;
                if ($isUserInShift) {
                    $allWeekShifts[] = $shiftItem;
                }
            }

            if ($dayWorkedShifts > 0) {
                $workedDaysCount++;
            }

            $totalWeekRevenue += $dayRevenue;
            $totalWeekCommission += $dayMyCommission;
            $totalWeekOrders += $dayOrdersCount;

            $daysList[] = [
                'date' => $dateStr,
                'day_name' => $dayNames[$dayOfWeek],
                'formatted_date' => $currentDay->format('d/m'),
                'is_today' => $currentDay->isToday(),
                'worked_shifts' => $dayWorkedShifts,
                'revenue' => $dayRevenue,
                'my_commission' => $dayMyCommission,
                'shifts' => $dayShiftsDetail
            ];
        }

        return [
            'start_date' => $startOfWeek->format('d/m/Y'),
            'end_date' => $endOfWeek->format('d/m/Y'),
            'days' => $daysList,
            'shifts' => $allWeekShifts,
            'total_revenue' => $totalWeekRevenue,
            'total_commission' => $totalWeekCommission,
            'total_pool' => $totalWeekRevenue * self::COMMISSION_RATE,
            'total_orders' => $totalWeekOrders,
            'worked_days_count' => $workedDaysCount,
            'worked_shifts_count' => $totalWorkedShifts
        ];
    }

    /**
     * Tính toán hoa hồng chi tiết theo Tháng
     */
    private function calculateMonthCommission($userId, $month, $year)
    {
        $shifts = Shift::with('users')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $monthShifts = [];
        $totalMonthRevenue = 0;
        $totalMonthCommission = 0;
        $totalMonthOrders = 0;
        $totalWorkedShifts = 0;
        $workedDates = [];

        foreach ($shifts as $shift) {
            $staffCount = $shift->users->count();
            $isUserInShift = $shift->users->contains($userId);

            $orders = DB::table('orders')
                ->where('shift_id', $shift->id)
                ->where('status', 'completed')
                ->get();

            $shiftRevenue = (float)$orders->sum('total_amount');
            $shiftOrders = $orders->count();
            $shiftPool = $shiftRevenue * self::COMMISSION_RATE;
            $myCommission = ($isUserInShift && $staffCount > 0) ? ($shiftPool / $staffCount) : 0;

            if ($isUserInShift) {
                $totalWorkedShifts++;
                $workedDates[$shift->date] = true;
                $totalMonthCommission += $myCommission;
                $totalMonthRevenue += $shiftRevenue;
            }

            $totalMonthOrders += $shiftOrders;

            $monthShifts[] = [
                'id' => $shift->id,
                'date' => $shift->date,
                'formatted_date' => Carbon::parse($shift->date)->format('d/m/Y'),
                'name' => $shift->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'staff_count' => $staffCount,
                'staff_names' => $shift->users->pluck('name')->toArray(),
                'is_user_in_shift' => $isUserInShift,
                'revenue' => $shiftRevenue,
                'orders_count' => $shiftOrders,
                'commission_pool' => $shiftPool,
                'my_commission' => $myCommission,
            ];
        }

        return [
            'month' => $month,
            'year' => $year,
            'shifts' => $monthShifts,
            'total_revenue' => $totalMonthRevenue,
            'total_commission' => $totalMonthCommission,
            'total_pool' => $totalMonthRevenue * self::COMMISSION_RATE,
            'total_orders' => $totalMonthOrders,
            'worked_days_count' => count($workedDates),
            'worked_shifts_count' => $totalWorkedShifts
        ];
    }
}