@extends('admin.layouts.app')

@section('title', 'Quản Lý Lịch & Lương Nhân Viên - Chill Chill Admin')

@section('content')
    {{-- Header của trang --}}
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản Lý Lịch & Lương Nhân Viên</h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
        </div>
    </header>

    <div class="p-8">
        <div class="w-full flex flex-col gap-6">

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-600 px-6 py-4 rounded-xl font-bold border border-emerald-100 shadow-sm flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="flex space-x-6 border-b border-gray-200">
                <button onclick="switchTab('shifts')" id="tab-btn-shifts" class="pb-3 px-4 font-black text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2">
                    📅 Duyệt Lịch Làm
                    @if(count($pendingShifts) > 0)
                        <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ count($pendingShifts) }}</span>
                    @endif
                </button>
                <button onclick="switchTab('salary')" id="tab-btn-salary" class="pb-3 px-4 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm">
                    💳 Bảng Lương (Tháng {{ $month }}/{{ $year }})
                </button>
            </div>

            <div id="tab-shifts" class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden block animate-fade-in">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 uppercase tracking-wider">Đơn đăng ký ca chờ duyệt</h3>
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
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6 font-black text-gray-900">{{ $shift->name }}</td>
                                <td class="p-4 font-bold text-gray-600">{{ \Carbon\Carbon::parse($shift->shift_date)->format('d/m/Y') }}</td>
                                <td class="p-4 font-bold text-[#e8634a]">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                <td class="p-4 text-center"><span class="bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold text-gray-600">{{ $shift->duration }} Tiếng</span></td>
                                <td class="p-4 pr-6 flex justify-end gap-2">
                                    <form action="{{ route('admin.staff.update_shift', $shift->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm px-4 py-2 rounded-lg transition-colors shadow-sm shadow-emerald-500/30">
                                            Duyệt Ca
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.staff.update_shift', $shift->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn từ chối ca này?');">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white font-bold text-sm px-4 py-2 rounded-lg transition-colors">
                                            Từ chối
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium">Hiện tại không có đơn đăng ký ca làm nào đang chờ duyệt.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-salary" class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden animate-fade-in">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 uppercase tracking-wider">Bảng kê thu nhập nhân viên</h3>
                    <span class="text-xs font-bold bg-[#e8634a]/10 text-[#e8634a] px-3 py-1.5 rounded-lg border border-[#e8634a]/20">Dữ liệu được cập nhật Real-time</span>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6">Nhân viên</th>
                                <th class="p-4 text-center">Giờ làm thực tế</th>
                                <th class="p-4 text-right">Lương cơ bản</th>
                                <th class="p-4 text-right">Tiền Hoa hồng</th>
                                <th class="p-4 pr-6 text-right">Tổng thực lãnh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($salaryData as $data)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6 font-black text-gray-900">{{ $data['name'] }}</td>
                                <td class="p-4 text-center">
                                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $data['hours'] }} giờ</span>
                                </td>
                                <td class="p-4 text-right font-bold text-gray-600">{{ number_format($data['base_salary'], 0, ',', '.') }}đ</td>
                                <td class="p-4 text-right font-bold text-orange-500">+{{ number_format($data['commission'], 0, ',', '.') }}đ</td>
                                <td class="p-4 pr-6 text-right font-black text-emerald-500 text-lg">{{ number_format($data['total_salary'], 0, ',', '.') }}đ</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium">Chưa có dữ liệu nhân viên.</td>
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
        // Javascript xử lý chuyển Tab mượt mà
        function switchTab(tabId) {
            document.getElementById('tab-shifts').classList.add('hidden');
            document.getElementById('tab-salary').classList.add('hidden');
            
            const shiftsBtn = document.getElementById('tab-btn-shifts');
            const salaryBtn = document.getElementById('tab-btn-salary');
            
            // Reset nút
            shiftsBtn.className = "pb-3 px-4 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            salaryBtn.className = "pb-3 px-4 font-bold text-gray-400 border-b-4 border-transparent hover:text-[#e8634a] transition-all uppercase tracking-wider text-sm";
            
            // Kích hoạt Tab
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            // Kích hoạt Nút
            if (tabId === 'shifts') {
                shiftsBtn.className = "pb-3 px-4 font-black text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            } else {
                salaryBtn.className = "pb-3 px-4 font-black text-[#e8634a] border-b-4 border-[#e8634a] transition-all uppercase tracking-wider text-sm";
            }
        }
    </script>
@endsection