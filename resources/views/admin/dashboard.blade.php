@extends('admin.layouts.app')

@section('title', 'Admin Dashboard - Chill Chill')

@section('content')
    {{-- Header của Main Content --}}
    <header class="hidden lg:flex h-16 bg-white shadow-sm items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Tổng quan hệ thống</h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline font-medium">Đăng xuất</a>
        </div>
    </header>

    {{-- Nội dung Dashboard --}}
    <div class="p-4 md:p-8 flex-1 overflow-y-auto custom-scrollbar">
        
        {{-- Widget thống kê thực tế (Gọn gàng 2x2 trên Mobile) --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-emerald-500/10 rounded-full blur-xl"></div>
                <h3 class="text-gray-500 text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-1">Tổng doanh thu</h3>
                <p class="text-lg sm:text-2xl md:text-3xl font-black text-emerald-600 line-clamp-1">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            </div>
            
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-[#e8634a]/10 rounded-full blur-xl"></div>
                <h3 class="text-gray-500 text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-1">Đơn chờ xử lý</h3>
                <p class="text-lg sm:text-2xl md:text-3xl font-black text-[#e8634a]">{{ $newOrders }}</p>
            </div>
            
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-blue-500/10 rounded-full blur-xl"></div>
                <h3 class="text-gray-500 text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-1">Tổng sản phẩm</h3>
                <p class="text-lg sm:text-2xl md:text-3xl font-black text-gray-800">{{ $totalProducts }}</p>
            </div>
            
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-purple-500/10 rounded-full blur-xl"></div>
                <h3 class="text-gray-500 text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-1">Khách hàng</h3>
                <p class="text-lg sm:text-2xl md:text-3xl font-black text-gray-800">{{ $totalUsers }}</p>
            </div>
        </div>

        {{-- KHU VỰC BIỂU ĐỒ --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Biểu đồ Doanh Thu</h3>
                    <p class="text-sm text-gray-500 mt-1">Chỉ thống kê các đơn hàng đã giao thành công.</p>
                </div>
                
                {{-- Dropdown Lọc thời gian --}}
                <div class="mt-4 sm:mt-0">
                    <select id="chartFilter" onchange="updateChart()" class="bg-gray-50 border border-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg focus:outline-none focus:border-[#e8634a] cursor-pointer">
                        <option value="day">7 Ngày gần nhất</option>
                        <option value="week">4 Tuần gần nhất</option>
                        <option value="month" selected>12 Tháng năm nay</option>
                        <option value="year">5 Năm gần nhất</option>
                    </select>
                </div>
            </div>

            {{-- Thẻ Canvas vẽ biểu đồ Chart.js --}}
            <div class="relative h-[400px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Import Chart.js từ CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // 1. Nhận cục dữ liệu JSON từ Controller PHP
        const chartDataSets = {
            day: @json($chartDay),
            week: @json($chartWeek),
            month: @json($chartMonth),
            year: @json($chartYear)
        };

        let revenueChart; // Biến lưu trữ biểu đồ

        // 2. Hàm vẽ/cập nhật biểu đồ
        function updateChart() {
            const filter = document.getElementById('chartFilter').value;
            const dataToRender = chartDataSets[filter];

            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Nếu đã có biểu đồ cũ, hủy nó trước khi vẽ mới để tránh lỗi chập chờn
            if (revenueChart) {
                revenueChart.destroy();
            }

            // Cấu hình vẽ biểu đồ mới
            revenueChart = new Chart(ctx, {
                type: 'bar', // Biểu đồ dạng cột
                data: {
                    labels: dataToRender.labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: dataToRender.data,
                        backgroundColor: '#e8634a', // Màu cột (Chill Chill brand)
                        borderRadius: 6, // Bo góc cột
                        barThickness: 'flex',
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Ẩn chú thích
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return value.toLocaleString('vi-VN') + ' đ'; // Định dạng tiền Việt
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f3f4f6' },
                            ticks: {
                                callback: function(value) {
                                    if(value >= 1000000) return (value / 1000000) + ' Tr';
                                    if(value >= 1000) return (value / 1000) + ' k';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false } // Ẩn đường kẻ sọc ngang
                        }
                    }
                }
            });
        }

        // 3. Gọi hàm vẽ biểu đồ ngay khi trang load xong
        window.addEventListener('load', function() {
            updateChart();
        });
    </script>
@endsection