<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Lương & Lịch Sử - Điểm Cộng Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { espresso: '#3e2723', coral: '#ff7043', cream: '#fbe9e7' } } } }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased h-screen flex overflow-hidden">

    @include('staff.partials.sidebar', ['isOpen' => true])

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="h-[64px] bg-espresso text-white px-6 flex justify-between items-center shadow-md shrink-0 z-10">
            <h1 class="font-serif font-bold tracking-widest uppercase text-lg">Báo Cáo Phiếu Lương</h1>
            <div class="text-sm font-bold bg-white/20 px-4 py-1.5 rounded-full shadow-inner flex items-center gap-2">
                <span>Kỳ Lương: Tháng {{ $month }}/{{ $year }}</span>
            </div>
        </div>

        <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- MENU TABS --}}
            <div class="flex space-x-6 mb-6 border-b border-gray-200">
                <button onclick="switchTab('current')" id="tab-btn-current" class="pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm">
                    💳 Bảng Lương (Tháng {{ $month }})
                </button>
                <button onclick="switchTab('history')" id="tab-btn-history" class="pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2">
                    🗂️ Lịch Sử Nhận Lương
                </button>
            </div>

            {{-- ================================================= --}}
            {{-- TAB 1: BẢNG LƯƠNG THÁNG HIỆN TẠI --}}
            {{-- ================================================= --}}
            <div id="tab-current" class="block animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full z-0"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 relative z-10">Tổng thời gian làm</p>
                        <h3 class="text-3xl font-black text-espresso relative z-10">{{ $totalHours }} <span class="text-lg opacity-60">giờ</span></h3>
                        <p class="text-xs text-blue-500 font-bold mt-2 bg-blue-50 inline-block px-2 py-1 rounded relative z-10">
                            x {{ number_format($hourlyRate, 0, ',', '.') }}đ / giờ
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full z-0"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 relative z-10">Lương cơ bản</p>
                        <h3 class="text-3xl font-black text-espresso relative z-10">{{ number_format($baseSalary, 0, ',', '.') }}đ</h3>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full z-0"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 relative z-10">Tổng Hoa Hồng</p>
                        <h3 class="text-3xl font-black text-coral relative z-10">+{{ number_format($totalCommission, 0, ',', '.') }}đ</h3>
                    </div>

                    {{-- THẺ TỔNG THU NHẬP CÓ ĐÓNG DẤU TRẠNG THÁI --}}
                    <div class="bg-gradient-to-br from-espresso to-[#2d1b18] p-6 rounded-3xl shadow-xl text-white relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/3"></div>
                        
                        <p class="text-xs font-bold text-white/60 uppercase tracking-widest mb-1 relative z-10">THỰC LÃNH CUỐI THÁNG</p>
                        <h3 class="text-4xl font-black text-emerald-400 relative z-10 mt-1 drop-shadow-lg">{{ number_format($finalSalary, 0, ',', '.') }}đ</h3>
                        
                        <div class="mt-4 relative z-10">
                            @if($paymentStatus == 'paid')
                                <span class="bg-emerald-500 text-white border-2 border-emerald-400 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.5)] flex items-center gap-2 w-fit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Đã Thanh Toán
                                </span>
                            @else
                                <span class="bg-orange-500/20 text-orange-400 border border-orange-500/50 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider flex items-center gap-2 w-fit">
                                    <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span> Chờ Thanh Toán
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SAO KÊ CHI TIẾT --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-black text-espresso text-lg uppercase tracking-wider">Sao kê Hoa Hồng Từng Ca</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase font-bold text-gray-400 bg-white border-b border-gray-100">
                                <tr>
                                    <th class="py-4 px-6">Ngày / Ca làm</th>
                                    <th class="py-4 px-6 text-right">Doanh thu Ca</th>
                                    <th class="py-4 px-6 text-center">Nhân sự</th>
                                    <th class="py-4 px-6 text-right text-coral">Hoa hồng của bạn</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($shiftDetails as $sd)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-espresso">{{ \Carbon\Carbon::parse($sd['date'])->format('d/m/Y') }}</div>
                                        <div class="text-[11px] font-bold text-gray-400 uppercase mt-0.5">{{ $sd['shift_name'] }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-right font-bold text-gray-600">{{ number_format($sd['revenue'], 0, ',', '.') }}đ</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $sd['staffCount'] }} người</span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="bg-emerald-100 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-lg text-xs font-black shadow-sm">
                                            +{{ number_format($sd['myShare'], 0, ',', '.') }}đ
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-400">Bạn chưa có ca làm nào phát sinh doanh thu.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- TAB 2: LỊCH SỬ NHẬN LƯƠNG --}}
            {{-- ================================================= --}}
            <div id="tab-history" class="hidden animate-fade-in">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-black text-espresso text-lg uppercase tracking-wider">Lịch Sử Lương Qua Các Tháng</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase font-bold text-gray-400 bg-white border-b border-gray-100">
                                <tr>
                                    <th class="py-4 px-6">Kỳ Lương (Tháng)</th>
                                    <th class="py-4 px-6 text-right">Tổng Lĩnh</th>
                                    <th class="py-4 px-6 text-center">Trạng Thái</th>
                                    <th class="py-4 px-6 text-center">Ngày Chuyển Khoản</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($paymentHistory as $ph)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-4 px-6 font-black text-espresso text-lg">Tháng {{ $ph['month_str'] }}</td>
                                    <td class="py-4 px-6 text-right font-black text-emerald-500 text-lg">{{ number_format($ph['amount'], 0, ',', '.') }}đ</td>
                                    <td class="py-4 px-6 text-center">
                                        @if($ph['status'] == 'paid')
                                            <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Đã Thanh Toán</span>
                                        @else
                                            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Đang Tính / Chờ</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center text-gray-500 font-medium">
                                        {{ $ph['paid_at'] ? \Carbon\Carbon::parse($ph['paid_at'])->format('d/m/Y H:i') : '--' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-400">Không có dữ liệu làm việc của các tháng trước.</td>
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
            document.getElementById('tab-current').classList.add('hidden');
            document.getElementById('tab-history').classList.add('hidden');
            
            const currentBtn = document.getElementById('tab-btn-current');
            const historyBtn = document.getElementById('tab-btn-history');
            
            currentBtn.className = "pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm";
            historyBtn.className = "pb-3 px-2 font-bold text-gray-400 border-b-4 border-transparent hover:text-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            if (tabId === 'current') {
                document.getElementById('tab-btn-current').className = "pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm";
            } else {
                document.getElementById('tab-btn-history').className = "pb-3 px-2 font-black text-coral border-b-4 border-coral transition-all uppercase tracking-wider text-sm flex items-center gap-2";
            }
        }
    </script>
</body>
</html>