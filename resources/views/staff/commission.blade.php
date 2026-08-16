<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoa Hồng & Ca Làm Việc - Điểm Cộng Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { espresso: '#3e2723', coral: '#ff7043', cream: '#fbe9e7' } } }
        }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased h-screen flex overflow-hidden">

    @include('staff.partials.sidebar', ['isOpen' => false])

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        {{-- THANH HEADER (ĐÃ ĐƯỢC ĐƯA RA NGOÀI ĐỂ TRÀN VIỀN) --}}
        <div class="h-[64px] bg-espresso text-white px-4 lg:px-6 flex justify-between items-center shadow-md shrink-0 z-10">
            <div class="font-bold text-lg flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-2 bg-white/10 hover:bg-coral rounded-lg transition-colors flex items-center justify-center focus:outline-none group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="font-serif font-bold tracking-widest uppercase text-base sm:text-lg">BÁO CÁO CA & HOA HỒNG</h1>
            </div>
            <div class="text-sm">Xin chào, <span class="font-bold text-coral">{{ auth()->user()->name ?? 'Nhân viên' }}</span></div>
        </div>

        {{-- NỘI DUNG CHÍNH (CÓ THANH CUỘN BÊN DƯỚI) --}}
        <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- THẺ TỔNG QUAN CA LÀM VIỆC --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-coral/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                
                <div class="flex items-center gap-5 z-10">
                    <div class="w-16 h-16 bg-espresso text-white rounded-2xl flex flex-col items-center justify-center font-black shadow-lg">
                        <span class="text-xs uppercase opacity-70 font-medium">ID</span>
                        <span class="text-xl">{{ sprintf('%02d', $shift->id ?? 0) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-espresso">{{ $shift->name ?? 'Đang tải...' }}</h2>
                        <p class="text-sm text-gray-500 font-medium mt-1">
                            🕒 Thời gian: <strong class="text-emerald-600">{{ isset($shift->start_time) ? \Carbon\Carbon::parse($shift->start_time)->format('H:i') : '--:--' }} - {{ isset($shift->end_time) ? \Carbon\Carbon::parse($shift->end_time)->format('H:i') : '--:--' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 z-10">
                    <span class="text-sm font-bold text-espresso/60 uppercase">Đồng đội trong ca:</span>
                    <span class="text-sm font-bold bg-gray-100 px-3 py-1.5 rounded-lg text-gray-600 shadow-inner">
                        <span class="text-coral">{{ $staffCount ?? 1 }}</span> nhân sự
                    </span>
                </div>
            </div>

            {{-- 3 THẺ SỐ LIỆU TÀI CHÍNH --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Tổng doanh thu Ca --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Doanh thu toàn ca</p>
                    <h3 class="text-3xl font-black text-espresso">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}đ</h3>
                    <p class="text-xs text-gray-400 mt-2">Đã hoàn thành <strong>{{ $totalOrders ?? 0 }} đơn hàng</strong></p>
                </div>
                
                {{-- Quỹ hoa hồng Ca --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1 flex justify-between">
                        <span>Quỹ hoa hồng (Ca)</span> <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px]">2.0% DT</span>
                    </p>
                    <h3 class="text-3xl font-black text-gray-700">{{ number_format($shiftCommissionPool ?? 0, 0, ',', '.') }}đ</h3>
                    <p class="text-xs text-gray-500 mt-2">Chia đều cho <strong class="text-coral">{{ $staffCount ?? 1 }}</strong> người</p>
                </div>

                {{-- Tiền thực nhận của User --}}
                <div class="bg-gradient-to-br from-coral to-[#e05b42] p-6 rounded-3xl shadow-lg shadow-coral/30 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/3"></div>
                    <p class="text-sm font-bold text-white/80 uppercase tracking-wider mb-1">Hoa hồng của bạn</p>
                    <h3 class="text-4xl font-black mb-1">+{{ number_format($myCommission ?? 0, 0, ',', '.') }}đ</h3>
                    <p class="text-xs text-white/90 mt-2 bg-white/20 inline-block px-2 py-1 rounded-md">
                        Tự động cộng vào lương cuối tháng
                    </p>
                </div>
            </div>

            {{-- BẢNG THỐNG KÊ MÓN BÁN ĐƯỢC TRONG CA --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-black text-espresso text-lg uppercase tracking-wider">Sản phẩm bán ra (Toàn ca)</h3>
                </div>
                <div class="p-6">
                    <table class="w-full text-left">
                        <thead class="text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="pb-3">Tên sản phẩm</th>
                                <th class="pb-3 text-center">Số lượng</th>
                                <th class="pb-3 text-right">Tổng tiền</th>
                                <th class="pb-3 text-right">Đóng góp quỹ (2.0%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            
                            @forelse($productsSold ?? [] as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 flex items-center gap-3">
                                    <span class="font-bold text-espresso text-sm">{{ $product['name'] }}</span>
                                </td>
                                <td class="py-4 text-center font-black text-espresso">{{ $product['quantity'] }} ly</td>
                                <td class="py-4 text-right font-bold text-gray-600">{{ number_format($product['total'], 0, ',', '.') }}đ</td>
                                <td class="py-4 text-right">
                                    <span class="bg-emerald-100 text-emerald-600 font-bold px-2 py-1 rounded-md text-xs">
                                        +{{ number_format($product['total'] * 0.02, 0, ',', '.') }}đ
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-400 font-medium">
                                    Chưa có đơn hàng nào hoàn thành trong Ca này.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

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