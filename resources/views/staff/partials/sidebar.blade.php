{{-- resources/views/staff/partials/sidebar.blade.php --}}

<div id="sidebar" class="bg-white h-screen border-r border-espresso/10 flex flex-col shrink-0 transition-all duration-300 ease-in-out {{ isset($isOpen) && $isOpen ? 'w-64' : 'w-0' }} overflow-hidden shadow-xl z-50">
    <div class="h-[64px] bg-espresso text-white flex items-center justify-center font-bold tracking-widest shrink-0 px-4 border-b border-white/10">
        <span class="whitespace-nowrap">MENU QUẢN LÝ</span>
    </div>
    
    <div class="flex-1 overflow-y-auto py-4 custom-scrollbar">
        
        {{-- 1. TRANG ĐƠN HÀNG MỚI --}}
        <a href="{{ route('staff.new_orders') }}" class="flex items-center justify-between px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.new_orders') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso border-b border-gray-50 font-bold' }}">
            <div class="flex items-center gap-4"><span class="text-xl">📥</span><span>Đơn hàng mới</span></div>
            <span id="new-order-badge" class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white transition-transform duration-300 shadow-md">0</span>
        </a>
        
        {{-- 2. TRANG BÁN HÀNG (POS) --}}
        <a href="{{ route('staff.pos') }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.pos') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span class="text-xl">🛒</span> Bán hàng (POS)
        </a>
        
        {{-- 3. QUẢN LÝ ĐƠN HÀNG --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">🧾</span> Quản lý đơn hàng
        </a>

        <div class="my-2 border-t-2 border-gray-100 mx-4"></div>
        
        {{-- 4. CHẤM CÔNG / ĐĂNG KÝ CA (Đã link đúng route) --}}
        <a href="{{ route('staff.shifts') ?? '#' }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.shifts') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span class="text-xl">📋</span> Chấm công / Ca làm
        </a>

        {{-- 5. BẢNG BÁN SẢN PHẨM --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">📊</span> Bảng bán sản phẩm
        </a>

        {{-- 6. ĐIỂM THƯỞNG --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">⭐</span> Điểm thưởng
        </a>

        {{-- 7. HOA HỒNG (Đã link đúng route) --}}
        <a href="{{ route('staff.commission') ?? '#' }}" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors {{ request()->routeIs('staff.commission') ? 'bg-coral/10 text-coral border-r-4 border-coral font-bold' : 'hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium' }}">
            <span class="text-xl">💰</span> Hoa hồng
        </a>
    </div>

    {{-- KHU VỰC NÚT BẤM CUỐI SIDEBAR (CHECK-OUT & ĐĂNG XUẤT) --}}
    <div class="mt-auto p-4 border-t border-espresso/10 space-y-3 bg-gray-50/50">
        
        {{-- Nút Check-out (Màu xanh/cam nổi bật để kết ca) --}}
        <button onclick="attemptCheckOut()" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-white bg-emerald-500 font-bold hover:bg-emerald-600 shadow-md transition-all group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Kết Ca (Check-out)</span>
        </button>

        {{-- Nút Đăng xuất --}}
        <a href="{{ route('logout') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-red-500 font-bold border border-red-100 hover:bg-red-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span>Đăng xuất</span>
        </a>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL BẮT LÝ DO VỀ SỚM (CHECK-OUT SỚM) --}}
{{-- ========================================== --}}
<div id="early-checkout-modal" class="fixed inset-0 bg-black/70 z-[999] hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0 text-left">
    <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300" id="early-checkout-content">
        
        <div class="bg-red-50 p-6 flex items-start gap-4 border-b border-red-100">
            <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="font-black text-lg text-red-600 mb-1">Cảnh báo về sớm!</h3>
                <p class="text-sm text-red-800/70 font-medium" id="early-warning-text">Bạn đang về sớm hơn quy định. Vui lòng ghi lại lý do!</p>
            </div>
        </div>

        <div class="p-6 bg-white">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Lý do đột xuất (Bắt buộc):</label>
            <textarea id="checkout-reason" rows="3" placeholder="Ví dụ: Đau bụng đột xuất, Nhà có việc bận..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:outline-none resize-none"></textarea>
            
            <div class="flex gap-3 mt-6">
                <button onclick="closeEarlyModal()" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">Hủy bỏ, Ở lại làm</button>
                <button onclick="confirmEarlyCheckOut()" class="flex-1 py-3 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl shadow-lg shadow-red-500/30 transition-all">Nộp lý do & Về</button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- TOAST THÔNG BÁO REAL-TIME (CÓ ĐƠN MỚI) --}}
{{-- ========================================== --}}
<div id="realtime-toast" onclick="window.location.href='{{ route('staff.new_orders') }}'" class="cursor-pointer fixed top-6 right-6 z-[9999] transform transition-transform duration-500 translate-x-[150%] opacity-0 bg-white hover:bg-gray-50 border-l-4 border-coral shadow-2xl rounded-xl p-4 flex items-center gap-4 min-w-[320px] group">
    <div class="w-12 h-12 bg-coral/10 rounded-full flex items-center justify-center text-coral shrink-0 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
    </div>
    <div class="flex-1">
        <h4 class="font-black text-espresso text-sm uppercase tracking-wider">Có Đơn Hàng Mới!</h4>
        <p id="realtime-toast-msg" class="text-xs text-espresso font-bold mt-1">Khách vừa đặt 1 đơn hàng mới.</p>
    </div>
    <div class="text-coral text-xs font-black shrink-0">Bấm để xem &rarr;</div>
</div>

{{-- ========================================== --}}
{{-- LOGIC JAVASCRIPT (CHECKOUT SỚM + REALTIME) --}}
{{-- ========================================== --}}
<script>
    // --- 1. LOGIC CHECK-OUT CA LÀM ---
    function attemptCheckOut() {
        fetch("{{ route('staff.checkout') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.require_reason) {
                document.getElementById('early-warning-text').textContent = data.message;
                const modal = document.getElementById('early-checkout-modal');
                const content = document.getElementById('early-checkout-content');
                modal.classList.remove('hidden');
                setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
            } else if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('logout') }}"; 
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error("Lỗi:", err);
            alert("Có lỗi xảy ra, vui lòng thử lại!");
        });
    }

    function closeEarlyModal() {
        const modal = document.getElementById('early-checkout-modal');
        const content = document.getElementById('early-checkout-content');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function confirmEarlyCheckOut() {
        const reason = document.getElementById('checkout-reason').value.trim();
        if (reason === "") {
            alert("Vui lòng nhập lý do để Quản lý có thể xem xét!"); 
            return;
        }

        fetch("{{ route('staff.checkout') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('logout') }}"; 
            }
        });
    }

    // --- 2. LOGIC TÌM ĐƠN HÀNG REAL-TIME (ĐÃ SỬA LỖI) ---
    let lastOrderId = 0;
    let isFirstLoad = true;

    function checkRealtimeOrders() {
        fetch(`/staff/api/check-new-orders?last_order_id=${lastOrderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.new_orders && data.new_orders.length > 0) {
                    lastOrderId = data.new_orders[data.new_orders.length - 1].order_id;
                    
                    // Nếu không phải lần chạy đầu tiên thì mới hiện popup ting ting
                    if (!isFirstLoad) {
                        showRealtimeToast(data.count);
                    }
                    
                    const badge = document.getElementById('new-order-badge');
                    if(badge) {
                        badge.textContent = parseInt(badge.textContent || 0) + data.count;
                        badge.classList.add('scale-150');
                        setTimeout(() => badge.classList.remove('scale-150'), 300);
                    }
                }
                
                // 👉 FIX: Luôn đánh dấu là đã qua lần load đầu tiên, bất kể có đơn hay không!
                isFirstLoad = false; 
            })
            .catch(err => console.log('Đang chờ kết nối kiểm tra đơn...'));
    }

    function showRealtimeToast(count) {
        const toast = document.getElementById('realtime-toast');
        const msg = document.getElementById('realtime-toast-msg');
        
        msg.innerText = `Vừa có ${count} đơn hàng đổ về từ Website.`;
        toast.classList.remove('translate-x-[150%]', 'opacity-0');
        
        setTimeout(() => {
            toast.classList.add('translate-x-[150%]', 'opacity-0');
        }, 15000);
    }

    setInterval(checkRealtimeOrders, 5000);
    checkRealtimeOrders();
</script>