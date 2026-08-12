@extends('admin.layouts.app')

@section('title', 'Admin Dashboard - Chill Chill')
@section('page_title', 'Tổng quan hệ thống')

@section('content')
<div class="flex-1 overflow-y-auto custom-scrollbar">
    
    {{-- Widget thống kê thực tế --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-emerald-500/10 rounded-full blur-xl"></div>
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Tổng doanh thu</h3>
            <p class="text-3xl font-black text-emerald-600">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}đ</p>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-[#e8634a]/10 rounded-full blur-xl"></div>
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Đơn chờ xử lý</h3>
            <p class="text-3xl font-black text-[#e8634a]">{{ $newOrders ?? $newOrdersCount ?? 0 }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-blue-500/10 rounded-full blur-xl"></div>
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Tổng sản phẩm</h3>
            <p class="text-3xl font-black text-gray-800">{{ $totalProducts ?? 0 }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-[-20px] top-[-20px] w-20 h-20 bg-purple-500/10 rounded-full blur-xl"></div>
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Khách hàng</h3>
            <p class="text-3xl font-black text-gray-800">{{ $totalUsers ?? $totalCustomers ?? 0 }}</p>
        </div>
    </div>

    {{-- KHU VỰC BIỂU ĐỒ --}}
    @if(isset($chartDay))
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
    @else
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[300px] flex flex-col justify-center items-center text-center">
        <div class="text-5xl mb-4">📊</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Báo cáo & Phân tích chi tiết</h3>
        <p class="text-gray-500 max-w-md">Khu vực này tổng hợp báo cáo doanh thu và hoạt động kinh doanh của Chill Chill Coffee.</p>
    </div>
    @endif
</div>

@if(isset($chartDay))
{{-- Import Chart.js từ CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartDataSets = {
        day: @json($chartDay ?? []),
        week: @json($chartWeek ?? []),
        month: @json($chartMonth ?? []),
        year: @json($chartYear ?? [])
    };

    let revenueChart;

    function updateChart() {
        const filter = document.getElementById('chartFilter').value;
        const dataToRender = chartDataSets[filter] || { labels: [], data: [] };

        const ctx = document.getElementById('revenueChart').getContext('2d');

        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dataToRender.labels || [],
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: dataToRender.data || [],
                    backgroundColor: '#e8634a',
                    borderRadius: 6,
                    barThickness: 'flex',
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return value.toLocaleString('vi-VN') + ' đ';
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
                        grid: { display: false }
                    }
                }
            }
        });
    }

    window.addEventListener('load', function() {
        updateChart();
    });
</script>
@endif
@endsection