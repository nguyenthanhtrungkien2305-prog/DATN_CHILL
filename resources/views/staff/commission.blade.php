<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Hoa Hồng Theo Ca - Điểm Cộng Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { 
                extend: { 
                    colors: { 
                        espresso: '#3e2723', 
                        coral: '#ff7043', 
                        cream: '#fbe9e7' 
                    } 
                } 
            }
        }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased h-screen flex overflow-hidden">

    @include('staff.partials.sidebar', ['isOpen' => false])

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        {{-- HEADER --}}
        <div class="h-[64px] bg-espresso text-white px-4 lg:px-6 flex justify-between items-center shadow-md shrink-0 z-10">
            <div class="font-bold text-lg flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-2 bg-white/10 hover:bg-coral rounded-lg transition-colors flex items-center justify-center focus:outline-none group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="font-serif font-bold tracking-widest uppercase text-base sm:text-lg">BÁO CÁO HOA HỒNG NHÂN VIÊN</h1>
            </div>
            <div class="text-sm">Xin chào, <span class="font-bold text-coral">{{ auth()->user()->name ?? 'Nhân viên' }}</span></div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="p-4 sm:p-6 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- BANNER QUY CHẾ HOA HỒNG 2% CHIA ĐỀU TRONG CA --}}
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-coral/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
                
                <div class="z-10">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-coral/10 text-coral text-xs font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Chính sách 2% Doanh Thu</span>
                        <span class="text-xs text-gray-500 font-semibold">• Chia đều cho tất cả nhân sự trực ca</span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-espresso">
                        Mức hoa hồng: <span class="text-coral font-black">2%</span> tổng giá trị đơn hàng hoàn thành
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Công thức: <strong>Hoa hồng của bạn = (Tổng doanh thu đơn hoàn thành × 2%) ÷ Số nhân sự trong ca</strong>.
                    </p>
                </div>

                {{-- THÔNG TIN CA HIỆN TẠI --}}
                <div class="z-10 bg-[#FAF7F2] border border-gray-200/80 rounded-2xl p-3.5 flex items-center gap-4 shrink-0 w-full lg:w-auto justify-between sm:justify-start">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase block">Ca hiện tại lúc này</span>
                        <span class="text-sm font-black text-espresso block">{{ $currentShift->name }}</span>
                        <span class="text-xs text-gray-500">Đồng đội: <strong class="text-coral">{{ $currentStaffCount }}</strong> nhân sự</span>
                    </div>
                    <div class="text-right pl-3 border-l border-gray-200">
                        <span class="text-[11px] font-bold text-gray-400 uppercase block">Quỹ ca hiện tại</span>
                        <span class="text-sm font-black text-emerald-600">+{{ number_format($currentShiftPool, 0, ',', '.') }}đ</span>
                        <span class="text-[11px] text-coral font-bold block">{{ $isUserInCurrentShift ? 'Bạn nhận: +' . number_format($currentMyCommission, 0, ',', '.') . 'đ' : 'Chưa Check-in' }}</span>
                    </div>
                </div>
            </div>

            {{-- THANH ĐIỀU HƯỚNG BỘ LỌC: THEO NGÀY / THEO TUẦN / THEO THÁNG --}}
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('staff.commission') }}" id="commission-filter-form" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                    
                    {{-- 3 NÚT CHỌN TAB THỜI GIAN --}}
                    <div class="flex items-center bg-gray-100 p-1 rounded-xl">
                        <button type="submit" name="type" value="day" 
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-xs sm:text-sm font-black transition-all {{ $filterType === 'day' ? 'bg-white text-coral shadow-sm' : 'text-gray-500 hover:text-espresso' }}">
                            Theo Ngày
                        </button>
                        <button type="submit" name="type" value="week" 
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-xs sm:text-sm font-black transition-all {{ $filterType === 'week' ? 'bg-white text-coral shadow-sm' : 'text-gray-500 hover:text-espresso' }}">
                            Theo Tuần
                        </button>
                        <button type="submit" name="type" value="month" 
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-xs sm:text-sm font-black transition-all {{ $filterType === 'month' ? 'bg-white text-coral shadow-sm' : 'text-gray-500 hover:text-espresso' }}">
                            Theo Tháng
                        </button>
                    </div>

                    {{-- BỘ CHỌN CHI TIẾT TÙY THEO LOẠI BỘ LỌC --}}
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <input type="hidden" name="type" value="{{ $filterType }}">

                        @if($filterType === 'day')
                            {{-- BỘ CHỌN NGÀY --}}
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Chọn ngày:</span>
                                <input type="date" name="date" value="{{ $selectedDate }}" 
                                       onchange="document.getElementById('commission-filter-form').submit()"
                                       class="border border-gray-300 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-bold text-espresso bg-white focus:outline-none focus:border-coral shadow-2xs">
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('staff.commission', ['type' => 'day', 'date' => now()->format('Y-m-d')]) }}" 
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === now()->format('Y-m-d') ? 'bg-coral text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    Hôm nay
                                </a>
                                <a href="{{ route('staff.commission', ['type' => 'day', 'date' => now()->subDay()->format('Y-m-d')]) }}" 
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === now()->subDay()->format('Y-m-d') ? 'bg-coral text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    Hôm qua
                                </a>
                            </div>

                        @elseif($filterType === 'week')
                            {{-- BỘ CHỌN TUẦN --}}
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Tuần của ngày:</span>
                                <input type="date" name="week_date" value="{{ $selectedWeekDate }}" 
                                       onchange="document.getElementById('commission-filter-form').submit()"
                                       class="border border-gray-300 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-bold text-espresso bg-white focus:outline-none focus:border-coral shadow-2xs">
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-coral bg-coral/10 px-2.5 py-1.5 rounded-lg">
                                    {{ $startOfWeek->format('d/m') }} - {{ $endOfWeek->format('d/m/Y') }}
                                </span>
                                <a href="{{ route('staff.commission', ['type' => 'week', 'week_date' => now()->format('Y-m-d')]) }}" 
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                                    Tuần này
                                </a>
                            </div>

                        @elseif($filterType === 'month')
                            {{-- BỘ CHỌN THÁNG & NĂM --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Tháng:</span>
                                <select name="month" onchange="document.getElementById('commission-filter-form').submit()"
                                        class="border border-gray-300 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-bold text-espresso bg-white focus:outline-none focus:border-coral shadow-2xs">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Năm:</span>
                                <select name="year" onchange="document.getElementById('commission-filter-form').submit()"
                                        class="border border-gray-300 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-bold text-espresso bg-white focus:outline-none focus:border-coral shadow-2xs">
                                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <a href="{{ route('staff.commission', ['type' => 'month', 'month' => now()->month, 'year' => now()->year]) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                                Tháng này
                            </a>
                        @endif

                    </div>
                </form>
            </div>

            {{-- 4 THẺ SỐ LIỆU TỔNG KẾT THEO MỐC THỜI GIAN ĐÃ CHỌN --}}
            @php
                if ($filterType === 'day') {
                    $summaryRevenue = $dayData['total_revenue'] ?? 0;
                    $summaryPool = $dayData['total_pool'] ?? 0;
                    $summaryMyComm = $dayData['total_my_commission'] ?? 0;
                    $summaryShifts = $dayData['worked_shifts_count'] ?? 0;
                    $timeTitle = 'Ngày ' . \Carbon\Carbon::parse($selectedDate)->format('d/m/Y');
                } elseif ($filterType === 'week') {
                    $summaryRevenue = $weekData['total_revenue'] ?? 0;
                    $summaryPool = $weekData['total_pool'] ?? 0;
                    $summaryMyComm = $weekData['total_commission'] ?? 0;
                    $summaryShifts = $weekData['worked_shifts_count'] ?? 0;
                    $timeTitle = 'Tuần (' . $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m/Y') . ')';
                } else {
                    $summaryRevenue = $monthData['total_revenue'] ?? 0;
                    $summaryPool = $monthData['total_pool'] ?? 0;
                    $summaryMyComm = $monthData['total_commission'] ?? 0;
                    $summaryShifts = $monthData['worked_shifts_count'] ?? 0;
                    $timeTitle = "Tháng $selectedMonth/$selectedYear";
                }
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                {{-- 1. Doanh thu --}}
                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Doanh thu {{ $timeTitle }}</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-espresso">{{ number_format($summaryRevenue, 0, ',', '.') }}đ</h3>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Từ các ca làm việc hoàn thành</p>
                </div>

                {{-- 2. Quỹ hoa hồng 2% toàn hệ thống --}}
                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 flex justify-between">
                        <span>Quỹ Hoa Hồng</span>
                        <span class="bg-emerald-100 text-emerald-700 font-extrabold px-2 py-0.5 rounded text-[10px]">2.0%</span>
                    </p>
                    <h3 class="text-2xl sm:text-3xl font-black text-emerald-600">{{ number_format($summaryPool, 0, ',', '.') }}đ</h3>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Tổng 2% từ doanh thu bán hàng</p>
                </div>

                {{-- 3. Hoa hồng thực nhận của bạn --}}
                <div class="bg-gradient-to-br from-coral to-[#e05b42] p-5 rounded-3xl shadow-lg shadow-coral/30 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/3"></div>
                    <p class="text-xs font-bold text-white/80 uppercase tracking-wider mb-1">Hoa hồng của bạn</p>
                    <h3 class="text-3xl sm:text-4xl font-black mb-1">+{{ number_format($summaryMyComm, 0, ',', '.') }}đ</h3>
                    <p class="text-[11px] text-white/90 font-medium mt-2 bg-white/20 inline-block px-2.5 py-1 rounded-lg">
                        Chia đều theo nhân sự từng ca
                    </p>
                </div>

                {{-- 4. Số ca đã trực --}}
                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ca trực của bạn</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-espresso">{{ $summaryShifts }} <span class="text-base font-bold text-gray-400">ca</span></h3>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Có mặt và hưởng hoa hồng</p>
                </div>
            </div>

            {{-- NỘI DUNG CHI TIẾT TÙY THEO CHẾ ĐỘ LỌC --}}

            {{-- 1. CHẾ ĐỘ XEM THEO NGÀY --}}
            @if($filterType === 'day')
                <div class="space-y-6">
                    {{-- BẢNG DANH SÁCH CÁC CA TRONG NGÀY --}}
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="font-black text-espresso text-base sm:text-lg uppercase tracking-wider">Danh Sách Ca Làm Việc Ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Hoa hồng được chia đều cho tất cả nhân viên có mặt trong ca</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50/80 border-b border-gray-100">
                                    <tr>
                                        <th class="py-3.5 px-4 sm:px-6">Ca & Khung Giờ</th>
                                        <th class="py-3.5 px-4">Nhân sự trong ca (Chia đều)</th>
                                        <th class="py-3.5 px-4 text-right">Doanh thu ca</th>
                                        <th class="py-3.5 px-4 text-right">Quỹ hoa hồng (2%)</th>
                                        <th class="py-3.5 px-4 sm:px-6 text-right">Hoa hồng của bạn</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @forelse($dayData['shifts'] ?? [] as $shift)
                                    <tr class="hover:bg-gray-50/50 transition-colors {{ $shift['is_user_in_shift'] ? 'bg-amber-50/30' : '' }}">
                                        <td class="py-4 px-4 sm:px-6">
                                            <div class="font-black text-espresso">{{ $shift['name'] }}</div>
                                            <div class="text-xs text-gray-500 font-medium">
                                                {{ \Carbon\Carbon::parse($shift['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($shift['end_time'])->format('H:i') }}
                                            </div>
                                            @if($shift['is_user_in_shift'])
                                                <span class="inline-block mt-1 text-[10px] font-black bg-coral/10 text-coral px-2 py-0.5 rounded-full uppercase">Ca của bạn</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-espresso text-xs bg-gray-100 px-2 py-1 rounded-md">
                                                    {{ $shift['staff_count'] }} người
                                                </span>
                                                @foreach($shift['staff_names'] as $name)
                                                    <span class="text-xs {{ $name === auth()->user()->name ? 'font-bold text-coral bg-coral/5 border border-coral/20' : 'text-gray-600 bg-gray-50' }} px-2 py-0.5 rounded">
                                                        {{ $name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right font-black text-espresso">
                                            {{ number_format($shift['revenue'], 0, ',', '.') }}đ
                                            <span class="text-[11px] text-gray-400 block font-normal">{{ $shift['orders_count'] }} đơn</span>
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-emerald-600">
                                            {{ number_format($shift['commission_pool'], 0, ',', '.') }}đ
                                        </td>
                                        <td class="py-4 px-4 sm:px-6 text-right">
                                            @if($shift['is_user_in_shift'])
                                                <span class="text-coral font-black text-base block">
                                                    +{{ number_format($shift['my_commission'], 0, ',', '.') }}đ
                                                </span>
                                                <span class="text-[10px] text-gray-400">
                                                    ({{ number_format($shift['commission_pool'], 0, ',', '.') }} ÷ {{ $shift['staff_count'] }})
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 font-medium italic">Không trực ca này</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 font-medium">
                                            Chưa có ca làm việc nào được ghi nhận trong ngày này.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- BẢNG THỐNG KÊ MÓN BÁN RA TRONG NGÀY --}}
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-black text-espresso text-base sm:text-lg uppercase tracking-wider">Sản Phẩm Bán Ra Trong Ngày</h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                                        <tr>
                                            <th class="pb-3">Tên sản phẩm</th>
                                            <th class="pb-3 text-center">Số lượng</th>
                                            <th class="pb-3 text-right">Tổng doanh thu</th>
                                            <th class="pb-3 text-right">Đóng góp quỹ hoa hồng (2%)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 text-sm">
                                        @forelse($dayData['products_sold'] ?? [] as $product)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-3.5 font-bold text-espresso">{{ $product['name'] }}</td>
                                            <td class="py-3.5 text-center font-black text-espresso">{{ $product['quantity'] }} ly</td>
                                            <td class="py-3.5 text-right font-bold text-gray-600">{{ number_format($product['total'], 0, ',', '.') }}đ</td>
                                            <td class="py-3.5 text-right">
                                                <span class="bg-emerald-100 text-emerald-700 font-black px-2 py-0.5 rounded text-xs">
                                                    +{{ number_format($product['commission_pool'], 0, ',', '.') }}đ
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-8 text-gray-400 font-medium">
                                                Chưa có sản phẩm nào được bán ra trong ngày này.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 2. CHẾ ĐỘ XEM THEO TUẦN --}}
            @elseif($filterType === 'week')
                <div class="space-y-6">
                    {{-- BẢNG THỐNG KÊ TỪNG NGÀY TRONG TUẦN --}}
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-black text-espresso text-base sm:text-lg uppercase tracking-wider">
                                Thống Kê Theo Từng Ngày (Tuần {{ $startOfWeek->format('d/m') }} - {{ $endOfWeek->format('d/m/Y') }})
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tổng hợp hoa hồng nhận được theo từng ngày làm việc</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50/80 border-b border-gray-100">
                                    <tr>
                                        <th class="py-3.5 px-6">Thứ & Ngày</th>
                                        <th class="py-3.5 px-4 text-center">Ca bạn đã trực</th>
                                        <th class="py-3.5 px-4 text-right">Doanh thu ca của bạn</th>
                                        <th class="py-3.5 px-4 text-right">Quỹ hoa hồng ca (2%)</th>
                                        <th class="py-3.5 px-6 text-right">Hoa hồng thực nhận</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach($weekData['days'] ?? [] as $day)
                                    <tr class="hover:bg-gray-50/50 transition-colors {{ $day['is_today'] ? 'bg-amber-50/40' : '' }}">
                                        <td class="py-4 px-6 font-bold text-espresso">
                                            <div class="font-black text-espresso">{{ $day['day_name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $day['formatted_date'] }} @if($day['is_today']) <span class="text-coral font-bold">(Hôm nay)</span> @endif</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($day['worked_shifts'] > 0)
                                                <span class="font-black bg-coral/10 text-coral px-3 py-1 rounded-full text-xs">
                                                    {{ $day['worked_shifts'] }} ca
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 font-medium">Nghỉ</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-espresso">
                                            {{ number_format($day['revenue'], 0, ',', '.') }}đ
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-emerald-600">
                                            {{ number_format($day['revenue'] * 0.02, 0, ',', '.') }}đ
                                        </td>
                                        <td class="py-4 px-6 text-right font-black text-coral text-base">
                                            @if($day['my_commission'] > 0)
                                                +{{ number_format($day['my_commission'], 0, ',', '.') }}đ
                                            @else
                                                <span class="text-xs text-gray-400 font-medium">0đ</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- BẢNG CHI TIẾT CÁC CA TRONG TUẦN MÀ BẠN ĐÃ TRỰC --}}
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-black text-espresso text-base sm:text-lg uppercase tracking-wider">Chi Tiết Các Ca Làm Việc Bạn Đã Trực Trong Tuần</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50/80 border-b border-gray-100">
                                    <tr>
                                        <th class="py-3.5 px-6">Ngày & Ca</th>
                                        <th class="py-3.5 px-4">Đồng đội trong ca</th>
                                        <th class="py-3.5 px-4 text-right">Doanh thu ca</th>
                                        <th class="py-3.5 px-4 text-right">Quỹ hoa hồng (2%)</th>
                                        <th class="py-3.5 px-6 text-right">Hoa hồng của bạn</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @forelse($weekData['shifts'] ?? [] as $shift)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-black text-espresso">{{ $shift['day_name'] }} ({{ \Carbon\Carbon::parse($shift['date'])->format('d/m/Y') }})</div>
                                            <div class="text-xs text-gray-500 font-medium">{{ $shift['name'] }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-espresso text-xs bg-gray-100 px-2 py-0.5 rounded">
                                                    {{ $shift['staff_count'] }} người
                                                </span>
                                                @foreach($shift['staff_names'] as $name)
                                                    <span class="text-xs {{ $name === auth()->user()->name ? 'font-bold text-coral bg-coral/5 border border-coral/20' : 'text-gray-600 bg-gray-50' }} px-2 py-0.5 rounded">
                                                        {{ $name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right font-black text-espresso">
                                            {{ number_format($shift['revenue'], 0, ',', '.') }}đ
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-emerald-600">
                                            {{ number_format($shift['commission_pool'], 0, ',', '.') }}đ
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <span class="text-coral font-black text-base block">
                                                +{{ number_format($shift['my_commission'], 0, ',', '.') }}đ
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                ({{ number_format($shift['commission_pool'], 0, ',', '.') }} ÷ {{ $shift['staff_count'] }})
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 font-medium">
                                            Bạn chưa trực ca nào trong tuần này.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            {{-- 3. CHẾ ĐỘ XEM THEO THÁNG --}}
            @elseif($filterType === 'month')
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="font-black text-espresso text-base sm:text-lg uppercase tracking-wider">
                                Lịch Sử Hoa Hồng Từng Ca Trong Tháng {{ $selectedMonth }}/{{ $selectedYear }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tất cả các ca bạn đã làm việc và được nhận hoa hồng 2% chia đều</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="py-3.5 px-6">Ngày & Ca</th>
                                    <th class="py-3.5 px-4">Số nhân sự & Đồng đội</th>
                                    <th class="py-3.5 px-4 text-right">Doanh thu ca</th>
                                    <th class="py-3.5 px-4 text-right">Quỹ hoa hồng ca (2%)</th>
                                    <th class="py-3.5 px-6 text-right">Hoa hồng của bạn</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($monthData['shifts'] ?? [] as $shift)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-espresso">{{ $shift['formatted_date'] }}</div>
                                        <div class="text-xs text-gray-500 font-medium">{{ $shift['name'] }} ({{ \Carbon\Carbon::parse($shift['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($shift['end_time'])->format('H:i') }})</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-espresso text-xs bg-gray-100 px-2 py-0.5 rounded">
                                                {{ $shift['staff_count'] }} người
                                            </span>
                                            @foreach($shift['staff_names'] as $name)
                                                <span class="text-xs {{ $name === auth()->user()->name ? 'font-bold text-coral bg-coral/5 border border-coral/20' : 'text-gray-600 bg-gray-50' }} px-2 py-0.5 rounded">
                                                    {{ $name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-right font-black text-espresso">
                                        {{ number_format($shift['revenue'], 0, ',', '.') }}đ
                                        <span class="text-[11px] text-gray-400 block font-normal">{{ $shift['orders_count'] }} đơn</span>
                                    </td>
                                    <td class="py-4 px-4 text-right font-bold text-emerald-600">
                                        {{ number_format($shift['commission_pool'], 0, ',', '.') }}đ
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="text-coral font-black text-base block">
                                            +{{ number_format($shift['my_commission'], 0, ',', '.') }}đ
                                        </span>
                                        <span class="text-[10px] text-gray-400">
                                            ({{ number_format($shift['commission_pool'], 0, ',', '.') }} ÷ {{ $shift['staff_count'] }})
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 font-medium">
                                        Không tìm thấy ca làm việc nào của bạn trong Tháng {{ $selectedMonth }}/{{ $selectedYear }}.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
       .custom-scrollbar::-webkit-scrollbar { width: 6px; }
       .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
       .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    </style>
    <script>
        function toggleSidebar() { 
            const sidebar = document.getElementById('sidebar'); 
            const backdrop = document.getElementById('sidebar-backdrop');
            if (window.innerWidth < 768) {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) backdrop.classList.add('hidden');
                }
            } else {
                sidebar.classList.toggle('md:w-0');
            }
        }
    </script>
</body>
</html>