@extends('admin.layouts.app')

@section('title', 'Quản Lý Lịch & Lương Nhân Viên - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản Lý Nhân Viên & Lịch Làm Việc</h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
        </div>
    </header>

    <div class="p-8">
        <div class="w-full flex flex-col gap-6">

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-600 px-6 py-3.5 rounded-xl font-bold border border-emerald-100 shadow-sm text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- THANH TÌM KIẾM VÀ ĐIỀU HƯỚNG TAB --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-3">
                <div class="flex space-x-6">
                    <button onclick="switchTab('shifts')" id="tab-btn-shifts" class="pb-2 px-3 font-bold text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2">
                        Duyệt Lịch Làm Việc
                        @if(count($pendingShifts) > 0)
                            <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ count($pendingShifts) }}</span>
                        @endif
                    </button>
                    <button onclick="switchTab('salary')" id="tab-btn-salary" class="pb-2 px-3 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm">
                        Bảng Lương & Hoa Hồng (Tháng {{ $month }}/{{ $year }})
                    </button>
                </div>

                {{-- Ô & Nút Tìm Kiếm Nhân Viên --}}
                <form action="{{ route('admin.staff.manager') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="text" name="keyword" id="staff-search-input" value="{{ request('keyword') }}" 
                           placeholder="Lọc tên hoặc SĐỐ nhân viên..." 
                           class="w-64 sm:w-72 px-3.5 py-1.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#e8634a]">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-1.5 rounded-xl text-sm font-bold hover:bg-gray-700 transition shrink-0">
                        Tìm
                    </button>
                    @if(request('keyword'))
                        <a href="{{ route('admin.staff.manager') }}" class="text-xs text-gray-500 hover:text-red-500 underline shrink-0">Bỏ lọc</a>
                    @endif
                </form>
            </div>

            {{-- TAB 1: DUYỆT LỊCH LÀM VIỆC --}}
            <div id="tab-shifts" class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden block animate-fade-in">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">Đơn đăng ký ca làm chờ duyệt</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Xem xét và phê duyệt lịch làm việc đăng ký từ nhân viên.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6 w-48">Nhân viên</th>
                                <th class="p-4">Ngày làm việc</th>
                                <th class="p-4">Khung giờ</th>
                                <th class="p-4 text-center">Độ dài ca</th>
                                <th class="p-4 pr-6 text-right w-64">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingShifts as $shift)
                            <tr class="staff-row hover:bg-gray-50/80 transition-colors" 
                                data-name="{{ mb_strtolower($shift->name) }}" 
                                data-phone="{{ $shift->phone ?? '' }}">
                                <td class="p-4 pl-6 font-bold text-gray-900">{{ $shift->name }}</td>
                                <td class="p-4 font-bold text-gray-600">{{ \Carbon\Carbon::parse($shift->shift_date)->format('d/m/Y') }}</td>
                                <td class="p-4 font-bold text-[#e8634a]">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                <td class="p-4 text-center"><span class="bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold text-gray-600">{{ $shift->duration }} Tiếng</span></td>
                                <td class="p-4 pr-6 flex justify-end gap-2">
                                    <form action="{{ route('admin.staff.update_shift', $shift->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm">
                                            Duyệt Ca
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.staff.update_shift', $shift->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn từ chối ca này?');">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs px-4 py-2 rounded-lg transition">
                                            Từ chối
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium">Hiện tại không có đơn đăng ký ca làm nào phù hợp.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: BẢNG LƯƠNG & HOA HỒNG TỔNG QUAN --}}
            <div id="tab-salary" class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden animate-fade-in">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">Bảng tổng kê thu nhập & hoa hồng nhân viên</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Bấm "Xem chi tiết" để xem báo cáo thống kê theo ngày, tháng, năm của từng nhân viên.</p>
                    </div>

                    {{-- Form chọn Tháng / Năm --}}
                    <form action="{{ route('admin.staff.manager') }}" method="GET" class="flex items-center gap-2">
                        @if(request('keyword'))
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        @endif
                        <select name="month" class="bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs font-bold">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                        <select name="year" class="bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs font-bold">
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-700 transition">Lọc</button>
                    </form>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6">Nhân viên</th>
                                <th class="p-4 text-center">Giờ làm thực tế</th>
                                <th class="p-4 text-right">Lương cơ bản</th>
                                <th class="p-4 text-right">Hoa hồng</th>
                                <th class="p-4 text-right">Tổng thực lãnh</th>
                                <th class="p-4 pr-6 text-center w-36">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @forelse($salaryData as $data)
                            <tr class="staff-row hover:bg-gray-50/80 transition-colors" 
                                data-name="{{ mb_strtolower($data['name']) }}" 
                                data-phone="{{ $data['phone'] ?? '' }}" 
                                data-email="{{ mb_strtolower($data['email'] ?? '') }}">
                                <td class="p-4 pl-6 font-bold text-gray-900">
                                    <div>{{ $data['name'] }}</div>
                                    <div class="text-xs text-gray-400 font-normal">{{ $data['phone'] ?? $data['email'] ?? '' }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $data['hours'] }} giờ</span>
                                </td>
                                <td class="p-4 text-right font-medium text-gray-700">{{ number_format($data['base_salary'], 0, ',', '.') }}đ</td>
                                <td class="p-4 text-right font-medium text-orange-500">+{{ number_format($data['commission'], 0, ',', '.') }}đ</td>
                                <td class="p-4 text-right font-bold text-emerald-600 text-base">{{ number_format($data['total_salary'], 0, ',', '.') }}đ</td>
                                <td class="p-4 pr-6 text-center">
                                    <a href="{{ route('admin.staff.detail', ['id' => $data['user_id'], 'month' => $month, 'year' => $year]) }}" 
                                       class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs px-3 py-1.5 rounded-lg transition inline-block">
                                        Xem Chi Tiết
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-gray-400 font-medium">Không tìm thấy dữ liệu nhân viên.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
        function switchTab(tabId) {
            document.getElementById('tab-shifts').classList.add('hidden');
            document.getElementById('tab-salary').classList.add('hidden');
            
            const shiftsBtn = document.getElementById('tab-btn-shifts');
            const salaryBtn = document.getElementById('tab-btn-salary');
            
            shiftsBtn.className = "pb-2 px-3 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            salaryBtn.className = "pb-2 px-3 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm";
            
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            if (tabId === 'shifts') {
                shiftsBtn.className = "pb-2 px-3 font-bold text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            } else {
                salaryBtn.className = "pb-2 px-3 font-bold text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm";
            }
        }

        // Lọc nhanh nhân viên theo từ khóa khi gõ chữ
        document.getElementById('staff-search-input')?.addEventListener('input', function() {
            const kw = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.staff-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const phone = row.getAttribute('data-phone') || '';
                const email = row.getAttribute('data-email') || '';
                if (name.includes(kw) || phone.includes(kw) || email.includes(kw)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection