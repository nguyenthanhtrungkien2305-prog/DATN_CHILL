<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Dashboard - Điểm Cộng Coffee</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { espresso: '#3e2723', coral: '#ff7043', cream: '#fbe9e7' } } }
        }
    </script>
</head>
<body class="bg-[#FAF7F2] font-sans antialiased overflow-hidden h-screen flex relative">

    {{-- TOAST NOTIFICATION (THÔNG BÁO TẠO ĐƠN) --}}
    <div id="toast-notification" class="fixed top-6 right-6 z-[200] transform transition-all duration-500 translate-x-[150%] opacity-0 bg-white border-l-4 border-emerald-500 shadow-2xl rounded-xl p-4 flex items-center gap-4 min-w-[280px]">
        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <h4 class="font-black text-espresso text-sm uppercase tracking-wider">Tạo đơn thành công!</h4>
            <p class="text-xs text-espresso/60 font-medium mt-0.5">Đơn hàng đã được cập nhật.</p>
        </div>
    </div>

    {{-- Gọi Sidebar chung --}}
    @include('staff.partials.sidebar', ['isOpen' => false])

    {{-- CONTAINER LÀM VIỆC CHÍNH --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300">
        
        {{-- TOP BAR --}}
        <div class="bg-espresso text-white px-4 lg:px-6 py-3 flex justify-between items-center shadow-md shrink-0 h-[64px]">
            <div class="font-bold text-lg flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-2 bg-white/10 hover:bg-coral rounded-lg transition-colors flex items-center justify-center focus:outline-none mr-2 group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <span class="hidden sm:block whitespace-nowrap tracking-wider font-serif">HỆ THỐNG POS - ĐIỂM CỘNG COFFEE</span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-white/70">Ca trực: <span class="font-bold text-white">{{ auth()->user()->name ?? 'Nhân viên' }}</span></span>
            </div>
        </div>

        {{-- Thanh Tiến Trình --}}
        <div class="bg-white py-3 border-b border-espresso/5 shadow-sm px-12 shrink-0">
            <div class="max-w-2xl mx-auto flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 w-full h-0.5 bg-gray-100 -z-10 -translate-y-1/2"></div>
                @php $steps = ['Gọi Món', 'Kiểm Tra', 'Tạo Đơn']; @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center bg-white px-4">
                        <div id="step-circle-{{ $index + 1 }}" class="w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 
                            {{ $index == 0 ? 'border-coral bg-coral text-white' : 'border-espresso/20 text-espresso/40 bg-white' }}">
                            {{ $index + 1 }}
                        </div>
                        <span id="step-text-{{ $index + 1 }}" class="text-[11px] font-bold transition-all duration-300 {{ $index == 0 ? 'text-coral' : 'text-espresso/40' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex-1 w-full overflow-hidden relative bg-[#FAF7F2]">
            <div id="step-track" class="flex h-full w-[300%] transition-transform duration-500 ease-in-out transform translate-x-0">
                
                {{-- GIAO ĐOẠN 1: GỌI MÓN --}}
                <div class="w-1/3 h-full p-4 lg:p-6 overflow-hidden grid grid-cols-12 gap-4 lg:gap-6 shrink-0">
                    <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden">
                        <div class="flex flex-col md:flex-row gap-4 bg-gray-50 p-3 rounded-xl border border-gray-100 shrink-0 items-center justify-between">
                            <div class="flex items-center gap-3 w-full md:w-1/2">
                                <span class="font-bold text-espresso text-sm whitespace-nowrap">Danh Mục</span>
                                <select id="category-filter" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-espresso text-sm bg-white focus:outline-none focus:border-coral">
                                    <option value="">--- Tất cả sản phẩm ---</option>
                                    @foreach($categories as $cat)
                                        @if(stripos($cat->name, 'Topping') === false)
                                            <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative w-full md:w-1/2">
                                <input type="text" id="search-product" placeholder="Nhập tên món cần tìm..." class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm text-espresso bg-white focus:outline-none focus:border-coral">
                                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>

                        {{-- TAB BUTTONS --}}
                        <div class="flex border-b border-gray-200 mb-4 mt-4 shrink-0">
                            <button type="button" onclick="switchPosTab('products')" id="btn-tab-products" class="flex-1 py-2 text-sm font-black uppercase tracking-wider text-coral border-b-2 border-coral transition-colors flex items-center justify-center gap-2">
                                ☕ Sản phẩm
                            </button>
                            <button type="button" onclick="switchPosTab('toppings')" id="btn-tab-toppings" class="flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2">
                                ✨ Topping
                            </button>
                        </div>

                        {{-- TAB 1: SẢN PHẨM CHÍNH --}}
                        <div id="content-tab-products" class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-2 custom-scrollbar p-2 rounded-xl block">
                            @foreach($products as $product)
                                <div onclick="openToppingModal({{ $product->product_id }}, '{{ $product->name }}', {{ $product->price }})" class="product-item flex items-center justify-between p-3 bg-white border border-espresso/10 rounded-xl hover:border-coral hover:shadow-md transition-all group cursor-pointer" data-category-id="{{ $product->category_id ?? '' }}" data-name="{{ $product->name }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-cream rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                            @if($product->image_url) <img src="{{ asset($product->image_url) }}" class="w-full h-full object-cover">
                                            @else <span class="text-xl">☕</span> @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-espresso text-sm group-hover:text-coral transition-colors line-clamp-1">{{ $product->name }}</h4>
                                            <p class="text-xs text-coral font-medium">{{ number_format($product->price, 0, ',', '.') }}đ</p>
                                        </div>
                                    </div>
                                    <button class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-espresso group-hover:bg-coral group-hover:text-white transition-all shrink-0">➕</button>
                                </div>
                            @endforeach
                        </div>

                        {{-- TAB 2: TOPPING --}}
                        <div id="content-tab-toppings" class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-2 custom-scrollbar p-2 rounded-xl hidden">
                            @foreach($toppings as $topping)
                                <div onclick="addDirectItem({{ $topping->product_id }}, '{{ $topping->name }}', {{ $topping->price }})" class="product-item flex items-center justify-between p-3 bg-white border border-emerald-100 rounded-xl hover:border-emerald-500 hover:shadow-md transition-all group cursor-pointer" data-category-id="{{ $topping->category_id ?? '' }}" data-name="{{ $topping->name }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                            @if($topping->image_url) <img src="{{ asset($topping->image_url) }}" class="w-full h-full object-cover">
                                            @else <span class="text-xl">✨</span> @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-espresso text-sm group-hover:text-emerald-600 transition-colors line-clamp-1">{{ $topping->name }}</h4>
                                            <p class="text-xs text-emerald-500 font-medium">+{{ number_format($topping->price, 0, ',', '.') }}đ</p>
                                        </div>
                                    </div>
                                    <button class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-all shrink-0">➕</button>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden">
                        <h3 class="text-base font-black text-espresso mb-3 uppercase tracking-wider shrink-0 border-b pb-2 border-dashed">Món Đang Chọn</h3>
                        <div class="flex-1 min-h-0 bg-[#FAF7F2] rounded-2xl border border-gray-200 p-3 mb-4 overflow-y-auto custom-scrollbar flex flex-col gap-2" id="step1-bill-container"></div>
                        <div class="shrink-0 bg-gray-50 rounded-xl p-3 border border-gray-200">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-espresso text-xs">TẠM TÍNH</span>
                                <span class="font-black text-xl text-coral total-amount-text">0đ</span>
                            </div>
                            <button onclick="goToStep(2)" class="w-full py-3.5 bg-coral text-white rounded-xl font-black text-sm uppercase tracking-wider shadow-md hover:bg-[#d5523b] transition-all flex items-center justify-center gap-2">
                                Kiểm tra đơn &rarr;
                            </button>
                        </div>
                    </div>
                </div>

                {{-- GIAO ĐOẠN 2: KIỂM TRA --}}
                <div class="w-1/3 h-full p-4 lg:p-6 overflow-hidden grid grid-cols-12 gap-4 lg:gap-6 shrink-0">
                    <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden">
                        <div class="flex justify-between items-center mb-4 shrink-0">
                            <h3 class="text-lg font-black text-espresso uppercase tracking-wider flex items-center gap-2">📋 Bảng Kiểm Tra Chi Tiết</h3>
                            <button onclick="goToStep(1)" class="text-xs bg-gray-100 hover:bg-espresso hover:text-white px-3 py-1.5 rounded-lg font-bold transition-colors">&larr; Thêm món khác</button>
                        </div>
                        <div class="flex-1 min-h-0 border border-gray-200 rounded-xl overflow-hidden bg-gray-50/30 flex flex-col">
                            <div class="overflow-x-auto w-full flex-1 custom-scrollbar">
                                <table class="w-full text-left border-collapse text-sm">
                                    <thead class="bg-espresso text-white uppercase text-[11px] tracking-wider sticky top-0 z-10">
                                        <tr>
                                            <th class="py-3 px-4 w-12 text-center">STT</th>
                                            <th class="py-3 px-4">Sản phẩm</th>
                                            <th class="py-3 px-4 w-32 text-center">Số lượng</th>
                                            <th class="py-3 px-4 text-right w-32">Thành tiền</th>
                                            <th class="py-3 px-4 w-24 text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="review-table-body" class="divide-y divide-gray-200 bg-white"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden justify-between">
                        <div>
                            <h3 class="text-base font-black text-espresso mb-4 uppercase tracking-wider border-b pb-2 border-dashed">💳 Thanh Toán</h3>
                            <div class="space-y-3 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-espresso/70 mb-1">Tên khách hàng</label>
                                    <input type="text" id="review_customer_name" oninput="syncCustomerInfo(this.value, 'customer_name')" placeholder="Nhập tên..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-coral">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-espresso/70 mb-1">Ghi chú đơn hàng</label>
                                    <textarea id="review_order_note" oninput="syncCustomerInfo(this.value, 'order_note')" placeholder="Ghi chú pha chế..." rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-coral resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-espresso/70 mb-1">Phương thức</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button class="py-2.5 border-2 border-coral bg-coral/5 text-coral font-bold text-xs rounded-xl">💵 Tiền mặt</button>
                                        <button class="py-2.5 border border-gray-200 text-espresso font-bold text-xs rounded-xl hover:border-coral transition-colors">💳 Chuyển khoản</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 shadow-sm mt-auto">
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-dashed border-gray-200">
                                <span class="font-bold text-espresso text-xs">TỔNG TIỀN</span>
                                <span class="font-black text-2xl text-coral total-amount-text" id="review-total-display">0đ</span>
                            </div>
                            <button onclick="goToStep(3)" class="w-full py-4 bg-emerald-500 rounded-xl font-black text-white hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition-all uppercase tracking-widest flex items-center justify-center gap-2">
                                THANH TOÁN &rarr;
                            </button>
                        </div>
                    </div>
                </div>

                {{-- GIAO ĐOẠN 3: TẠO ĐƠN --}}
                <div class="w-1/3 h-full p-6 flex flex-col items-center justify-center shrink-0">
                    <div class="bg-white rounded-3xl shadow-xl border border-espresso/5 p-8 w-full max-w-lg flex flex-col items-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-coral/5 rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl"></div>

                        <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-6 shadow-inner z-10">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        
                        <h2 class="text-2xl font-black text-espresso mb-6 uppercase tracking-widest z-10">Tạo Đơn Hàng</h2>
                        
                        <div class="w-full space-y-3 mb-8 bg-[#FAF7F2] p-5 rounded-2xl border border-espresso/10 z-10">
                            <div class="flex justify-between items-end">
                                <span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Khách hàng:</span> 
                                <span id="summary-customer" class="font-black text-espresso text-sm text-right">Khách Vãng Lai</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Ghi chú:</span> 
                                <span id="summary-note" class="font-bold text-espresso text-sm text-right line-clamp-1 italic">Không có</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Số món:</span> 
                                <span id="summary-count" class="font-black text-espresso text-sm">0 ly</span>
                            </div>
                            
                            <div class="border-t-2 border-dashed border-gray-300 my-4 pt-4 flex justify-between items-center">
                                <span class="text-espresso font-black uppercase tracking-widest">Cần Thu:</span>
                                <span id="summary-total" class="font-black text-3xl text-coral">0đ</span>
                            </div>
                        </div>

                        <button onclick="submitFinalOrder()" class="w-full py-4 bg-coral text-white font-black rounded-xl uppercase tracking-widest shadow-lg shadow-coral/30 hover:bg-[#d5523b] hover:-translate-y-0.5 transition-all z-10">
                            IN BILL & XÁC NHẬN TẠO ĐƠN
                        </button>
                        
                        <button onclick="goToStep(2)" class="mt-5 text-xs font-bold text-espresso/50 hover:text-espresso transition-colors z-10 flex items-center gap-1">
                            &larr; Quay lại kiểm tra
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <input type="hidden" id="customer_name">
    <input type="hidden" id="order_note">

    {{-- MODAL TOPPING MỚI (Tăng kích thước & Có thanh tìm kiếm) --}}
    <div id="topping-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.2s;">
        
        {{-- KÍCH THƯỚC ĐÃ ĐƯỢC CẬP NHẬT THEO YÊU CẦU: W-463px và H-468px --}}
        <div class="bg-white rounded-[24px] shadow-2xl overflow-hidden flex flex-col scale-95 transition-transform duration-200 w-[463px] h-[468px]" id="topping-modal-content">
            
            {{-- HEADER BẢNG --}}
            <div class="bg-espresso text-white px-6 py-4 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-lg leading-tight" id="modal-product-name">Tên món</h3>
                    <p class="text-coral font-medium text-sm" id="modal-product-price">0đ</p>
                </div>
                <button onclick="closeToppingModal()" class="text-white/50 hover:text-white bg-white/10 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
            </div>

            {{-- THANH TÌM KIẾM BÊN TRONG BẢNG --}}
            <div class="px-5 py-3 bg-[#FAF7F2] border-b border-gray-200 shrink-0">
                <div class="relative">
                    <input type="text" id="search-topping" placeholder="Tìm topping nhanh..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-espresso bg-white focus:outline-none focus:border-coral" onkeyup="filterModalToppings()">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            {{-- DANH SÁCH TOPPING KÈM SCROLL-BAR TỰ ĐỘNG --}}
            <div class="p-5 overflow-y-auto custom-scrollbar flex-1 bg-[#FAF7F2]">
                <div id="modal-toppings-list" class="space-y-3"></div>
            </div>
            
            {{-- NÚT LƯU BẢNG --}}
            <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-between items-center shrink-0 shadow-[0_-10px_20px_rgba(0,0,0,0.03)]">
                <div><span class="text-xs text-espresso/60 font-medium">Tạm tính (1 ly):</span><div id="modal-total-price" class="text-coral font-black text-xl">0đ</div></div>
                <button onclick="confirmToppingModal()" class="bg-coral text-white px-8 py-3 rounded-xl font-bold hover:bg-[#d5523b]">Lưu vào Đơn</button>
            </div>
        </div>
    </div>

    <style>
       .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    </style>

    <script>
        const toppingsData = @json($toppings);
        const baseUrl = "{{ url('/') }}"; 
    </script>

    {{-- LOGIC JAVASCRIPT --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('w-0'); sidebar.classList.toggle('w-64');
        }

        // --- HÀM CHUYỂN TAB ---
        function switchPosTab(tab) {
            const btnProducts = document.getElementById('btn-tab-products');
            const btnToppings = document.getElementById('btn-tab-toppings');
            const contentProducts = document.getElementById('content-tab-products');
            const contentToppings = document.getElementById('content-tab-toppings');

            if (tab === 'products') {
                btnProducts.className = "flex-1 py-2 text-sm font-black uppercase tracking-wider text-coral border-b-2 border-coral transition-colors flex items-center justify-center gap-2";
                btnToppings.className = "flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2";
                
                contentProducts.classList.remove('hidden'); contentProducts.classList.add('block');
                contentToppings.classList.remove('block'); contentToppings.classList.add('hidden');
            } else {
                btnToppings.className = "flex-1 py-2 text-sm font-black uppercase tracking-wider text-emerald-500 border-b-2 border-emerald-500 transition-colors flex items-center justify-center gap-2";
                btnProducts.className = "flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2";
                
                contentToppings.classList.remove('hidden'); contentToppings.classList.add('block');
                contentProducts.classList.remove('block'); contentProducts.classList.add('hidden');
            }
        }

        // --- HÀM THÊM TRỰC TIẾP (DÀNH CHO TAB TOPPING) ---
        function addDirectItem(productId, name, price) {
            let existingItem = posCart.find(i => i.productId === productId && Object.keys(i.toppings).length === 0);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                posCart.push({
                    cartItemId: Date.now(),
                    productId: productId,
                    name: name,
                    price: price,
                    quantity: 1,
                    toppings: {}
                });
            }
            renderBill();
        }

        function goToStep(stepNumber) {
            if(stepNumber === 3 && posCart.length === 0) {
                alert("Hóa đơn trống! Vui lòng gọi món trước khi tạo đơn."); return;
            }
            const track = document.getElementById('step-track');
            track.style.transform = `translateX(-${(stepNumber - 1) * 33.3333}%)`;

            for (let i = 1; i <= 3; i++) {
                const circle = document.getElementById(`step-circle-${i}`);
                const text = document.getElementById(`step-text-${i}`);
                if (i === stepNumber) {
                    circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-coral bg-coral text-white shadow-md shadow-coral/20";
                    text.className = "text-[11px] font-black text-coral transition-all duration-300";
                } else if (i < stepNumber) {
                    circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-emerald-500 bg-emerald-500 text-white";
                    text.className = "text-[11px] font-bold text-emerald-500 transition-all duration-300";
                } else {
                    circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-espresso/20 text-espresso/40 bg-white";
                    text.className = "text-[11px] font-bold text-espresso/40 transition-all duration-300";
                }
            }
            if(stepNumber === 2) { renderReviewTable(); }
            if(stepNumber === 3) { renderStep3Summary(); }
        }

        function renderStep3Summary() {
            const customerName = document.getElementById('review_customer_name').value.trim();
            const orderNote = document.getElementById('review_order_note').value.trim();
            let totalItems = 0;
            posCart.forEach(i => totalItems += i.quantity);

            document.getElementById('summary-customer').textContent = customerName === '' ? 'Khách Vãng Lai' : customerName;
            document.getElementById('summary-note').textContent = orderNote === '' ? 'Không có ghi chú' : orderNote;
            document.getElementById('summary-count').textContent = totalItems + " ly";
            document.getElementById('summary-total').textContent = document.getElementById('review-total-display').textContent;
        }

        function submitFinalOrder() {
            const customerName = document.getElementById('review_customer_name').value.trim();
            const orderNote = document.getElementById('review_order_note').value.trim();
            
            let totalAmount = 0;
            posCart.forEach(item => {
                let toppingTotal = 0;
                for (let tid in item.toppings) {
                    if (item.toppings[tid] > 0) {
                        let toppingData = toppingsData.find(t => t.product_id == tid);
                        if (toppingData) toppingTotal += toppingData.price * item.toppings[tid];
                    }
                }
                totalAmount += (item.price + toppingTotal) * item.quantity;
            });

            const payload = { customer_name: customerName, order_note: orderNote, total_amount: totalAmount, items: posCart };

            fetch('/staff/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const toast = document.getElementById('toast-notification');
                    toast.classList.remove('translate-x-[150%]', 'opacity-0');
                    setTimeout(() => { toast.classList.add('translate-x-[150%]', 'opacity-0'); }, 3000);

                    posCart = [];
                    document.getElementById('review_customer_name').value = '';
                    document.getElementById('review_order_note').value = '';
                    renderBill();
                    setTimeout(() => { goToStep(1); }, 800);
                }
            })
            .catch(err => {
                alert("Đã xảy ra lỗi khi tạo đơn hàng. Vui lòng kiểm tra lại kết nối!");
                console.error(err);
            });
        }

        function syncCustomerInfo(value, targetId) {
            document.getElementById(targetId).value = value;
            const reviewInput = document.getElementById('review_' + targetId);
            if(reviewInput && reviewInput !== document.activeElement) reviewInput.value = value;
        }

        let posCart = []; 
        let modalState = { cartItemId: null, productId: null, productName: '', productPrice: 0, toppings: {} };
        function formatVND(amount) { return new Intl.NumberFormat('vi-VN').format(amount) + 'đ'; }

        // --- HÀM MỞ BẢNG MODAL TOPPING ---
        function openToppingModal(productId, name, price, editCartItemId = null) {
            modalState.productId = productId; modalState.productName = name; modalState.productPrice = price; modalState.cartItemId = editCartItemId;
            if (editCartItemId) {
                const item = posCart.find(i => i.cartItemId === editCartItemId); modalState.toppings = { ...item.toppings };
            } else { modalState.toppings = {}; }
            
            document.getElementById('modal-product-name').textContent = name;
            document.getElementById('modal-product-price').textContent = formatVND(price);
            
            // Tự động xóa sạch nội dung tìm kiếm cũ khi mở lại bảng
            const searchInput = document.getElementById('search-topping');
            if(searchInput) searchInput.value = '';

            renderModalToppings();
            
            const modal = document.getElementById('topping-modal'); const content = document.getElementById('topping-modal-content');
            modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }
        
        function closeToppingModal() {
            const modal = document.getElementById('topping-modal'); const content = document.getElementById('topping-modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }
        
        // --- HÀM TÌM KIẾM BÊN TRONG BẢNG MODAL ---
        function filterModalToppings() {
            const searchTerm = document.getElementById('search-topping').value.toLowerCase().trim();
            const toppingItems = document.querySelectorAll('.modal-topping-item');
            toppingItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                item.style.display = name.includes(searchTerm) ? 'flex' : 'none';
            });
        }

        function updateModalTopping(toppingId, delta) {
            let currentQty = modalState.toppings[toppingId] || 0; let newQty = currentQty + delta; if (newQty < 0) newQty = 0;
            modalState.toppings[toppingId] = newQty; renderModalToppings(); 
        }

        function renderModalToppings() {
            const list = document.getElementById('modal-toppings-list'); list.innerHTML = ''; let currentTotal = modalState.productPrice;
            toppingsData.forEach(t => {
                const qty = modalState.toppings[t.product_id] || 0; currentTotal += qty * t.price;
                let imgHtml = t.image_url ? `<img src="${baseUrl}/${t.image_url}" class="w-full h-full object-cover">` : `<span class="text-lg">✨</span>`;
                
                // Nạp data-name vào HTML để bộ tìm kiếm hoạt động
                list.innerHTML += `
                    <div class="modal-topping-item flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl shadow-sm" data-name="${t.name}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100 shrink-0">${imgHtml}</div>
                            <div><p class="font-bold text-sm text-espresso leading-tight">${t.name}</p><p class="text-xs text-coral font-bold">+${formatVND(t.price)}</p></div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <button onclick="updateModalTopping(${t.product_id}, -1)" class="w-7 h-7 rounded-md bg-gray-100 font-bold ${qty === 0 ? 'opacity-50 pointer-events-none' : ''}">-</button>
                            <span class="w-4 text-center text-sm font-black text-espresso">${qty}</span>
                            <button onclick="updateModalTopping(${t.product_id}, 1)" class="w-7 h-7 rounded-md bg-gray-100 font-bold">+</button>
                        </div>
                    </div>`;
            });
            document.getElementById('modal-total-price').textContent = formatVND(currentTotal);
            
            // Giữ kết quả lọc khi đang cộng trừ số lượng
            filterModalToppings(); 
        }

        function areToppingsEqual(t1, t2) {
            const keys1 = Object.keys(t1).filter(k => t1[k] > 0); const keys2 = Object.keys(t2).filter(k => t2[k] > 0);
            if (keys1.length !== keys2.length) return false; for (let k of keys1) { if (t1[k] !== t2[k]) return false; } return true;
        }

        function confirmToppingModal() {
            if (modalState.cartItemId) {
                let item = posCart.find(i => i.cartItemId === modalState.cartItemId); if(item) { item.toppings = { ...modalState.toppings }; }
            } else {
                let existingItem = posCart.find(i => i.productId === modalState.productId && areToppingsEqual(i.toppings, modalState.toppings));
                if (existingItem) { existingItem.quantity += 1; } 
                else { posCart.push({ cartItemId: Date.now(), productId: modalState.productId, name: modalState.productName, price: modalState.productPrice, quantity: 1, toppings: { ...modalState.toppings } }); }
            }
            renderBill(); closeToppingModal();
        }

        function changeQty(cartItemId, delta) {
            let item = posCart.find(i => i.cartItemId === cartItemId); if (!item) return;
            item.quantity += delta; if (item.quantity <= 0) { posCart = posCart.filter(i => i.cartItemId !== cartItemId); } renderBill();
        }
        function removeCartItem(cartItemId) { posCart = posCart.filter(i => i.cartItemId !== cartItemId); renderBill(); }

        function renderBill() {
            const step1Container = document.getElementById('step1-bill-container');
            const totalDisplays = document.querySelectorAll('.total-amount-text');
            if (posCart.length === 0) {
                step1Container.innerHTML = `<div class="h-full flex flex-col justify-center items-center text-gray-400 py-12"><p class="text-xs">Chưa có sản phẩm nào</p></div>`;
                totalDisplays.forEach(el => el.textContent = "0đ"); return;
            }
            step1Container.innerHTML = ""; let totalAmount = 0;
            posCart.forEach(item => {
                let toppingTotal = 0; let toppingTexts = [];
                for (let tid in item.toppings) {
                    let tqty = item.toppings[tid];
                    if (tqty > 0) {
                        let toppingData = toppingsData.find(t => t.product_id == tid);
                        if (toppingData) { toppingTotal += toppingData.price * tqty; toppingTexts.push(`${toppingData.name} (x${tqty})`); }
                    }
                }
                let itemUnitPrice = item.price + toppingTotal; let itemTotal = itemUnitPrice * item.quantity; totalAmount += itemTotal;
                let toppingHtml = toppingTexts.length > 0 ? `<p class="text-[10px] text-espresso/60 italic leading-tight mt-0.5">+ ${toppingTexts.join(', ')}</p>` : '';
                step1Container.innerHTML += `
                    <div class="bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center text-xs">
                        <div class="flex-1 pr-2"><h5 class="font-bold text-espresso line-clamp-1">${item.name}</h5>${toppingHtml}<span class="text-[11px] text-coral font-bold mt-1 block">${formatVND(itemTotal)}</span></div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="changeQty(${item.cartItemId}, -1)" class="w-5 h-5 bg-gray-100 rounded font-black flex items-center justify-center hover:bg-coral hover:text-white">-</button>
                            <span class="font-bold text-espresso text-center min-w-[12px]">${item.quantity}</span>
                            <button onclick="changeQty(${item.cartItemId}, 1)" class="w-5 h-5 bg-gray-100 rounded font-black flex items-center justify-center hover:bg-coral hover:text-white">+</button>
                        </div>
                    </div>`;
            });
            totalDisplays.forEach(el => el.textContent = formatVND(totalAmount));
            if(document.getElementById('step-track').style.transform.includes('33.3333%')) { renderReviewTable(); }
        }

        function renderReviewTable() {
            const tbody = document.getElementById('review-table-body'); const totalDisplay = document.getElementById('review-total-display'); tbody.innerHTML = "";
            if(posCart.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-12 text-center text-gray-400 font-medium">Không có sản phẩm nào để kiểm tra!</td></tr>`;
                totalDisplay.textContent = "0đ"; return;
            }
            let totalAmount = 0;
            posCart.forEach((item, index) => {
                let toppingTotal = 0; let toppingTexts = [];
                for (let tid in item.toppings) {
                    let tqty = item.toppings[tid];
                    if (tqty > 0) {
                        let toppingData = toppingsData.find(t => t.product_id == tid);
                        if (toppingData) { toppingTotal += toppingData.price * tqty; toppingTexts.push(`<span class="bg-coral/5 text-coral border border-coral/10 px-2 py-0.5 rounded-md text-[11px] font-medium">${toppingData.name} x${tqty}</span>`); }
                    }
                }
                let itemUnitPrice = item.price + toppingTotal; let itemTotal = itemUnitPrice * item.quantity; totalAmount += itemTotal;
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-4 font-bold text-espresso/60 text-center">${index + 1}</td>
                        <td class="py-4 px-4"><span class="font-black text-espresso block text-sm">${item.name}</span><div class="flex flex-wrap gap-1 mt-1.5">${toppingTexts.length > 0 ? toppingTexts.join('') : '<span class="text-xs text-gray-400 italic">Không thêm topping</span>'}</div></td>
                        <td class="py-4 px-4"><div class="flex items-center justify-center gap-2.5"><button onclick="changeQty(${item.cartItemId}, -1)" class="w-7 h-7 bg-gray-100 rounded-lg font-bold flex items-center justify-center hover:bg-coral hover:text-white transition-colors">-</button><span class="font-black text-espresso text-sm w-4 text-center">${item.quantity}</span><button onclick="changeQty(${item.cartItemId}, 1)" class="w-7 h-7 bg-gray-100 rounded-lg font-bold flex items-center justify-center hover:bg-coral hover:text-white transition-colors">+</button></div></td>
                        <td class="py-4 px-4 text-right font-black text-espresso text-sm">${formatVND(itemTotal)}</td>
                        <td class="py-4 px-4"><div class="flex items-center justify-center gap-2"><button onclick="openToppingModal(${item.productId}, '${item.name}', ${item.price}, ${item.cartItemId})" class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white transition-all">Sửa</button><button onclick="removeCartItem(${item.cartItemId})" class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 p-2 rounded-lg transition-colors">🗑️</button></div></td>
                    </tr>`;
            });
            totalDisplay.textContent = formatVND(totalAmount);
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderBill();
            const searchInput = document.getElementById('search-product'); const categorySelect = document.getElementById('category-filter');
            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase().trim(); const selectedCategory = categorySelect.value;
                document.querySelectorAll('.product-item').forEach(item => {
                    const matchesSearch = item.getAttribute('data-name').toLowerCase().includes(searchTerm);
                    const matchesCategory = selectedCategory === "" || item.getAttribute('data-category-id') === selectedCategory;
                    item.style.display = (matchesSearch && matchesCategory) ? 'flex' : 'none';
                });
            }
            searchInput.addEventListener('input', filterProducts); categorySelect.addEventListener('change', filterProducts);
        });
    </script>
</body>
</html>