<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Bạn có thể lấy các thống kê (tổng đơn hàng, tổng sản phẩm...) ở đây để truyền ra view
        return view('admin.dashboard');
    }
}