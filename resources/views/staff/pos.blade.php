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

    {{-- TOAST NOTIFICATION --}}
    <div id="toast-notification" class="fixed top-6 right-6 z-[200] transform transition-all duration-500 translate-x-[150%] opacity-0 bg-white border-l-4 border-emerald-500 shadow-2xl rounded-xl p-4 flex items-center gap-4 min-w-[280px]">
        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <h4 class="font-black text-espresso text-sm uppercase tracking-wider">Tạo đơn thành công!</h4>
            <p class="text-xs text-espresso/60 font-medium mt-0.5">Đơn hàng đã được cập nhật.</p>
        </div>
    </div>

    @include('staff.partials.sidebar', ['isOpen' => false])

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
                        <div id="step-circle-{{ $index + 1 }}" class="w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 {{ $index == 0 ? 'border-coral bg-coral text-white' : 'border-espresso/20 text-espresso/40 bg-white' }}">
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

                        <div class="flex border-b border-gray-200 mb-4 mt-4 shrink-0">
                            <button type="button" onclick="switchPosTab('products')" id="btn-tab-products" class="flex-1 py-2 text-sm font-black uppercase tracking-wider text-coral border-b-2 border-coral transition-colors flex items-center justify-center gap-2">Sản phẩm</button>
                            <button type="button" onclick="switchPosTab('combos')" id="btn-tab-combos" class="flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-amber-600 transition-colors flex items-center justify-center gap-2">Combo</button>
                            <button type="button" onclick="switchPosTab('toppings')" id="btn-tab-toppings" class="flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2">Topping</button>
                        </div>

                        <div id="content-tab-products" class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-2 custom-scrollbar p-2 rounded-xl block">
                            @foreach($products as $product)
                                <div onclick="openToppingModal({{ $product->product_id }}, '{{ $product->name }}', {{ $product->price }})" class="product-item flex items-center justify-between p-3 bg-white border border-espresso/10 rounded-xl hover:border-coral hover:shadow-md transition-all group cursor-pointer" data-category-id="{{ $product->category_id ?? '' }}" data-name="{{ $product->name }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-cream rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                            @if($product->image_url) <img src="{{ asset($product->image_url) }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-espresso text-sm group-hover:text-coral transition-colors line-clamp-1">{{ $product->name }}</h4>
                                            <p class="text-xs text-coral font-medium">{{ number_format($product->price, 0, ',', '.') }}đ</p>
                                        </div>
                                    </div>
                                    <button class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-espresso font-bold group-hover:bg-coral group-hover:text-white transition-all shrink-0">+</button>
                                </div>
                            @endforeach
                        </div>

                        {{-- TAB COMBO --}}
                        <div id="content-tab-combos" class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-2 custom-scrollbar p-2 rounded-xl hidden">
                            @if(isset($combos) && count($combos) > 0)
                                @foreach($combos as $combo)
                                    <div onclick="addDirectItem('combo_{{ $combo->combo_id }}', '[COMBO] {{ $combo->name }}', {{ $combo->price }})" class="product-item flex items-center justify-between p-3 bg-white border border-amber-200 rounded-xl hover:border-amber-500 hover:shadow-md transition-all group cursor-pointer" data-category-id="" data-name="{{ $combo->name }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                                @if($combo->image_url) 
                                                    <img src="{{ asset($combo->image_url) }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xl">🎁</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded uppercase">COMBO</span>
                                                    <h4 class="font-bold text-espresso text-sm group-hover:text-amber-600 transition-colors line-clamp-1">{{ $combo->name }}</h4>
                                                </div>
                                                <p class="text-xs text-amber-600 font-extrabold mt-0.5">{{ number_format($combo->price, 0, ',', '.') }}đ 
                                                    @if(!empty($combo->original_price) && $combo->original_price > $combo->price)
                                                        <span class="text-[10px] text-gray-400 line-through font-normal ml-1">{{ number_format($combo->original_price, 0, ',', '.') }}đ</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <button class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 font-bold group-hover:bg-amber-500 group-hover:text-white transition-all shrink-0">+</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-6 text-center text-gray-400 text-sm italic">
                                    Chưa có gói Combo nào khả dụng.
                                </div>
                            @endif
                        </div>

                        <div id="content-tab-toppings" class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-2 custom-scrollbar p-2 rounded-xl hidden">
                            @foreach($toppings as $topping)
                                <div onclick="addDirectItem({{ $topping->product_id }}, '{{ $topping->name }}', {{ $topping->price }})" class="product-item flex items-center justify-between p-3 bg-white border border-emerald-100 rounded-xl hover:border-emerald-500 hover:shadow-md transition-all group cursor-pointer" data-category-id="{{ $topping->category_id ?? '' }}" data-name="{{ $topping->name }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                            @if($topping->image_url) <img src="{{ asset($topping->image_url) }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-espresso text-sm group-hover:text-emerald-600 transition-colors line-clamp-1">{{ $topping->name }}</h4>
                                            <p class="text-xs text-emerald-500 font-medium">+{{ number_format($topping->price, 0, ',', '.') }}đ</p>
                                        </div>
                                    </div>
                                    <button class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold group-hover:bg-emerald-500 group-hover:text-white transition-all shrink-0">+</button>
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
                            <button onclick="goToStep(2)" class="w-full py-3.5 bg-coral text-white rounded-xl font-black text-sm uppercase tracking-wider shadow-md hover:bg-[#d5523b] transition-all flex items-center justify-center gap-2">Kiểm tra đơn &rarr;</button>
                        </div>
                    </div>
                </div>

                {{-- GIAO ĐOẠN 2: KIỂM TRA --}}
                <div class="w-1/3 h-full p-4 lg:p-6 overflow-hidden grid grid-cols-12 gap-4 lg:gap-6 shrink-0">
                    <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden">
                        <div class="flex justify-between items-center mb-4 shrink-0">
                            <h3 class="text-lg font-black text-espresso uppercase tracking-wider flex items-center gap-2">Bảng Kiểm Tra Chi Tiết</h3>
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
                                            <th class="py-3 px-4 w-24 text-center">HĐ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="review-table-body" class="divide-y divide-gray-200 bg-white"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border border-espresso/10 p-4 lg:p-6 shadow-sm flex flex-col h-full overflow-hidden justify-between">
                        <div>
                            <h3 class="text-base font-black text-espresso mb-3 uppercase tracking-wider border-b pb-2 border-dashed">Thanh Toán</h3>
                            
                            <!-- Nút mở modal tìm kiếm khách hàng -->
                            <button type="button" onclick="openCustomerModal()" class="w-full mb-3 py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                Tìm & Chọn Khách Hàng (Tích điểm)
                            </button>

                            <!-- Khung hiển thị thông tin khách hàng đã chọn -->
                            <div id="selected-customer-card" class="hidden mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-black text-emerald-900" id="card-customer-name">Khách Hàng</span>
                                            <span class="bg-emerald-200 text-emerald-800 text-[10px] font-extrabold px-1.5 py-0.5 rounded">Thành viên</span>
                                        </div>
                                        <p class="text-[11px] text-emerald-700 font-semibold mt-0.5" id="card-customer-phone">SĐT: ---</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="text-[11px] font-bold text-emerald-900">Hiện có: <strong id="card-customer-points" class="text-emerald-700 font-black">0</strong> điểm</span>
                                            <span class="text-[10px] font-bold text-emerald-600 bg-white px-2 py-0.5 rounded border border-emerald-200 shadow-2xs">+<span id="card-projected-points">0</span> điểm đơn này</span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearSelectedCustomer()" class="text-xs text-red-500 hover:text-red-700 font-bold bg-white hover:bg-red-50 px-2 py-1 rounded-lg border border-red-200 transition-colors shrink-0">✕ Đổi khách</button>
                                </div>
                            </div>

                            <div class="space-y-3 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-espresso/70 mb-1">Tên khách hàng</label>
                                    <input type="text" id="review_customer_name" oninput="syncCustomerInfo(this.value, 'customer_name')" placeholder="Nhập tên khách hàng..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-coral">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-espresso/70 mb-1">Ghi chú đơn hàng</label>
                                    <textarea id="review_order_note" oninput="syncCustomerInfo(this.value, 'order_note')" placeholder="Ghi chú pha chế..." rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-coral resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 shadow-sm mt-auto">
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-dashed border-gray-200">
                                <span class="font-bold text-espresso text-xs">TỔNG TIỀN</span>
                                <span class="font-black text-2xl text-coral total-amount-text" id="review-total-display">0đ</span>
                            </div>
                            <button onclick="goToStep(3)" class="w-full py-4 bg-emerald-500 rounded-xl font-black text-white hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition-all uppercase tracking-widest flex items-center justify-center gap-2">THANH TOÁN &rarr;</button>
                        </div>
                    </div>
                </div>

                {{-- GIAO ĐOẠN 3: TẠO ĐƠN --}}
                <div class="w-1/3 h-full p-6 flex flex-col items-center justify-center shrink-0">
                    <div class="bg-white rounded-3xl shadow-xl border border-espresso/5 p-8 w-full max-w-lg flex flex-col items-center relative overflow-hidden">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-6 shadow-inner z-10">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-espresso mb-6 uppercase tracking-widest z-10">Tạo Đơn Hàng</h2>
                        <div class="w-full space-y-3 mb-8 bg-[#FAF7F2] p-5 rounded-2xl border border-espresso/10 z-10">
                            <div class="flex justify-between items-end"><span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Khách:</span><span id="summary-customer" class="font-black text-espresso text-sm text-right">Khách Vãng Lai</span></div>
                            <div class="flex justify-between items-end"><span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Ghi chú:</span><span id="summary-note" class="font-bold text-espresso text-sm text-right line-clamp-1 italic">Không có</span></div>
                            <div class="flex justify-between items-end"><span class="text-espresso/60 font-bold text-xs uppercase tracking-wider">Số món:</span><span id="summary-count" class="font-black text-espresso text-sm">0 ly</span></div>
                            <div class="border-t-2 border-dashed border-gray-300 my-4 pt-4 flex justify-between items-center">
                                <span class="text-espresso font-black uppercase tracking-widest">Cần Thu:</span>
                                <span id="summary-total" class="font-black text-3xl text-coral">0đ</span>
                            </div>
                        </div>
                        <button onclick="submitFinalOrder()" class="w-full py-4 bg-coral text-white font-black rounded-xl uppercase tracking-widest shadow-lg shadow-coral/30 hover:bg-[#d5523b] hover:-translate-y-0.5 transition-all z-10">IN BILL & XÁC NHẬN TẠO ĐƠN</button>
                        <button onclick="goToStep(2)" class="mt-5 text-xs font-bold text-espresso/50 hover:text-espresso transition-colors z-10 flex items-center gap-1">&larr; Quay lại kiểm tra</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <input type="hidden" id="customer_name">
    <input type="hidden" id="customer_phone">
    <input type="hidden" id="selected_user_id">
    <input type="hidden" id="order_note">

    {{-- MODAL TÌM KIẾM KHÁCH HÀNG THÂN THIẾT (TÍCH ĐIỂM) --}}
    <div id="customer-search-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.2s;">
        <div class="bg-white rounded-[24px] shadow-2xl overflow-hidden flex flex-col scale-95 transition-transform duration-200 w-[720px] max-h-[85vh]" id="customer-search-modal-content">
            <div class="bg-espresso text-white px-6 py-4 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-lg leading-tight flex items-center gap-2">Danh Sách Khách Hàng Thân Thiết</h3>
                    <p class="text-coral font-medium text-xs mt-0.5">Tìm kiếm khách hàng theo Tên, Số điện thoại hoặc Email để tích điểm</p>
                </div>
                <button type="button" onclick="closeCustomerModal()" class="text-white/50 hover:text-white bg-white/10 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
            </div>
            
            <div class="p-4 bg-[#FAF7F2] border-b border-gray-200 shrink-0">
                <div class="relative">
                    <input type="text" id="customer-search-input" placeholder="Nhập tên, số điện thoại hoặc email khách hàng cần tìm..." class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-2.5 text-sm text-espresso bg-white focus:outline-none focus:border-coral shadow-xs" oninput="debounceCustomerSearch()">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-white min-h-[280px]">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-gray-100 text-espresso uppercase text-[11px] font-black tracking-wider sticky top-0 z-10">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">STT</th>
                            <th class="py-3 px-3">Khách Hàng</th>
                            <th class="py-3 px-3">Số Điện Thoại</th>
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3 text-center">Điểm Hiện Có</th>
                            <th class="py-3 px-3 text-center w-24">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="customer-table-body" class="divide-y divide-gray-100">
                        <!-- Content rendered dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center text-xs text-espresso/60 shrink-0">
                <span class="font-medium">Đơn hàng tích <strong>1 điểm</strong> cho mỗi <strong>10.000đ</strong> thanh toán</span>
                <button type="button" onclick="closeCustomerModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-espresso rounded-xl font-bold transition-colors">Đóng</button>
            </div>
        </div>
    </div>

    {{-- MODAL TOPPING VÀ ĐÁ ĐƯỜNG MỞ RỘNG CỦA POS --}}
    <div id="topping-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.2s;">
        <div class="bg-white rounded-[24px] shadow-2xl overflow-hidden flex flex-col scale-95 transition-transform duration-200 w-[550px] max-h-[85vh]" id="topping-modal-content">
            
            <div class="bg-espresso text-white px-6 py-4 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-lg leading-tight" id="modal-product-name">Tên món</h3>
                    <p class="text-coral font-medium text-sm" id="modal-product-price">0đ</p>
                </div>
                <button onclick="closeToppingModal()" class="text-white/50 hover:text-white bg-white/10 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
            </div>

            <div class="flex gap-4 border-b border-gray-200 px-6 pt-3 shrink-0">
                <button onclick="switchInnerTab('inner-topping')" id="btn-inner-topping" class="pb-3 text-sm font-bold border-b-2 border-coral text-coral transition-colors">Topping</button>
                <button onclick="switchInnerTab('inner-ice')" id="btn-inner-ice" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Đá</button>
                <button onclick="switchInnerTab('inner-sugar')" id="btn-inner-sugar" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Đường</button>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col bg-[#FAF7F2]">
                {{-- TAB TOPPING --}}
                <div id="inner-topping" class="inner-tab-content flex-1 flex flex-col overflow-hidden">
                    <div class="px-5 py-3 bg-[#FAF7F2] shrink-0 border-b border-gray-200">
                        <div class="relative">
                            <input type="text" id="search-topping" placeholder="Tìm topping nhanh..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-espresso bg-white focus:outline-none focus:border-coral" onkeyup="filterModalToppings()">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <div class="p-5 overflow-y-auto custom-scrollbar flex-1"><div id="modal-toppings-list" class="space-y-3"></div></div>
                </div>

                {{-- TAB ĐÁ --}}
                <div id="inner-ice" class="inner-tab-content hidden flex-1 overflow-y-auto custom-scrollbar p-6">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['100' => '100% Đá (Mặc định)', '70' => '70% Đá', '50' => '50% Đá', '20' => '20% Đá', '0' => '0% Đá (Không đá)'] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="modal_ice_level" value="{{ $val }}" class="peer sr-only" onchange="renderModalToppings()">
                            <div class="px-4 py-4 rounded-xl border border-gray-200 text-center text-sm peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-white">{{ $label }}</div>
                        </label>
                        @endforeach
                        <label class="cursor-pointer col-span-2">
                            <input type="radio" name="modal_ice_level" value="0_full" class="peer sr-only" onchange="renderModalToppings()">
                            <div class="px-4 py-4 rounded-xl border border-gray-200 text-sm peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-white flex justify-between items-center px-6">
                                <span>0% Đá (Nước đầy ly)</span><span class="text-coral bg-white px-3 py-1 rounded-lg border border-coral/20">+3.000đ</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- TAB ĐƯỜNG --}}
                <div id="inner-sugar" class="inner-tab-content hidden flex-1 overflow-y-auto custom-scrollbar p-6">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['100' => '100% Đường (Mặc định)', '70' => '70% Đường', '50' => '50% Đường', '20' => '20% Đường'] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="modal_sugar_level" value="{{ $val }}" class="peer sr-only">
                            <div class="px-4 py-4 rounded-xl border border-gray-200 text-center text-sm peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-white">{{ $label }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
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
        const iceTexts = {'100': '100% Đá', '70': '70% Đá', '50': '50% Đá', '20': '20% Đá', '0': 'Không đá', '0_full': 'Không đá (Đầy ly)'};
        const sugarTexts = {'100': '100% Đường', '70': '70% Đường', '50': '50% Đường', '20': '20% Đường'};
    </script>

    <script>
        function toggleSidebar() { const sidebar = document.getElementById('sidebar'); sidebar.classList.toggle('w-0'); sidebar.classList.toggle('w-64'); }
        function switchPosTab(tab) {
            document.getElementById('content-tab-products').classList.toggle('hidden', tab !== 'products');
            document.getElementById('content-tab-products').classList.toggle('block', tab === 'products');

            document.getElementById('content-tab-combos').classList.toggle('hidden', tab !== 'combos');
            document.getElementById('content-tab-combos').classList.toggle('block', tab === 'combos');

            document.getElementById('content-tab-toppings').classList.toggle('hidden', tab !== 'toppings');
            document.getElementById('content-tab-toppings').classList.toggle('block', tab === 'toppings');
            
            document.getElementById('btn-tab-products').className = tab === 'products' ? "flex-1 py-2 text-sm font-black uppercase tracking-wider text-coral border-b-2 border-coral transition-colors flex items-center justify-center gap-2" : "flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2";
            document.getElementById('btn-tab-combos').className = tab === 'combos' ? "flex-1 py-2 text-sm font-black uppercase tracking-wider text-amber-600 border-b-2 border-amber-600 transition-colors flex items-center justify-center gap-2" : "flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-amber-600 transition-colors flex items-center justify-center gap-2";
            document.getElementById('btn-tab-toppings').className = tab === 'toppings' ? "flex-1 py-2 text-sm font-black uppercase tracking-wider text-emerald-500 border-b-2 border-emerald-500 transition-colors flex items-center justify-center gap-2" : "flex-1 py-2 text-sm font-bold uppercase tracking-wider text-gray-400 border-b-2 border-transparent hover:text-coral transition-colors flex items-center justify-center gap-2";
        }
        function switchInnerTab(tabId) {
            document.querySelectorAll('.inner-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="btn-inner-"]').forEach(el => { el.classList.remove('text-coral', 'border-coral'); el.classList.add('text-gray-400', 'border-transparent'); });
            document.getElementById(tabId).classList.remove('hidden'); document.getElementById(tabId).classList.add('flex');
            document.getElementById('btn-' + tabId).classList.remove('text-gray-400', 'border-transparent'); document.getElementById('btn-' + tabId).classList.add('text-coral', 'border-coral');
        }

        let posCart = []; 
        let modalState = { cartItemId: null, productId: null, productName: '', productPrice: 0, toppings: {}, ice_level: '100', sugar_level: '100' };
        function formatVND(amount) { return new Intl.NumberFormat('vi-VN').format(amount) + 'đ'; }

        function addDirectItem(productId, name, price) {
            let existingItem = posCart.find(i => i.productId === productId && Object.keys(i.toppings).length === 0 && i.ice_level === '100' && i.sugar_level === '100');
            if (existingItem) { existingItem.quantity += 1; } 
            else { posCart.push({ cartItemId: Date.now(), productId: productId, name: name, price: price, quantity: 1, toppings: {}, ice_level: '100', sugar_level: '100' }); }
            renderBill();
        }

        function goToStep(step) {
            if(step === 3 && posCart.length === 0) { alert("Hóa đơn trống!"); return; }
            document.getElementById('step-track').style.transform = `translateX(-${(step - 1) * 33.3333}%)`;
            for (let i = 1; i <= 3; i++) {
                const circle = document.getElementById(`step-circle-${i}`); const text = document.getElementById(`step-text-${i}`);
                if (i === step) { circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-coral bg-coral text-white shadow-md shadow-coral/20"; text.className = "text-[11px] font-black text-coral transition-all duration-300"; } 
                else if (i < step) { circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-emerald-500 bg-emerald-500 text-white"; text.className = "text-[11px] font-bold text-emerald-500 transition-all duration-300"; } 
                else { circle.className = "w-8 h-8 rounded-full border-2 transition-all duration-300 flex items-center justify-center font-bold text-sm mb-1 z-10 border-espresso/20 text-espresso/40 bg-white"; text.className = "text-[11px] font-bold text-espresso/40 transition-all duration-300"; }
            }
            if(step === 2) renderReviewTable(); if(step === 3) renderStep3Summary();
        }

        let selectedCustomer = null;
        let customerSearchTimeout = null;

        function openCustomerModal() {
            const modal = document.getElementById('customer-search-modal');
            const content = document.getElementById('customer-search-modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
            document.getElementById('customer-search-input').value = '';
            fetchCustomersApi('');
        }

        function closeCustomerModal() {
            const modal = document.getElementById('customer-search-modal');
            const content = document.getElementById('customer-search-modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        function debounceCustomerSearch() {
            clearTimeout(customerSearchTimeout);
            customerSearchTimeout = setTimeout(() => {
                const q = document.getElementById('customer-search-input').value;
                fetchCustomersApi(q);
            }, 300);
        }

        function fetchCustomersApi(query) {
            const tbody = document.getElementById('customer-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-gray-400">Đang tải danh sách khách hàng...</td></tr>`;

            fetch(`/staff/api/customers/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.customers.length > 0) {
                        tbody.innerHTML = '';
                        data.customers.forEach((cust, idx) => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-amber-50/50 transition-colors';
                            tr.innerHTML = `
                                <td class="py-3 px-3 text-center font-bold text-gray-400 text-xs">${idx + 1}</td>
                                <td class="py-3 px-3 font-bold text-espresso">${cust.name} ${cust.role === 'admin' ? '<span class="text-[10px] bg-red-100 text-red-600 px-1 rounded font-normal">Admin</span>' : ''}</td>
                                <td class="py-3 px-3 text-espresso/80 font-mono text-xs">${cust.phone || '---'}</td>
                                <td class="py-3 px-3 text-espresso/70 text-xs">${cust.email || '---'}</td>
                                <td class="py-3 px-3 text-center"><span class="font-black text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full text-xs">${cust.point ?? 0} điểm</span></td>
                                <td class="py-3 px-3 text-center">
                                    <button type="button" class="btn-select-cust bg-coral hover:bg-[#d5523b] text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs">Chọn</button>
                                </td>
                            `;
                            tr.querySelector('.btn-select-cust').addEventListener('click', () => selectCustomer(cust));
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-gray-400 font-medium">Không tìm thấy khách hàng nào phù hợp!</td></tr>`;
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-red-500 font-medium">Lỗi kết nối khi tải danh sách khách hàng!</td></tr>`;
                });
        }

        function selectCustomer(cust) {
            selectedCustomer = cust;
            document.getElementById('selected_user_id').value = cust.user_id;
            document.getElementById('customer_phone').value = cust.phone || '';
            document.getElementById('review_customer_name').value = cust.name;
            document.getElementById('customer_name').value = cust.name;

            document.getElementById('card-customer-name').textContent = cust.name;
            document.getElementById('card-customer-phone').textContent = cust.phone ? '📞 ' + cust.phone : '📞 Chưa có SĐT';
            document.getElementById('card-customer-points').textContent = cust.point ?? 0;
            
            updateProjectedPoints();
            document.getElementById('selected-customer-card').classList.remove('hidden');
            closeCustomerModal();
        }

        function clearSelectedCustomer() {
            selectedCustomer = null;
            document.getElementById('selected_user_id').value = '';
            document.getElementById('customer_phone').value = '';
            document.getElementById('review_customer_name').value = '';
            document.getElementById('customer_name').value = '';
            document.getElementById('selected-customer-card').classList.add('hidden');
        }

        function updateProjectedPoints() {
            if (!selectedCustomer) return;
            let totalAmount = getCartTotalAmount();
            let projected = Math.floor(totalAmount / 10000);
            document.getElementById('card-projected-points').textContent = projected;
        }

        function getCartTotalAmount() {
            let totalAmount = 0;
            posCart.forEach(item => {
                let toppingTotal = 0;
                for (let tid in item.toppings) {
                    if (item.toppings[tid] > 0) {
                        let tData = toppingsData.find(t => t.product_id == tid);
                        if (tData) toppingTotal += tData.price * item.toppings[tid];
                    }
                }
                let icePrice = item.ice_level === '0_full' ? 3000 : 0;
                totalAmount += (item.price + toppingTotal + icePrice) * item.quantity;
            });
            return totalAmount;
        }

        function renderStep3Summary() {
            let name = document.getElementById('review_customer_name').value.trim() || 'Khách Vãng Lai';
            if (selectedCustomer) {
                name += ` (⭐ Tích điểm)`;
            }
            document.getElementById('summary-customer').textContent = name;
            document.getElementById('summary-note').textContent = document.getElementById('review_order_note').value.trim() || 'Không có ghi chú';
            document.getElementById('summary-count').textContent = posCart.reduce((sum, i) => sum + i.quantity, 0) + " ly";
            document.getElementById('summary-total').textContent = document.getElementById('review-total-display').textContent;
        }

        function submitFinalOrder() {
            let totalAmount = getCartTotalAmount();
            const payload = { 
                user_id: selectedCustomer ? selectedCustomer.user_id : null,
                customer_name: document.getElementById('review_customer_name').value.trim() || 'Khách Vãng Lai',
                customer_phone: selectedCustomer ? selectedCustomer.phone : (document.getElementById('customer_phone').value.trim() || null),
                order_note: document.getElementById('review_order_note').value.trim(), 
                total_amount: totalAmount, 
                items: posCart 
            };
            fetch('/staff/api/orders', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, 
                body: JSON.stringify(payload)
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const toast = document.getElementById('toast-notification'); 
                    toast.classList.remove('translate-x-[150%]', 'opacity-0');
                    setTimeout(() => { toast.classList.add('translate-x-[150%]', 'opacity-0'); }, 3000);
                    posCart = []; 
                    clearSelectedCustomer();
                    document.getElementById('review_order_note').value = '';
                    renderBill(); 
                    setTimeout(() => goToStep(1), 800);
                }
            }).catch(err => alert("Lỗi mạng khi tạo đơn!"));
        }

        function syncCustomerInfo(val, target) { document.getElementById(target).value = val; const reviewInput = document.getElementById('review_' + target); if(reviewInput && reviewInput !== document.activeElement) reviewInput.value = val; }

        function openToppingModal(productId, name, price, editCartItemId = null) {
            modalState.productId = productId; modalState.productName = name; modalState.productPrice = price; modalState.cartItemId = editCartItemId;
            if (editCartItemId) {
                const item = posCart.find(i => i.cartItemId === editCartItemId); 
                modalState.toppings = { ...item.toppings }; modalState.ice_level = item.ice_level; modalState.sugar_level = item.sugar_level;
            } else { modalState.toppings = {}; modalState.ice_level = '100'; modalState.sugar_level = '100'; }
            
            document.getElementById('modal-product-name').textContent = name;
            let iceRadios = document.getElementsByName('modal_ice_level'); for(let r of iceRadios) { r.checked = (r.value === modalState.ice_level); }
            let sugarRadios = document.getElementsByName('modal_sugar_level'); for(let r of sugarRadios) { r.checked = (r.value === modalState.sugar_level); }
            
            switchInnerTab('inner-topping');
            if(document.getElementById('search-topping')) document.getElementById('search-topping').value = '';
            renderModalToppings();
            
            const modal = document.getElementById('topping-modal'); const content = document.getElementById('topping-modal-content');
            modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }
        
        function closeToppingModal() {
            const modal = document.getElementById('topping-modal'); const content = document.getElementById('topping-modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }
        
        function filterModalToppings() {
            const searchTerm = document.getElementById('search-topping').value.toLowerCase().trim();
            document.querySelectorAll('.modal-topping-item').forEach(item => { item.style.display = item.getAttribute('data-name').toLowerCase().includes(searchTerm) ? 'flex' : 'none'; });
        }

        function updateModalTopping(toppingId, delta) {
            let currentQty = modalState.toppings[toppingId] || 0; let newQty = Math.max(0, currentQty + delta);
            modalState.toppings[toppingId] = newQty; renderModalToppings(); 
        }

        function renderModalToppings() {
            const list = document.getElementById('modal-toppings-list'); list.innerHTML = ''; let currentTotal = modalState.productPrice;
            let iceRadio = document.querySelector('input[name="modal_ice_level"]:checked');
            if(iceRadio && iceRadio.value === '0_full') currentTotal += 3000;

            toppingsData.forEach(t => {
                const qty = modalState.toppings[t.product_id] || 0; currentTotal += qty * t.price;
                let imgHtml = t.image_url ? `<img src="${baseUrl}/${t.image_url}" class="w-full h-full object-cover">` : `<span class="text-lg">✨</span>`;
                list.innerHTML += `
                    <div class="modal-topping-item flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl shadow-sm" data-name="${t.name}">
                        <div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100 shrink-0">${imgHtml}</div><div><p class="font-bold text-sm text-espresso leading-tight">${t.name}</p><p class="text-xs text-coral font-bold">+${formatVND(t.price)}</p></div></div>
                        <div class="flex items-center gap-3 shrink-0"><button onclick="updateModalTopping(${t.product_id}, -1)" class="w-7 h-7 rounded-md bg-gray-100 font-bold ${qty === 0 ? 'opacity-50 pointer-events-none' : ''}">-</button><span class="w-4 text-center text-sm font-black text-espresso">${qty}</span><button onclick="updateModalTopping(${t.product_id}, 1)" class="w-7 h-7 rounded-md bg-gray-100 font-bold">+</button></div>
                    </div>`;
            });
            document.getElementById('modal-total-price').textContent = formatVND(currentTotal);
            filterModalToppings(); 
        }

        function areItemsEqual(i1, newState) {
            if (i1.ice_level !== newState.ice_level || i1.sugar_level !== newState.sugar_level) return false;
            const keys1 = Object.keys(i1.toppings).filter(k => i1.toppings[k] > 0); const keys2 = Object.keys(newState.toppings).filter(k => newState.toppings[k] > 0);
            if (keys1.length !== keys2.length) return false; for (let k of keys1) { if (i1.toppings[k] !== newState.toppings[k]) return false; } return true;
        }

        function confirmToppingModal() {
            modalState.ice_level = document.querySelector('input[name="modal_ice_level"]:checked').value;
            modalState.sugar_level = document.querySelector('input[name="modal_sugar_level"]:checked').value;

            if (modalState.cartItemId) {
                let item = posCart.find(i => i.cartItemId === modalState.cartItemId); 
                if(item) { item.toppings = { ...modalState.toppings }; item.ice_level = modalState.ice_level; item.sugar_level = modalState.sugar_level; }
            } else {
                let existingItem = posCart.find(i => i.productId === modalState.productId && areItemsEqual(i, modalState));
                if (existingItem) { existingItem.quantity += 1; } 
                else { posCart.push({ cartItemId: Date.now(), productId: modalState.productId, name: modalState.productName, price: modalState.productPrice, quantity: 1, toppings: { ...modalState.toppings }, ice_level: modalState.ice_level, sugar_level: modalState.sugar_level }); }
            }
            renderBill(); closeToppingModal();
        }

        function changeQty(cartItemId, delta) { let item = posCart.find(i => i.cartItemId === cartItemId); if (!item) return; item.quantity += delta; if (item.quantity <= 0) posCart = posCart.filter(i => i.cartItemId !== cartItemId); renderBill(); }
        function removeCartItem(cartItemId) { posCart = posCart.filter(i => i.cartItemId !== cartItemId); renderBill(); }

        function renderBill() {
            const step1Container = document.getElementById('step1-bill-container'); const totalDisplays = document.querySelectorAll('.total-amount-text');
            if (posCart.length === 0) { step1Container.innerHTML = `<div class="h-full flex flex-col justify-center items-center text-gray-400 py-12"><p class="text-xs">Chưa có sản phẩm nào</p></div>`; totalDisplays.forEach(el => el.textContent = "0đ"); return; }
            step1Container.innerHTML = ""; let totalAmount = 0;
            posCart.forEach(item => {
                let toppingTotal = 0; let optionsHtml = [];
                for (let tid in item.toppings) { if (item.toppings[tid] > 0) { let tData = toppingsData.find(t => t.product_id == tid); if (tData) { toppingTotal += tData.price * item.toppings[tid]; optionsHtml.push(`${tData.name} (x${item.toppings[tid]})`); } } }
                
                let icePrice = item.ice_level === '0_full' ? 3000 : 0;
                let itemUnitPrice = item.price + toppingTotal + icePrice; let itemTotal = itemUnitPrice * item.quantity; totalAmount += itemTotal;
                
                if(item.ice_level !== '100') optionsHtml.push(`${iceTexts[item.ice_level]}`);
                if(item.sugar_level !== '100') optionsHtml.push(`${sugarTexts[item.sugar_level]}`);
                
                let detailHtml = optionsHtml.length > 0 ? `<p class="text-[10px] text-espresso/60 italic leading-tight mt-0.5">+ ${optionsHtml.join(', ')}</p>` : '';
                step1Container.innerHTML += `
                    <div class="bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center text-xs">
                        <div class="flex-1 pr-2"><h5 class="font-bold text-espresso line-clamp-1">${item.name}</h5>${detailHtml}<span class="text-[11px] text-coral font-bold mt-1 block">${formatVND(itemTotal)}</span></div>
                        <div class="flex items-center gap-1.5"><button onclick="changeQty(${item.cartItemId}, -1)" class="w-5 h-5 bg-gray-100 rounded font-black flex items-center justify-center hover:bg-coral hover:text-white">-</button><span class="font-bold text-espresso text-center min-w-[12px]">${item.quantity}</span><button onclick="changeQty(${item.cartItemId}, 1)" class="w-5 h-5 bg-gray-100 rounded font-black flex items-center justify-center hover:bg-coral hover:text-white">+</button></div>
                    </div>`;
            });
            totalDisplays.forEach(el => el.textContent = formatVND(totalAmount));
            updateProjectedPoints();
            if(document.getElementById('step-track').style.transform.includes('33.3333%')) renderReviewTable();
        }

        function renderReviewTable() {
            const tbody = document.getElementById('review-table-body'); const totalDisplay = document.getElementById('review-total-display'); tbody.innerHTML = "";
            if(posCart.length === 0) { tbody.innerHTML = `<tr><td colspan="5" class="py-12 text-center text-gray-400 font-medium">Không có sản phẩm nào để kiểm tra!</td></tr>`; totalDisplay.textContent = "0đ"; return; }
            let totalAmount = 0;
            posCart.forEach((item, index) => {
                let toppingTotal = 0; let optionsHtml = [];
                for (let tid in item.toppings) { if (item.toppings[tid] > 0) { let tData = toppingsData.find(t => t.product_id == tid); if (tData) { toppingTotal += tData.price * item.toppings[tid]; optionsHtml.push(`<span class="bg-coral/5 text-coral border border-coral/10 px-2 py-0.5 rounded-md text-[11px] font-medium">${tData.name} x${item.toppings[tid]}</span>`); } } }
                
                let icePrice = item.ice_level === '0_full' ? 3000 : 0;
                let itemUnitPrice = item.price + toppingTotal + icePrice; let itemTotal = itemUnitPrice * item.quantity; totalAmount += itemTotal;
                
                if(item.ice_level !== '100') optionsHtml.push(`<span class="bg-blue-50 text-blue-500 border border-blue-100 px-2 py-0.5 rounded-md text-[11px] font-medium">${iceTexts[item.ice_level]}</span>`);
                if(item.sugar_level !== '100') optionsHtml.push(`<span class="bg-amber-50 text-amber-500 border border-amber-100 px-2 py-0.5 rounded-md text-[11px] font-medium">${sugarTexts[item.sugar_level]}</span>`);

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-4 font-bold text-espresso/60 text-center">${index + 1}</td>
                        <td class="py-4 px-4"><span class="font-black text-espresso block text-sm">${item.name}</span><div class="flex flex-wrap gap-1 mt-1.5">${optionsHtml.length > 0 ? optionsHtml.join('') : '<span class="text-xs text-gray-400 italic">Mặc định</span>'}</div></td>
                        <td class="py-4 px-4"><div class="flex items-center justify-center gap-2.5"><button onclick="changeQty(${item.cartItemId}, -1)" class="w-7 h-7 bg-gray-100 rounded-lg font-bold flex items-center justify-center hover:bg-coral hover:text-white transition-colors">-</button><span class="font-black text-espresso text-sm w-4 text-center">${item.quantity}</span><button onclick="changeQty(${item.cartItemId}, 1)" class="w-7 h-7 bg-gray-100 rounded-lg font-bold flex items-center justify-center hover:bg-coral hover:text-white transition-colors">+</button></div></td>
                        <td class="py-4 px-4 text-right font-black text-espresso text-sm">${formatVND(itemTotal)}</td>
                        <td class="py-4 px-4"><div class="flex items-center justify-center gap-2"><button onclick="openToppingModal(${item.productId}, '${item.name}', ${item.price}, ${item.cartItemId})" class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white transition-all">Sửa</button><button onclick="removeCartItem(${item.cartItemId})" class="text-xs font-bold text-red-500 hover:text-white bg-red-50 hover:bg-red-500 px-2.5 py-1.5 rounded-lg transition-colors">Xóa</button></div></td>
                    </tr>`;
            });
            totalDisplay.textContent = formatVND(totalAmount);
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderBill();
            document.getElementById('search-product').addEventListener('input', filterProducts); 
            document.getElementById('category-filter').addEventListener('change', filterProducts);
            function filterProducts() {
                const s = document.getElementById('search-product').value.toLowerCase().trim(); const c = document.getElementById('category-filter').value;
                document.querySelectorAll('.product-item').forEach(i => { i.style.display = (i.getAttribute('data-name').toLowerCase().includes(s) && (c === "" || i.getAttribute('data-category-id') === c)) ? 'flex' : 'none'; });
            }
        });
    </script>
</body>
</html>