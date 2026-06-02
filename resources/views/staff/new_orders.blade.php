<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đơn Hàng Mới - Điểm Cộng Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { espresso: '#3e2723', coral: '#ff7043', cream: '#fbe9e7' } } }
        }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased overflow-hidden h-screen flex relative">

    {{-- TOAST THÔNG BÁO HOÀN THÀNH --}}
    <div id="toast-notification" class="fixed top-6 right-6 z-[200] transform transition-transform duration-500 translate-x-[150%] bg-white border-l-4 border-emerald-500 shadow-2xl rounded-xl p-4 flex items-center gap-4 min-w-[280px]">
        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <h4 class="font-black text-espresso text-sm uppercase tracking-wider">Thành công!</h4>
            <p class="text-xs text-espresso/60 font-medium mt-0.5">Đơn hàng đã được hoàn thành.</p>
        </div>
    </div>

    {{-- Gọi Sidebar chung (isOpen = true) --}}
    @include('staff.partials.sidebar', ['isOpen' => true])

    {{-- CONTAINER CHÍNH --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300">
        
        {{-- TOP BAR --}}
        <div class="bg-espresso text-white px-4 lg:px-6 py-3 flex justify-between items-center shadow-md shrink-0 h-[64px]">
            <div class="font-bold text-lg flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-2 bg-white/10 hover:bg-coral rounded-lg transition-colors flex items-center justify-center focus:outline-none group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="tracking-wider font-serif">ĐƠN HÀNG MỚI CHỜ XỬ LÝ</span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-white/70">Ca trực: <span class="font-bold text-white">{{ auth()->user()->name ?? 'Nhân viên' }}</span></span>
            </div>
        </div>

        {{-- DANH SÁCH ĐƠN HÀNG (Dữ liệu thật) --}}
        <div class="flex-1 p-6 overflow-y-auto custom-scrollbar bg-[#FAF7F2]">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-espresso uppercase tracking-widest flex items-center gap-2">
                    <span class="relative flex h-4 w-4">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </span>
                    Danh sách đơn đang chờ (<span id="total-orders-count">{{ $pendingOrders->count() }}</span>)
                </h2>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200 text-sm font-bold text-espresso">
                    Sắp xếp: <span class="text-coral">Cũ nhất ở trên</span>
                </div>
            </div>

            {{-- LƯỚI CARD ĐƠN HÀNG THẬT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6" id="orders-grid">
                
                @foreach($pendingOrders as $order)
                <div id="order-card-{{ $order->order_id }}" class="bg-white rounded-2xl border border-espresso/10 shadow-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-lg hover:border-coral/50">
                    
                    {{-- Header Card --}}
                    <div class="bg-gray-50 border-b border-gray-200 p-4 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-red-100 text-red-600 font-black text-xs px-2 py-1 rounded-md uppercase">Đang chờ</span>
                                <span class="font-black text-espresso text-lg">#{{ $order->order_id }}</span>
                            </div>
                            <p class="text-xs font-bold text-espresso/60 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold text-coral bg-coral/10 px-2 py-1 rounded-lg">{{ strtoupper($order->order_type) }}</span>
                        </div>
                    </div>

                    {{-- Body Card: Chi tiết khách & Món ăn --}}
                    <div class="p-4 flex-1">
                        <div class="mb-3 pb-3 border-b border-dashed border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Khách hàng</p>
                            <p class="font-bold text-espresso text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-espresso/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $order->customer_name ?? 'Khách Vãng Lai' }}
                            </p>
                            @if($order->shipping_address) {{-- Ở POS chúng ta dùng cột này lưu Ghi chú đơn hàng --}}
                                <p class="text-xs text-coral italic mt-1 bg-coral/5 p-1.5 rounded-lg border border-coral/10">📝 {{ $order->shipping_address }}</p>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Chi tiết món</p>
                            @php 
                                $items = json_decode($order->items, true); 
                                if(!is_array($items)) $items = [];
                            @endphp
                            
                            @foreach($items as $item)
                                <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    <div class="flex justify-between items-start">
                                        <span class="font-bold text-espresso text-sm flex-1">{{ $item['name'] }}</span>
                                        <span class="font-black text-coral text-sm">x{{ $item['quantity'] }}</span>
                                    </div>
                                    
                                    {{-- Render Topping nếu có --}}
                                    @if(isset($item['toppings']) && count(array_filter($item['toppings'])) > 0)
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach($item['toppings'] as $t_id => $t_qty)
                                                @if($t_qty > 0 && isset($toppings[$t_id]))
                                                    <span class="text-[10px] text-espresso/60 italic bg-white inline-block px-1.5 py-0.5 rounded border border-gray-200">
                                                        + {{ $toppings[$t_id]->name }} (x{{ $t_qty }})
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer Card: Tổng tiền & Nút Hoàn thành --}}
                    <div class="p-4 bg-gray-50 border-t border-gray-200 mt-auto">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-espresso text-xs">TỔNG TIỀN:</span>
                            <span class="font-black text-xl text-coral">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <button onclick="openConfirmModal('{{ $order->order_id }}')" class="w-full py-3 bg-emerald-500 text-white font-black rounded-xl uppercase tracking-widest shadow-md shadow-emerald-500/20 hover:bg-emerald-600 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Hoàn Thành Đơn
                        </button>
                    </div>
                </div>
                @endforeach
                
            </div>
            
            {{-- Màn hình trống (Hiện khi không có đơn nào) --}}
            <div id="empty-state" class="{{ $pendingOrders->count() > 0 ? 'hidden' : 'flex' }} flex-col items-center justify-center h-full text-gray-400">
                <svg class="w-20 h-20 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <h3 class="text-xl font-bold text-espresso/40">Tuyệt vời!</h3>
                <p class="text-sm">Bạn đã xử lý xong toàn bộ đơn hàng mới.</p>
            </div>

        </div>
    </div>

    {{-- POPUP XÁC NHẬN --}}
    <div id="confirm-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.2s;">
        <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm mx-4 overflow-hidden flex flex-col scale-95 transition-transform duration-200 text-center" id="confirm-modal-content">
            <div class="p-8">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-black text-xl text-espresso mb-2">Xác nhận hoàn thành?</h3>
                <p class="text-sm text-gray-500 font-medium">Bạn đã pha chế xong và muốn hoàn tất đơn hàng <span id="modal-order-id-text" class="font-black text-coral">#???</span> chứ?</p>
            </div>
            
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 py-3 rounded-xl font-bold text-espresso bg-white border border-gray-200 hover:bg-gray-100 transition-colors">Hủy bỏ</button>
                <button onclick="processCompleteOrder()" class="flex-1 py-3 rounded-xl font-bold text-white bg-emerald-500 shadow-md shadow-emerald-500/20 hover:bg-emerald-600 transition-colors">Xác nhận</button>
            </div>
        </div>
    </div>

    <style>
       .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    </style>

    {{-- LOGIC JAVASCRIPT GỌI API --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('w-64'); sidebar.classList.toggle('w-0');
        }

        let currentProcessingOrderId = null;

        function openConfirmModal(orderId) {
            currentProcessingOrderId = orderId;
            document.getElementById('modal-order-id-text').innerText = "#" + orderId;
            const modal = document.getElementById('confirm-modal');
            const content = document.getElementById('confirm-modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirm-modal');
            const content = document.getElementById('confirm-modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
            currentProcessingOrderId = null;
        }

        function processCompleteOrder() {
            const orderId = currentProcessingOrderId;
            closeConfirmModal();

            if (!orderId) return;

            // 1. GỌI API LÊN BACKEND ĐỂ CHUYỂN TRẠNG THÁI DB THÀNH 'completed'
            fetch(`/staff/api/orders/${orderId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // 2. TẠO HIỆU ỨNG THU NHỎ VÀ BIẾN MẤT THẺ CARD
                    const card = document.getElementById('order-card-' + orderId);
                    if(card) {
                        card.style.transform = 'scale(0.8)'; card.style.opacity = '0';
                        
                        setTimeout(() => {
                            card.remove(); 
                            
                            // 3. TỰ ĐỘNG GIẢM SỐ TRÊN THANH ĐIỀU HƯỚNG (NAV)
                            const badge = document.getElementById('new-order-badge');
                            if(badge) {
                                let currentCount = parseInt(badge.textContent) || 0;
                                if(currentCount > 0) badge.textContent = currentCount - 1;
                            }
                            
                            // Cập nhật text số lượng ở tiêu đề
                            const titleBadge = document.getElementById('total-orders-count');
                            if(titleBadge) {
                                let titleCount = parseInt(titleBadge.textContent) || 0;
                                if(titleCount > 0) titleBadge.textContent = titleCount - 1;
                            }

                            // 4. KIỂM TRA NẾU XÓA HẾT THÌ HIỆN BẢNG "TUYỆT VỜI"
                            const grid = document.getElementById('orders-grid');
                            if(grid.children.length === 0) {
                                grid.classList.add('hidden');
                                document.getElementById('empty-state').classList.remove('hidden');
                                document.getElementById('empty-state').classList.add('flex');
                            }
                        }, 300);
                    }

                    // Hiện Toast Thành Công Trượt ngang
                    const toast = document.getElementById('toast-notification');
                    toast.classList.remove('translate-x-[150%]');
                    setTimeout(() => { toast.classList.add('translate-x-[150%]'); }, 3000);
                }
            })
            .catch(err => {
                alert("Lỗi mạng! Không thể hoàn thành đơn.");
                console.error(err);
            });
        }
    </script>
</body>
</html>