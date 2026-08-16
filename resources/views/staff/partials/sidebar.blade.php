@php
    $userId = auth()->user()->user_id ?? auth()->id();

    // Tự động kết ca nếu đã quá giờ làm quy định
    \App\Http\Controllers\AttendanceController::autoCheckOutExpiredShifts($userId);

    // Tìm ca đang mở của nhân viên
    $activeAttendance = \Illuminate\Support\Facades\DB::table('attendances')
        ->where('user_id', $userId)
        ->whereNull('check_out')
        ->first();

    $hasActiveShift = !empty($activeAttendance) || (auth()->check() && auth()->user()->role === 'admin');

    $shiftEndTimeIso = null;
    if ($hasActiveShift && !empty($activeAttendance->scheduled_end_time)) {
        $shiftEndTimeIso = \Carbon\Carbon::parse($activeAttendance->scheduled_end_time)->toIso8601String();
    }

    if (!isset($pendingOrders)) {
        $pendingOrders = \Illuminate\Support\Facades\DB::table('orders')
            ->where('status', 'pending')
            ->get();
    }
@endphp

<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-[90] md:hidden hidden transition-opacity"></div>

<div id="sidebar" class="bg-white h-screen border-r border-espresso/10 flex flex-col shrink-0 transition-all duration-300 ease-in-out fixed md:relative top-0 bottom-0 left-0 z-[100] transform -translate-x-full md:translate-x-0 w-64 md:w-64 overflow-hidden shadow-2xl">
    <div class="h-[64px] bg-espresso text-white flex items-center justify-between font-bold tracking-widest shrink-0 px-4 border-b border-white/10">
        <span class="whitespace-nowrap">MENU QUẢN LÝ</span>
        <button type="button" onclick="toggleSidebar()" class="md:hidden text-white/70 hover:text-white p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto py-4">
        
        {{-- NHÓM 1: CÁC CHỨC NĂNG BÁN HÀNG (YÊU CẦU CHECK-IN) --}}
        <div class="{{ !$hasActiveShift ? 'opacity-50 grayscale pointer-events-none' : '' }}">
            @if(!$hasActiveShift)
                <!-- <div class="px-6 py-2 text-[10px] font-black text-red-500 uppercase tracking-widest flex items-center gap-1">
                    🔒 Cần Check-in để bán hàng
                </div> -->
            @endif

            <a href="{{ route('staff.new_orders') }}" class="flex items-center justify-between px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.new_orders') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso border-b border-gray-50 font-bold' }}">
                <span>Đơn hàng mới</span>
                <span id="new-order-badge" class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $pendingOrders->count() }}
                </span>
            </a>
            
            <a href="{{ route('staff.pos') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.pos') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
                <span>Bán hàng (POS)</span>
            </a>
            <div class="my-2 border-t-2 border-gray-100 mx-4"></div>
        </div>
        
        {{-- NHÓM 2: CÁC CHỨC NĂNG CÁ NHÂN (TỰ DO XEM) --}}
        <a href="{{ route('staff.shifts') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.shifts') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span>Quản lý Ca làm</span>
        </a>

        <a href="{{ route('staff.commission') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.commission') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span>Báo cáo Hoa hồng</span>
        </a>

        <a href="{{ route('staff.salary') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.salary') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span>Bảng lương</span>
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
                'X-CSRF-TOKEN': "{{ csrf_token() }}" // Nơi hay bị kẹt cache nhất
            }
        })
        .then(async res => {
            // Nếu phát hiện kẹt Cache (Lỗi 419) -> Tự động F5 lại trang
            if (res.status === 419) {
                alert("Mã bảo mật bị kẹt do Cache máy tính. Hệ thống đang tự động làm mới...");
                window.location.reload(true); 
                throw new Error("419 CSRF Token Expired");
            }
            if (!res.ok) throw new Error("Lỗi Server: " + res.status);
            return res.json();
        })
        .then(data => {
            alert(data.message);
            if (data.success) window.location.reload();
        })
        .catch(err => {
            if (err.message !== "419 CSRF Token Expired") {
                console.error(err);
                alert("Lỗi kết nối ngầm! Vui lòng ấn F12 xem tab Console.");
            }
        });
    }

    function attemptCheckOut() {
        fetch("{{ route('staff.checkout') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
        .then(async res => {
            if (res.status === 419) {
                alert("Mã bảo mật bị kẹt do Cache máy tính. Hệ thống đang tự động làm mới...");
                window.location.reload(true);
                throw new Error("419 CSRF Token Expired");
            }
            if (!res.ok) throw new Error("Lỗi Server: " + res.status);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('staff.shifts') }}"; 
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            if (err.message !== "419 CSRF Token Expired") {
                console.error(err);
                alert("Lỗi kết nối ngầm! Vui lòng ấn F12 xem tab Console.");
            }
        });
    }

    @if($hasActiveShift && $shiftEndTimeIso)
    (function() {
        const endTime = new Date("{{ $shiftEndTimeIso }}").getTime();
        let notified = false;
        const checkExpiry = () => {
            if (!notified && Date.now() >= endTime) {
                notified = true;
                alert("⏰ ĐÃ HẾT GIỜ LÀM VIỆC!\nHệ thống sẽ tự động kết ca cho bạn.");
                window.location.reload();
            }
        };
        setInterval(checkExpiry, 10000);
    })();
    @endif
</script>