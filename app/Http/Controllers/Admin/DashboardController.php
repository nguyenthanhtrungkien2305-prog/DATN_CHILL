<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total_amount');
        $newOrdersCount = DB::table('orders')->where('status', 'pending')->count();
        $totalProducts = DB::table('products')->count();
        $totalCustomers = DB::table('users')->where('role', 'customer')->count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'newOrdersCount',
            'totalProducts',
            'totalCustomers'
        ));
    }
}