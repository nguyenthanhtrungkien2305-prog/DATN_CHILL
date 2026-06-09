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

    @include('staff.partials.sidebar', ['isOpen' => true])

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="bg-espresso text-white px-6 py-4 flex justify-between items-center shadow-md shrink-0">
            <h1 class="font-serif font-bold tracking-widest uppercase text-lg">Đăng Ký Lịch Làm Việc</h1>
        </div>

        <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- WIDGET: TỔNG SỐ GIỜ LÀM --}}
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/20 mb-6 flex justify-between items-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="z-10">
                    <h2 class="text-white/90 font-bold uppercase tracking-wider text-sm">Tổng số giờ làm (Tháng {{ date('m/Y') }})</h2>
                    <p class="text-5xl font-black mt-1">{{ $totalHours }} <span class="text-xl font-bold opacity-80">giờ</span></p>
                    <p class="text-xs text-white/80 mt-2 bg-white/20 inline-block px-3 py-1 rounded-lg">Chỉ tính các ca đã được Quản lý duyệt</p>
                </div>
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center z-10 shrink-0 border border-white/30">
                    <span class="text-4xl">⏱️</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- CỘT TRÁI: FORM ĐĂNG KÝ (w-1/3) --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 h-fit">
                        <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2">Đăng ký ca mới</h3>
                        
                        @if(session('success'))
                            <div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-xl text-sm font-bold mb-4 border border-emerald-100">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('staff.shifts.register') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Ngày làm việc</label>
                                <input type="date" name="shift_date" required min="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-coral focus:outline-none font-bold text-espresso">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Chọn ca làm (Tham khảo bảng bên)</label>
                                <select name="shift_select" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-coral focus:outline-none bg-white font-medium text-espresso">
                                    <optgroup label="Ca Part-time (4 Tiếng)">
                                        @foreach($availableShifts['4_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['start'] }} - {{ $s['end'] }})</option> @endforeach
                                    </optgroup>
                                    <optgroup label="Ca Full-time (8 Tiếng)">
                                        @foreach($availableShifts['8_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['start'] }} - {{ $s['end'] }})</option> @endforeach
                                    </optgroup>
                                    <optgroup label="Ca Tăng cường (12 Tiếng)">
                                        @foreach($availableShifts['12_hours'] as $s) <option value="{{ $s['val'] }}">{{ $s['name'] }} ({{ $s['start'] }} - {{ $s['end'] }})</option> @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-coral text-white font-black py-4 rounded-xl shadow-lg shadow-coral/30 hover:bg-[#d5523b] hover:-translate-y-0.5 transition-all uppercase tracking-widest mt-2">
                                GỬI XÁC NHẬN
                            </button>
                        </form>
                    </div>
                </div>

                {{-- CỘT PHẢI: BẢNG CA TRỐNG & LỊCH SỬ (w-2/3) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- BẢNG DANH SÁCH CA TRỐNG QUY ĐỊNH --}}
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2 flex items-center justify-between">
                            <span>Bảng Ca Làm Trống (06:00 - 22:00)</span>
                            <span class="text-xs font-bold bg-coral/10 text-coral px-3 py-1 rounded-lg">9 Lựa chọn</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border border-gray-100 rounded-xl bg-gray-50/50 p-4">
                                <div class="text-center font-black text-espresso mb-3 bg-white py-2 rounded-lg border border-gray-200 shadow-sm">Ca 4 Tiếng (4 Ca)</div>
                                <div class="space-y-2 text-sm font-medium text-gray-600">
                                    @foreach($availableShifts['4_hours'] as $s)
                                        <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-100">
                                            <span>{{ $s['name'] }}</span> <span class="font-bold text-coral">{{ $s['start'] }} - {{ $s['end'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="border border-gray-100 rounded-xl bg-gray-50/50 p-4">
                                <div class="text-center font-black text-espresso mb-3 bg-white py-2 rounded-lg border border-gray-200 shadow-sm">Ca 8 Tiếng (3 Ca)</div>
                                <div class="space-y-2 text-sm font-medium text-gray-600">
                                    @foreach($availableShifts['8_hours'] as $s)
                                        <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-100">
                                            <span>{{ $s['name'] }}</span> <span class="font-bold text-coral">{{ $s['start'] }} - {{ $s['end'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="border border-gray-100 rounded-xl bg-gray-50/50 p-4">
                                <div class="text-center font-black text-espresso mb-3 bg-white py-2 rounded-lg border border-gray-200 shadow-sm">Ca 12 Tiếng (2 Ca)</div>
                                <div class="space-y-2 text-sm font-medium text-gray-600">
                                    @foreach($availableShifts['12_hours'] as $s)
                                        <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-100">
                                            <span>{{ $s['name'] }}</span> <span class="font-bold text-coral">{{ $s['start'] }} - {{ $s['end'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BẢNG LỊCH SỬ ĐÃ ĐĂNG KÝ --}}
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-black text-espresso text-lg mb-4 uppercase tracking-wider border-b pb-2">Lịch sử đăng ký của bạn</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="text-xs uppercase text-gray-400 bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 rounded-tl-xl">Ngày làm</th>
                                        <th class="py-3 px-4">Giờ bắt đầu</th>
                                        <th class="py-3 px-4">Loại ca</th>
                                        <th class="py-3 px-4 rounded-tr-xl text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($registrations as $reg)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                        <td class="py-4 px-4 font-bold text-espresso">{{ \Carbon\Carbon::parse($reg->shift_date)->format('d/m/Y') }}</td>
                                        <td class="py-4 px-4 text-gray-600 font-bold">{{ \Carbon\Carbon::parse($reg->start_time)->format('H:i') }}</td>
                                        <td class="py-4 px-4 font-black text-coral">{{ $reg->duration }} Tiếng</td>
                                        <td class="py-4 px-4 text-center">
                                            @if($reg->status == 'pending') <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Đang chờ</span>
                                            @elseif($reg->status == 'approved') <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Đã duyệt</span>
                                            @else <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Từ chối</span> @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-gray-400 italic">Bạn chưa đăng ký ca làm nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    
    <style>
       .custom-scrollbar::-webkit-scrollbar { width: 6px; }
       .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
       .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    </style>
</body>
</html>