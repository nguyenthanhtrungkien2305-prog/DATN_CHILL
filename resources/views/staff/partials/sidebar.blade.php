@php
    // Kiểm tra xem nhân viên có ca nào đang mở không
    $hasActiveShift = \Illuminate\Support\Facades\DB::table('attendances')
        ->where('user_id', auth()->user()->user_id ?? auth()->id())
        ->whereNull('check_out') // Đã xóa dòng whereDate ở đây
        ->exists();
@endphp

<div id="sidebar" class="bg-white h-screen border-r border-espresso/10 flex flex-col shrink-0 transition-all duration-300 ease-in-out {{ isset($isOpen) && $isOpen ? 'w-64' : 'w-0' }} overflow-hidden shadow-xl z-50">
    <div class="h-[64px] bg-espresso text-white flex items-center justify-center font-bold tracking-widest shrink-0 px-4 border-b border-white/10">
        <span class="whitespace-nowrap">MENU QUẢN LÝ</span>
    </div>
    
    <div class="flex-1 overflow-y-auto py-4">
        
        {{-- NHÓM 1: CÁC CHỨC NĂNG BỊ KHÓA NẾU CHƯA CHECK-IN --}}
        <div class="{{ !$hasActiveShift ? 'opacity-50 grayscale pointer-events-none' : '' }}">
            @if(!$hasActiveShift)
                <div class="px-6 py-2 text-[10px] font-black text-red-500 uppercase tracking-widest flex items-center gap-1">
                    🔒 Cần Check-in để mở khóa
                </div>
            @endif

            <a href="{{ route('staff.new_orders') }}" class="flex items-center justify-between px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.new_orders') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso border-b border-gray-50 font-bold' }}">
                <div class="flex items-center gap-4"><span class="text-xl">📥</span><span>Đơn hàng mới</span></div>
                <span id="new-order-badge" class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white shadow-md">0</span>
            </a>
            
            <a href="{{ route('staff.pos') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.pos') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
                <span class="text-xl">🛒</span> Bán hàng (POS)
            </a>

            <a href="{{ route('staff.commission') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.commission') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
                <span class="text-xl">💰</span> Báo cáo Hoa hồng
            </a>
            <div class="my-2 border-t-2 border-gray-100 mx-4"></div>
        </div>
        
        {{-- NHÓM 2: CÁC CHỨC NĂNG TỰ DO --}}
        <a href="{{ route('staff.shifts') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.shifts') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span class="text-xl">📋</span> Quản lý Ca làm
        </a>

        <a href="{{ route('staff.salary') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.salary') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span class="text-xl">💳</span> Bảng lương
        </a>
    </div>

    {{-- KHU VỰC CHECK-IN / CHECK-OUT / LOGOUT --}}
    <div class="mt-auto p-4 border-t border-espresso/10 space-y-3 bg-gray-50/50">
        @if(!$hasActiveShift)
            <button onclick="attemptCheckIn()" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white bg-emerald-500 font-bold hover:bg-emerald-600 transition-all shadow-md shadow-emerald-500/30 group">
                <span class="text-sm uppercase tracking-wider">VÀO CA (Check-in)</span>
            </button>
        @else
            <button onclick="attemptCheckOut()" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-coral bg-coral/10 border border-coral/20 font-bold hover:bg-coral hover:text-white transition-all">
                <span class="text-sm uppercase tracking-wider">KẾT CA (Check-out)</span>
            </button>
        @endif

        <a href="{{ route('logout') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-red-500 font-bold border border-red-100 hover:bg-red-50 transition-colors">
            <span>Đăng xuất</span>
        </a>
    </div>
</div>

<script>
    function attemptCheckIn() {
        fetch("{{ route('staff.checkin') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message); // Hiển thị thông báo (Thành công hoặc Bị từ chối)
            if (data.success) window.location.reload();
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi kết nối ngầm! Vui lòng ấn F12 xem tab Console.");
        });
    }

    function attemptCheckOut() {
        fetch("{{ route('staff.checkout') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}" // <-- Đã sửa
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('logout') }}"; 
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra, không thể kết ca lúc này!');
        });
    }
</script>