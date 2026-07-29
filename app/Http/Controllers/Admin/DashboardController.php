<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> main

class DashboardController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total_amount');
        $newOrdersCount = DB::table('orders')->where('status', 'pending')->count();
        $totalProducts = DB::table('products')->count();
        $totalCustomers = DB::table('users')->where('role', 'customer')->count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'newOrdersCount',
            'totalProducts',
            'totalCustomers'
=======
        // 1. LẤY DỮ LIỆU THỐNG KÊ (WIDGETS)
        $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total_amount');
        $newOrders = DB::table('orders')->where('status', 'pending')->count();
        $totalProducts = DB::table('products')->count();
        // Đếm khách hàng (Giả sử dựa vào role 'user' trong bảng users)
        $totalUsers = DB::table('users')->where('role', 'user')->count();

        // 2. LẤY DỮ LIỆU CHO BIỂU ĐỒ (Lọc doanh thu các đơn đã hoàn thành)
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        // A. Theo Ngày (7 ngày gần nhất)
        $chartDay = ['labels' => [], 'data' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $rev = DB::table('orders')->where('status', 'completed')->whereDate('created_at', $date->format('Y-m-d'))->sum('total_amount');
            $chartDay['labels'][] = $date->format('d/m');
            $chartDay['data'][] = (float)$rev;
        }

        // B. Theo Tuần (4 tuần gần nhất)
        $chartWeek = ['labels' => [], 'data' => []];
        for ($i = 3; $i >= 0; $i--) {
            $start = $now->copy()->startOfWeek()->subWeeks($i);
            $end = $start->copy()->endOfWeek();
            $rev = DB::table('orders')->where('status', 'completed')->whereBetween('created_at', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')])->sum('total_amount');
            $chartWeek['labels'][] = 'Tuần ' . $start->format('W');
            $chartWeek['data'][] = (float)$rev;
        }

        // C. Theo Tháng (12 tháng trong năm nay)
        $chartMonth = ['labels' => [], 'data' => []];
        for ($i = 1; $i <= 12; $i++) {
            $rev = DB::table('orders')->where('status', 'completed')
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $i)->sum('total_amount');
            $chartMonth['labels'][] = 'Tháng ' . $i;
            $chartMonth['data'][] = (float)$rev;
        }

        // D. Theo Năm (5 năm gần nhất)
        $chartYear = ['labels' => [], 'data' => []];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $rev = DB::table('orders')->where('status', 'completed')->whereYear('created_at', $year)->sum('total_amount');
            $chartYear['labels'][] = $year;
            $chartYear['data'][] = (float)$rev;
        }

        return view('admin.dashboard', compact(
            'totalRevenue', 'newOrders', 'totalProducts', 'totalUsers',
            'chartDay', 'chartWeek', 'chartMonth', 'chartYear'
>>>>>>> main
        ));
    }
}