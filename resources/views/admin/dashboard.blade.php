@extends('admin.layouts.app')

@section('title', 'Admin Dashboard - Chill Chill')
@section('page_title', 'Tổng quan hệ thống')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-medium mb-1">Tổng doanh thu</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }} đ</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-medium mb-1">Đơn hàng mới</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $newOrdersCount ?? 0 }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-medium mb-1">Tổng sản phẩm</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $totalProducts ?? 0 }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-medium mb-1">Khách hàng</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $totalCustomers ?? 0 }}</p>
    </div>
</div>

<div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[400px] flex flex-col justify-center items-center text-center">
    <div class="text-5xl mb-4">📊</div>
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Báo cáo & Phân tích chi tiết</h3>
    <p class="text-gray-500 max-w-md">Khu vực này có thể được nâng cấp để tích hợp biểu đồ trực quan (như Chart.js) báo cáo doanh thu theo tháng, thống kê món bán chạy và hiệu suất làm việc của nhân viên.</p>
</div>
@endsection