<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký ca làm - Điểm Cộng Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { espresso: '#3e2723', coral: '#ff7043', cream: '#fbe9e7' } } } }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased h-screen flex overflow-hidden">

    @include('staff.partials.sidebar', ['isOpen' => false])

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="h-[64px] bg-espresso text-white px-4 lg:px-6 flex justify-between items-center shadow-md shrink-0 z-10">
            <div class="font-bold text-lg flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-2 bg-white/10 hover:bg-coral rounded-lg transition-colors flex items-center justify-center focus:outline-none group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="font-serif font-bold tracking-widest uppercase text-base sm:text-lg">Quản Lý Lịch Làm Việc</h1>
            </div>
        </div>

        <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- WIDGET: TỔNG SỐ GIỜ LÀM --}}
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/20 mb-6 flex justify-between items-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="z-10">
                    <h2 class="text-white/90 font-bold uppercase tracking-wider text-sm">Tổng số giờ làm (Tháng {{ date('m/Y') }})</h2>
                    <p class="text-5xl font-black mt-1">{{ $totalHours }} <span class="text-xl font-bold opacity-80">giờ</span></p>
                </div>
            </div>

            {{-- MENU TABS TỰ ĐỘNG ĐỔI TÊN NẾU LÀ TUẦN SAU --}}
            <div class="flex space-x-4 md:space-x-6 mb-6 border-b border-gray-200 overflow-x-auto custom-scrollbar pb-1">
                <button onclick="switchTab('schedule')" id="tab-btn-schedule" class="pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-xs md:text-sm whitespace-nowrap shrink-0">
                    Lịch Cá Nhân ({{ $isNextWeek ? 'Tuần Sau' : 'Tuần Này' }})
                </button>
                <button onclick="switchTab('register')" id="tab-btn-register" class="pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-xs md:text-sm flex items-center gap-2 whitespace-nowrap shrink-0">
                    Đăng Ký Ca Mới & Danh Sách Chờ
                    @php $pendingCount = $registrations->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0) <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $pendingCount }}</span> @endif
                </button>
                <button onclick="switchTab('statistics')" id="tab-btn-statistics" class="pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-xs md:text-sm flex items-center gap-2 whitespace-nowrap shrink-0">
                    Thống Kê Giờ Làm Thực Tế
                </button>
            </div>

            @php
                // TẠO LỊCH TỪ BIẾN $startOfWeek CỦA CONTROLLER TRUYỀN SANG
                $weekDays = [];
                for ($i = 0; $i < 7; $i++) $weekDays[] = $startOfWeek->copy()->addDays($i);
                $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                
                $slots = [
                    ['name' => 'Ca Sáng', 'time' => '06:00 - 10:00', 'start' => 6],
                    ['name' => 'Ca Trưa', 'time' => '10:00 - 14:00', 'start' => 10],
                    ['name' => 'Ca Chiều', 'time' => '14:00 - 18:00', 'start' => 14],
                    ['name' => 'Ca Tối', 'time' => '18:00 - 22:00', 'start' => 18],
                ];
                $regsByDate = $registrations->groupBy('shift_date');
            @endphp

            {{-- ================================================= --}}
            {{-- TAB 1: BẢNG LỊCH CÁ NHÂN --}}
            {{-- ================================================= --}}
            <div id="tab-schedule" class="block animate-fade-in">
                @if($isNextWeek)
                    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2 shadow-sm">
                        <span>ℹ️</span> Hôm nay là Chủ Nhật, hệ thống đang tự động hiển thị lịch của <strong>Tuần Tiếp Theo</strong> để bạn đăng ký.
                    </div>
                @endif
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                    <div class="p-4 bg-gray-50/50 border-b border-gray-100 font-bold text-gray-600">
                        Chỉ hiển thị những ca bạn đã đăng ký
                    </div>
                    <div class="overflow-x-auto custom-scrollbar pb-2">
                        <table class="w-full text-center border-collapse min-w-[900px]">
                            <thead>
                                <tr>
                                    <th class="p-4 border-b border-r border-gray-100 bg-gray-50 w-[140px] sticky left-0 z-10">Thời gian</th>
                                    @foreach($weekDays as $index => $day)
                                        <th class="p-4 border-b border-gray-100 {{ $day->isToday() ? 'bg-coral/5 border-b-2 border-b-coral' : 'bg-gray-50' }}">
                                            <div class="text-[11px] font-black {{ $day->isToday() ? 'text-coral' : 'text-gray-400' }} uppercase">{{ $dayNames[$index] }}</div>
                                            <div class="text-lg font-black {{ $day->isToday() ? 'text-coral' : 'text-espresso' }} mt-1">{{ $day->format('d/m') }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slots as $slot)
                                    <tr>
                                        <td class="p-4 border-b border-r border-gray-100 bg-white sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                            <div class="font-black text-espresso text-sm">{{ $slot['name'] }}</div>
                                            <div class="text-xs text-coral font-bold mt-1">{{ $slot['time'] }}</div>
                                        </td>
                                        @foreach($weekDays as $day)
                                            @php
                                                $dayRegs = $regsByDate->get($day->format('Y-m-d'), []);
                                                $activeReg = null;
                                                foreach($dayRegs as $reg) {
                                                    $regStartHour = (int)\Carbon\Carbon::parse($reg->start_time)->format('H');
                                                    if ($regStartHour <= $slot['start'] && ($regStartHour + $reg->duration) >= ($slot['start'] + 4)) {
                                                        $activeReg = $reg; break;
                                                    }
                                                }
                                            @endphp
                                            <td class="p-2 border-b border-r border-gray-100 border-dashed {{ $day->isToday() ? 'bg-coral/5' : 'bg-white' }}">
                                                @if($activeReg)
                                                    @php
                                                        $bgClass = $activeReg->status == 'approved' ? 'bg-emerald-500 shadow-emerald-500/40' : 
                                                                  ($activeReg->status == 'rejected' ? 'bg-red-500 shadow-red-500/40' : 'bg-orange-400 shadow-orange-400/40');
                                                        $statusText = $activeReg->status == 'approved' ? 'Làm Việc' : ($activeReg->status == 'rejected' ? 'Từ chối' : 'Chờ duyệt');
                                                    @endphp
                                                    <div class="h-[60px] rounded-xl {{ $bgClass }} text-white flex flex-col items-center justify-center">
                                                        <span class="font-black text-sm">{{ $statusText }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- TAB 2: ĐĂNG KÝ CA & QUẢN LÝ ĐƠN --}}
            {{-- ================================================= --}}
            <div id="tab-register" class="hidden animate-fade-in space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    
                    {{-- FORM ĐĂNG KÝ (w-1/4) --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 h-fit sticky top-0">
                            <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2">Gửi đơn ca mới</h3>
                            
                            @if(session('error'))
                                <div class="bg-red-50 text-red-600 px-4 py-3 rounded-xl text-sm font-bold mb-4 border border-red-100">{{ session('error') }}</div>
                            @endif
                            @if(session('success'))
                                <div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-xl text-sm font-bold mb-4 border border-emerald-100">{{ session('success') }}</div>
                            @endif

                            <form action="{{ route('staff.shifts.register') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Ngày làm việc</label>
                                    <input type="date" id="shift_date_input" name="shift_date" required min="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-coral focus:outline-none font-bold text-espresso transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Ca làm việc</label>
                                    <select id="shift_select_input" name="shift_select" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-coral focus:outline-none bg-white font-medium text-espresso transition-all">
                                        <optgroup label="Ca Part-time (4 Tiếng)">
                                            @foreach($availableShifts['4_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['time'] }})</option> @endforeach
                                        </optgroup>
                                        <optgroup label="Ca Full-time (8 Tiếng)">
                                            @foreach($availableShifts['8_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['time'] }})</option> @endforeach
                                        </optgroup>
                                        <optgroup label="Ca Tăng cường (12 Tiếng)">
                                            @foreach($availableShifts['12_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['time'] }})</option> @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-coral text-white font-black py-4 rounded-xl shadow-lg shadow-coral/30 hover:bg-[#d5523b] transition-all uppercase tracking-widest mt-2">
                                    NỘP ĐƠN ĐĂNG KÝ
                                </button>
                                <p class="text-[11px] text-gray-400 mt-2 text-center">* Nhấp vào bảng bên phải để chọn ca nhanh.</p>
                            </form>
                        </div>
                    </div>

                    {{-- BẢNG HIỂN THỊ CHỖ TRỐNG (w-3/4) --}}
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                            <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2 flex items-center justify-between">
                                <span>Tình Trạng Chỗ Trống ({{ $isNextWeek ? 'Tuần Sau' : 'Tuần Này' }})</span>
                                <span class="text-xs font-bold bg-gray-100 text-gray-500 px-3 py-1 rounded-lg">Tối đa 4 người/ca</span>
                            </h3>
                            
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-center border-collapse min-w-[700px]">
                                    <thead>
                                        <tr>
                                            <th class="p-3 border-b border-gray-100 bg-gray-50 sticky left-0 z-10 w-[120px]">Giờ làm</th>
                                            @foreach($weekDays as $index => $day)
                                                <th class="p-3 border-b border-gray-100 bg-gray-50">
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase">{{ $dayNames[$index] }}</div>
                                                    <div class="text-sm font-black text-espresso mt-0.5">{{ $day->format('d/m') }}</div>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($slots as $slot)
                                            <tr>
                                                <td class="p-3 border-b border-r border-gray-100 bg-white sticky left-0 z-10">
                                                    <div class="text-xs font-black text-coral bg-coral/10 px-2 py-1 rounded">{{ $slot['time'] }}</div>
                                                </td>
                                                @foreach($weekDays as $day)
                                                    @php
                                                        $dateStr = $day->format('Y-m-d');
                                                        $currentStaff = $slotCounts[$dateStr][$slot['start']] ?? 0;
                                                        $isFull = $currentStaff >= 4;
                                                        $isPast = $day->isPast() && !$day->isToday();
                                                    @endphp
                                                    
                                                    <td class="p-2 border-b border-r border-gray-100 border-dashed transition-colors">
                                                        @if($isPast)
                                                            <div class="h-[50px] bg-gray-100 rounded-lg flex items-center justify-center opacity-50"><span class="text-[10px] font-bold text-gray-400">Đã qua</span></div>
                                                        @elseif($isFull)
                                                            <div onclick="alert('Ca này đã đủ 4 nhân viên làm việc. Vui lòng chọn ca khác!')" class="h-[50px] bg-red-50 border border-red-200 rounded-lg flex flex-col items-center justify-center cursor-not-allowed hover:bg-red-100">
                                                                <span class="text-xs font-black text-red-600">Đã đầy</span>
                                                                <span class="text-[10px] font-bold text-red-400 mt-0.5">4/4</span>
                                                            </div>
                                                        @else
                                                            <div onclick="fillForm('{{ $dateStr }}', '{{ sprintf('%02d:00|4', $slot['start']) }}')" class="h-[50px] bg-emerald-50 border border-emerald-200 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-emerald-500 hover:text-white transition-colors group">
                                                                <span class="text-xs font-black text-emerald-600 group-hover:text-white">Còn trống</span>
                                                                <span class="text-[10px] font-bold text-emerald-500 group-hover:text-emerald-100 mt-0.5">{{ $currentStaff }}/4 Người</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DANH SÁCH CHỜ DUYỆT & LỊCH SỬ --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mt-6">
                    <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2">Đơn Đăng Ký & Lịch Sử Của Bạn</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-4 rounded-tl-xl">Ngày làm</th>
                                    <th class="py-3 px-4">Giờ bắt đầu</th>
                                    <th class="py-3 px-4">Độ dài ca</th>
                                    <th class="py-3 px-4 rounded-tr-xl text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $reg)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-4 font-bold text-espresso">{{ \Carbon\Carbon::parse($reg->shift_date)->format('d/m/Y') }}</td>
                                    <td class="py-4 px-4 text-gray-600 font-bold">{{ \Carbon\Carbon::parse($reg->start_time)->format('H:i') }}</td>
                                    <td class="py-4 px-4 font-black text-coral">{{ $reg->duration }} Tiếng</td>
                                    <td class="py-4 px-4 text-center">
                                        @if($reg->status == 'pending') <span class="bg-orange-100 text-orange-600 border border-orange-200 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm">Đang chờ duyệt</span>
                                        @elseif($reg->status == 'approved') <span class="bg-emerald-100 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm">Đã duyệt</span>
                                        @else <span class="bg-red-100 text-red-600 border border-red-200 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm">Từ chối</span> @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400 italic font-medium">Bạn chưa đăng ký ca làm nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- ================================================= --}}
            {{-- TAB 3: THỐNG KÊ GIỜ LÀM THỰC TẾ --}}
            {{-- ================================================= --}}
            <div id="tab-statistics" class="hidden animate-fade-in space-y-6">
                
                {{-- 2 THẺ TỔNG QUAN (TUẦN & THÁNG) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-3xl font-black">📅</div>
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Thực làm tuần này</p>
                            <p class="text-3xl font-black text-espresso mt-1">{{ $realTotalWeek }} <span class="text-base text-gray-500 font-medium">giờ</span></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl font-black">📆</div>
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Thực làm tháng {{ date('m/Y') }}</p>
                            <p class="text-3xl font-black text-espresso mt-1">{{ $realTotalMonth }} <span class="text-base text-gray-500 font-medium">giờ</span></p>
                        </div>
                    </div>
                </div>

                {{-- BẢNG CHI TIẾT TỪNG CA --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2">Lịch Sử Chấm Công Chi Tiết</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase font-bold text-gray-400 bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-4 rounded-tl-xl w-32">Ngày làm</th>
                                    <th class="py-3 px-4 text-center">Giờ Vào Ca (Check-in)</th>
                                    <th class="py-3 px-4 text-center">Giờ Kết Ca (Check-out)</th>
                                    <th class="py-3 px-4 rounded-tr-xl text-right">Tổng thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyData as $history)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-4 font-black text-espresso">{{ $history['date'] }}</td>
                                    <td class="py-4 px-4 text-center font-bold text-emerald-500">{{ $history['check_in'] }}</td>
                                    <td class="py-4 px-4 text-center font-bold text-coral">{{ $history['check_out'] }}</td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg font-black text-sm">{{ $history['hours'] }} Giờ</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400 italic font-medium">Bạn chưa có lịch sử làm việc nào được ghi nhận.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <style>
       .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
       .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
       .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
       .animate-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
       @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
        function switchTab(tabId) {
            document.getElementById('tab-schedule').classList.add('hidden');
            document.getElementById('tab-register').classList.add('hidden');
            
            const scheduleBtn = document.getElementById('tab-btn-schedule');
            const registerBtn = document.getElementById('tab-btn-register');
            
            scheduleBtn.className = "pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm";
            registerBtn.className = "pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-btn-' + tabId);
            if (tabId === 'schedule') {
                activeBtn.className = "pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm";
            } else {
                activeBtn.className = "pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            }
        }
        function switchTab(tabId) {
            // Danh sách các tab hiện có
            const tabs = ['schedule', 'register', 'statistics'];
            
            // Ẩn tất cả và reset màu nút
            tabs.forEach(id => {
                document.getElementById('tab-' + id).classList.add('hidden');
                document.getElementById('tab-btn-' + id).className = "pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            });
            
            // Hiện tab được chọn và tô màu nút
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('tab-btn-' + tabId).className = "pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
        }

        function fillForm(date, timeVal) {
            const dateInput = document.getElementById('shift_date_input');
            const selectInput = document.getElementById('shift_select_input');
            
            dateInput.value = date;
            selectInput.value = timeVal;
            
            dateInput.classList.add('ring-4', 'ring-coral/50');
            selectInput.classList.add('ring-4', 'ring-coral/50');
            setTimeout(() => {
                dateInput.classList.remove('ring-4', 'ring-coral/50');
                selectInput.classList.remove('ring-4', 'ring-coral/50');
            }, 800);
        }

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

        @if(session('active_tab') == 'register')
            document.addEventListener('DOMContentLoaded', function() { switchTab('register'); });
        @endif
    </script>
</body>
</html>