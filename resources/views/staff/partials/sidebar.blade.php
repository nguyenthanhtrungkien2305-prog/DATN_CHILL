{{-- resources/views/staff/partials/sidebar.blade.php --}}

<div id="sidebar" class="bg-white h-screen border-r border-espresso/10 flex flex-col shrink-0 transition-all duration-300 ease-in-out {{ isset($isOpen) && $isOpen ? 'w-64' : 'w-0' }} overflow-hidden shadow-xl z-50">
    <div class="h-[64px] bg-espresso text-white flex items-center justify-center font-bold tracking-widest shrink-0 px-4 border-b border-white/10">
        <span class="whitespace-nowrap">MENU QUẢN LÝ</span>
    </div>
    
    <div class="flex-1 overflow-y-auto py-4">
        
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
        
        {{-- 4. CHẤM CÔNG --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">📋</span> Chấm công
        </a>

        {{-- 5. BẢNG BÁN SẢN PHẨM --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">📊</span> Bảng bán sản phẩm
        </a>

        {{-- 6. ĐIỂM THƯỞNG --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">⭐</span> Điểm thưởng
        </a>

        {{-- 7. HOA HỒNG --}}
        <a href="#" class="flex items-center gap-4 px-6 py-4 whitespace-nowrap transition-colors hover:bg-coral/10 hover:text-coral text-espresso/80 border-b border-gray-50 font-medium">
            <span class="text-xl">💰</span> Hoa hồng
        </a>
    </div>

    {{-- NÚT ĐĂNG XUẤT NẰM Ở ĐÁY --}}
    <div class="p-4 border-t border-gray-100 shrink-0">
        {{-- Sửa lại route logout cho đúng với hệ thống của bạn (nếu có) --}}
        <a href="#" class="flex items-center justify-center gap-3 w-full py-3 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white rounded-xl font-bold transition-colors whitespace-nowrap">
            <span class="text-lg">🚪</span> Đăng xuất
        </a>
    </div>
</div>
{{-- ========================================== --}}
{{-- TOAST THÔNG BÁO REAL-TIME (LẤY TỪ DATABASE) --}}
{{-- ========================================== --}}
<div id="realtime-toast" onclick="window.location.href='{{ route('staff.new_orders') }}'" class="cursor-pointer fixed top-6 right-6 z-[9999] transform transition-transform duration-500 translate-x-[150%] bg-white hover:bg-gray-50 border-l-4 border-coral shadow-2xl rounded-xl p-4 flex items-center gap-4 min-w-[320px] group">
    
    <div class="w-12 h-12 bg-coral/10 rounded-full flex items-center justify-center text-coral shrink-0 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
    </div>
    
    <div class="flex-1">
        <h4 class="font-black text-espresso text-sm uppercase tracking-wider">Có Đơn Hàng Mới!</h4>
        {{-- Bỏ text-espresso/60, đổi thành text-espresso để chữ rõ nét 100% --}}
        <p id="realtime-toast-msg" class="text-xs text-espresso font-bold mt-1">Khách vừa đặt 1 đơn hàng mới.</p>
    </div>
    
    <div class="text-coral text-xs font-black shrink-0">Bấm để xem &rarr;</div>
</div>

<script>
    let lastOrderId = 0;
    let isFirstLoad = true;

    function checkRealtimeOrders() {
        fetch(`/staff/api/check-new-orders?last_order_id=${lastOrderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.new_orders.length > 0) {
                    // Cập nhật lastOrderId thành ID lớn nhất vừa lấy được
                    lastOrderId = data.new_orders[data.new_orders.length - 1].order_id;
                    
                    // NẾU KHÔNG PHẢI LẦN LOAD TRANG ĐẦU TIÊN -> HIỆN THÔNG BÁO POPUP
                    if (!isFirstLoad) {
                        showRealtimeToast(data.count);
                    }
                    
                    // Cập nhật số đỏ nhảy trên Sidebar
                    const badge = document.getElementById('new-order-badge');
                    if(badge) {
                        // Tính tổng số đơn pending (có thể gọi API đếm lại, hoặc cộng dồn)
                        badge.textContent = parseInt(badge.textContent || 0) + data.count;
                        badge.classList.add('scale-150');
                        setTimeout(() => badge.classList.remove('scale-150'), 300);
                    }
                    
                    isFirstLoad = false;
                }
            })
            .catch(err => console.log('Đang chờ kết nối kiểm tra đơn...'));
    }

    function showRealtimeToast(count) {
        const toast = document.getElementById('realtime-toast');
        const msg = document.getElementById('realtime-toast-msg');
        
        msg.innerText = `Vừa có ${count} đơn hàng đổ về từ Website.`;
        toast.classList.remove('translate-x-[150%]', 'opacity-0');
        
        // Tự động ẩn sau đúng 15 GIÂY (15000 milliseconds)
        setTimeout(() => {
            toast.classList.add('translate-x-[150%]', 'opacity-0');
        }, 15000);
    }

    // Thiết lập hỏi thăm Server mỗi 5 giây 1 lần
    setInterval(checkRealtimeOrders, 5000);
    
    // Khởi chạy ngay lần đầu để lấy lastOrderId hiện tại
    checkRealtimeOrders();
</script>