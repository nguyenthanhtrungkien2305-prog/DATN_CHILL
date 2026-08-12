@extends('admin.layouts.app')

@section('title', 'Chi Tiết Nhân Viên: ' . $staff->name . ' - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.staff.manager') }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium">← Quay lại Quản lý Nhân viên</a>
            <h2 class="text-xl font-semibold text-gray-800">Chi Tiết Nhân Viên: <strong>{{ $staff->name }}</strong></h2>
        </div>
    </header>

    <div class="p-8 max-w-7xl mx-auto space-y-8">
        
        {{-- Khối thông tin Nhân viên & Bộ lọc Thời Gian --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-2xl border border-emerald-200">
                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $staff->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">SĐT: {{ $staff->phone ?? 'Chưa cập nhật' }} | Email: {{ $staff->email ?? 'Chưa cập nhật' }}</p>
                    <span class="inline-block mt-1 bg-emerald-50 text-emerald-600 font-bold px-2.5 py-0.5 rounded text-[11px]">Nhân viên chính thức</span>
                </div>
            </div>

            {{-- Bộ lọc Chọn Tháng / Năm --}}
            <form action="{{ route('admin.staff.detail', $staff->user_id) }}" method="GET" class="flex items-center gap-3 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                <div class="flex items-center gap-1 text-xs font-bold text-gray-600">
                    <span>Tháng:</span>
                    <select name="month" class="bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs font-bold focus:outline-none">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-center gap-1 text-xs font-bold text-gray-600">
                    <span>Năm:</span>
                    <select name="year" class="bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs font-bold focus:outline-none">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="bg-[#e8634a] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#d5523b] transition">
                    Lọc dữ liệu
                </button>
            </form>
        </div>

        {{-- 4 Thẻ Tổng Quan Thu Nhập Trong Tháng --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng giờ làm việc</span>
                <div class="text-3xl font-black text-blue-600 mt-2">{{ $stats['monthly_hours'] }} <span class="text-sm font-normal text-gray-500">giờ</span></div>
                <span class="text-[11px] text-gray-400 mt-2">Tháng {{ $month }}/{{ $year }}</span>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lương cơ bản ({{ number_format($hourlyRate) }}đ/h)</span>
                <div class="text-3xl font-black text-gray-800 mt-2">{{ number_format($stats['monthly_base_salary'], 0, ',', '.') }}đ</div>
                <span class="text-[11px] text-gray-400 mt-2">Tính theo giờ làm thực tế</span>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tiền Hoa hồng bán hàng</span>
                <div class="text-3xl font-black text-orange-500 mt-2">+{{ number_format($stats['monthly_commission'], 0, ',', '.') }}đ</div>
                <span class="text-[11px] text-gray-400 mt-2">2% Doanh thu ca trực</span>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-amber-200 bg-amber-50/20 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Tổng thu nhập thực lãnh</span>
                <div class="text-3xl font-black text-emerald-600 mt-2">{{ number_format($stats['monthly_total_salary'], 0, ',', '.') }}đ</div>
                <span class="text-[11px] text-amber-700 mt-2">Lương cơ bản + Hoa hồng</span>
            </div>
        </div>

        {{-- Bảng 1: Thống Kê Chi Tiết Giờ Làm & Lương Theo Ngày Trong Tháng --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">1. Nhật ký giờ làm & Thu nhập chi tiết theo Ngày (Tháng {{ $month }}/{{ $year }})</h3>
                <span class="text-xs font-bold text-gray-500">Tổng: {{ count($stats['daily_stats']) }} ngày làm việc</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-100">
                        <tr>
                            <th class="p-4 pl-6">Ngày làm việc</th>
                            <th class="p-4">Thời gian Check-in</th>
                            <th class="p-4">Thời gian Check-out</th>
                            <th class="p-4 text-center">Số giờ làm</th>
                            <th class="p-4 text-right">Lương cơ bản</th>
                            <th class="p-4 text-right">Hoa hồng ngày</th>
                            <th class="p-4 pr-6 text-right">Tổng thu nhập ngày</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($stats['daily_stats'] as $day)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6 font-bold text-gray-900">{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                            <td class="p-4 text-xs font-medium text-gray-600">{{ $day['check_in'] }}</td>
                            <td class="p-4 text-xs font-medium text-gray-600">{{ $day['check_out'] }}</td>
                            <td class="p-4 text-center">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $day['hours'] }}h</span>
                            </td>
                            <td class="p-4 text-right font-medium text-gray-700">{{ number_format($day['base_salary'], 0, ',', '.') }}đ</td>
                            <td class="p-4 text-right font-medium text-orange-500">+{{ number_format($day['commission'], 0, ',', '.') }}đ</td>
                            <td class="p-4 pr-6 text-right font-bold text-emerald-600">{{ number_format($day['total_income'], 0, ',', '.') }}đ</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400 text-sm font-medium">Không có dữ liệu điểm danh trong tháng {{ $month }}/{{ $year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bảng 2: Thống Kê Tổng Quan Theo 12 Tháng Trong Năm --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">2. Thống kê tổng hợp 12 tháng trong Năm {{ $year }}</h3>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-100">
                        <tr>
                            <th class="p-4 pl-6">Tháng</th>
                            <th class="p-4 text-center">Tổng giờ làm</th>
                            <th class="p-4 text-right">Lương cơ bản</th>
                            <th class="p-4 text-right">Hoa hồng</th>
                            <th class="p-4 pr-6 text-right">Tổng thực lãnh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($stats['yearly_stats'] as $mStats)
                        <tr class="hover:bg-gray-50/80 transition-colors {{ $mStats['month'] == $month ? 'bg-amber-50/30 font-bold' : '' }}">
                            <td class="p-4 pl-6 font-bold text-gray-900">
                                Tháng {{ $mStats['month'] }}/{{ $year }}
                                @if($mStats['month'] == $month)
                                    <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded ml-2 font-bold">(Đang chọn)</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $mStats['hours'] }}h</span>
                            </td>
                            <td class="p-4 text-right font-medium text-gray-700">{{ number_format($mStats['base_salary'], 0, ',', '.') }}đ</td>
                            <td class="p-4 text-right font-medium text-orange-500">+{{ number_format($mStats['commission'], 0, ',', '.') }}đ</td>
                            <td class="p-4 pr-6 text-right font-bold text-emerald-600">{{ number_format($mStats['total_salary'], 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
