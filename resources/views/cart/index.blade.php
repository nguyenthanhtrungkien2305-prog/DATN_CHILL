@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        @php
            $totalItems = 0;
            $subTotal = 0;
            if(session()->has('cart')) {
                foreach(session('cart') as $item) {
                    $totalItems += $item['quantity'];
                    $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
                }
            }
        @endphp

        <div class="flex items-center gap-3 mb-8">
            <h1 class="font-serif font-bold text-3xl md:text-4xl text-espresso">Giỏ hàng của bạn</h1>
            <span class="bg-coral text-white text-sm font-bold px-3 py-1 rounded-full">{{ $totalItems }} món</span>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
        
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3 flex flex-col space-y-4">
                
                {{-- Thanh Tìm Kiếm Món Trong Giỏ Hàng --}}
                @if(count(session('cart')) > 1)
                    <div class="relative w-full bg-white rounded-2xl p-2.5 border border-espresso/10 shadow-sm flex items-center gap-3">
                        <div class="pl-2 text-espresso/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="cart-search-input" onkeyup="filterCartItems()" placeholder="Tìm món trong giỏ hàng..." class="w-full bg-transparent border-none outline-none text-sm text-espresso font-medium focus:ring-0 placeholder:text-espresso/40">
                        <button type="button" onclick="clearCartSearch()" id="btn-clear-cart-search" class="hidden pr-2 text-espresso/40 hover:text-coral font-bold text-xs">✕ Xóa</button>
                    </div>
                @endif

                {{-- Container Giới Hạn Chiều Cao Có Thanh Cuộn (Scrollbar) --}}
                <div id="cart-items-container" class="space-y-4 max-h-[620px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach(session('cart') as $cartKey => $item)
                    @php
                        $categoryName = \DB::table('products')
                            ->join('categories', 'products.category_id', '=', 'categories.category_id')
                            ->where('products.product_id', $item['product_id'])
                            ->value('categories.name');

                        $isStandaloneTopping = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');
                    @endphp

                    <div class="cart-item-card {{ $isStandaloneTopping ? 'bg-coral/5 border-coral/20 p-4 rounded-[20px]' : 'bg-white border-espresso/5 p-4 md:p-6 rounded-[24px]' }} shadow-sm border flex flex-col sm:flex-row gap-6 relative group transition-all" data-name="{{ mb_strtolower($item['name']) }}" data-size="{{ mb_strtolower($item['size_name']) }}">
                        
                        <button type="button" onclick="removeCartItem('{{ $cartKey }}')" class="absolute top-4 right-4 text-espresso/40 hover:text-red-500 transition-colors p-2" title="Xóa món này">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>

                        <div class="{{ $isStandaloneTopping ? 'w-20 h-20 sm:w-24 sm:h-24' : 'w-24 h-24 sm:w-32 sm:h-32' }} rounded-2xl overflow-hidden bg-cream shrink-0">
                            <img src="{{ $item['image'] ?? 'https://via.placeholder.com/200' }}" class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 flex flex-col justify-center">
                            @if($isStandaloneTopping)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-coral mb-1 w-max">Topping Mua Rời</span>
                            @endif

                            <h3 class="font-serif font-bold {{ $isStandaloneTopping ? 'text-lg' : 'text-xl' }} text-espresso mb-1 pr-8">{{ $item['name'] }}</h3>
                            
                            <p class="text-sm text-espresso/60 mb-1">Kích cỡ: <span class="font-medium text-espresso">{{ $item['size_name'] }}</span></p>
                            
                            @php
                                $iceTexts = ['70' => '70% Đá', '50' => '50% Đá', '20' => '20% Đá', '0' => 'Không đá', '0_full' => 'Không đá (Nước đầy ly)'];
                                $sugarTexts = ['70' => '70% Đường', '50' => '50% Đường', '20' => '20% Đường', '0' => 'Không đường'];
                            @endphp

                            @if((isset($item['ice_level']) && $item['ice_level'] !== '100') || (isset($item['sugar_level']) && $item['sugar_level'] !== '100'))
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    @if(isset($item['ice_level']) && $item['ice_level'] !== '100')
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100 font-medium">
                                            {{ $iceTexts[$item['ice_level']] ?? $item['ice_level'] }}
                                        </span>
                                    @endif
                                    @if(isset($item['sugar_level']) && $item['sugar_level'] !== '100')
                                        <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 font-medium">
                                            {{ $sugarTexts[$item['sugar_level']] ?? $item['sugar_level'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($item['toppings']))
                                <div class="bg-[#FAF7F2] p-3 rounded-xl mb-3 border border-espresso/5 w-full md:w-max">
                                    <p class="text-xs font-bold text-espresso/50 uppercase tracking-wider mb-2 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                        Topping đi kèm
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($item['toppings'] as $top)
                                            <div class="px-3 py-1 bg-white text-coral border border-coral/20 rounded-lg text-xs font-medium flex items-center gap-1 shadow-sm">
                                                {{ $top['name'] }} 
                                                <span class="bg-coral text-white text-[10px] px-1.5 py-0.5 rounded-md ml-1">x{{ $top['qty'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!$isStandaloneTopping)
                            <button onclick="openEditToppingModal('{{ $cartKey }}')" class="text-xs font-medium text-coral hover:text-[#d5523b] hover:underline text-left w-max mb-4 flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Tùy chỉnh Đồ uống & Topping
                            </button>
                            @endif

                            <div class="flex items-end justify-between mt-auto pt-2">
                                <div class="font-black {{ $isStandaloneTopping ? 'text-base' : 'text-lg' }} text-espresso">
                                    {{ number_format(($item['price'] + $item['topping_total']) * $item['quantity'], 0, ',', '.') }} đ
                                </div>

                                <div class="flex items-center justify-between border border-espresso/20 rounded-full h-9 w-24 bg-white overflow-hidden">
                                    <button onclick="updateCartItemQty('{{ $cartKey }}', -1)" class="w-7 h-full flex items-center justify-center text-espresso hover:text-coral font-bold text-base transition-colors">-</button>
                                    <input type="text" value="{{ $item['quantity'] }}" readonly class="w-8 h-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 p-0 m-0 leading-none">
                                    <button onclick="updateCartItemQty('{{ $cartKey }}', 1)" class="w-7 h-full flex items-center justify-center text-espresso hover:text-coral font-bold text-base transition-colors">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Khung hiển thị khi tìm không thấy sản phẩm --}}
                    <div id="no-search-results" class="hidden bg-white p-8 text-center rounded-3xl border border-dashed border-gray-200">
                        <p class="text-sm text-espresso/60 font-medium">Không tìm thấy sản phẩm nào khớp với từ khóa tìm kiếm.</p>
                    </div>
                </div>

            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 md:p-8 rounded-[32px] shadow-lg border border-espresso/5 sticky top-24">
                    <h2 class="font-serif font-bold text-2xl text-espresso mb-6">Tổng đơn hàng</h2>
                    
                    <div class="mb-6 pb-6 border-b border-espresso/10">
                        <label class="block text-sm font-bold text-espresso mb-2">Mã ưu đãi (Voucher)</label>
                        
                        <div class="relative">
                            {{-- Trạng thái 1: Đã áp dụng voucher --}}
                            @if(session()->has('voucher'))
                                <div class="w-full flex items-center justify-between bg-emerald-50 border border-emerald-300 rounded-xl px-3.5 py-2.5 text-xs cursor-pointer select-none hover:bg-emerald-100/60 transition-colors" onclick="toggleVoucherDropdown('cart')">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <span class="font-mono font-black text-espresso uppercase">{{ session('voucher')['code'] }}</span>
                                        <span class="text-emerald-700 font-extrabold truncate">(-{{ number_format(session('voucher')['discount_amount'], 0, ',', '.') }}đ)</span>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-[10px] bg-white text-emerald-800 font-bold px-2 py-0.5 rounded border border-emerald-200 shadow-2xs">✓ Đang áp dụng</span>
                                        <svg class="w-4 h-4 text-espresso/60 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            @else
                                {{-- Trạng thái 2: Chưa áp dụng voucher --}}
                                <div class="flex gap-2">
                                    <div class="relative flex-1 flex items-center">
                                        <input type="text" id="voucher-input" placeholder="Nhập mã giảm giá..." class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-coral text-xs uppercase font-medium">
                                        @if(isset($availableVouchers) && $availableVouchers->isNotEmpty())
                                            <button type="button" onclick="toggleVoucherDropdown('cart')" title="Xem danh sách mã phù hợp" class="absolute right-2 text-espresso/50 hover:text-coral p-1 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                    <button type="button" onclick="applyVoucher()" class="bg-espresso text-white px-4 py-2.5 rounded-xl font-bold hover:bg-coral transition-colors text-xs whitespace-nowrap">Áp dụng</button>
                                </div>
                            @endif

                            {{-- Menu Popover Thả Nổi (Absolute) --}}
                            @if(isset($availableVouchers) && $availableVouchers->isNotEmpty())
                                <div id="voucher-dropdown-menu-cart" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-espresso/15 rounded-2xl shadow-2xl z-50 p-2.5 max-h-60 overflow-y-auto custom-scrollbar">
                                    <div class="text-[11px] font-extrabold text-espresso/60 px-2 py-1 uppercase tracking-wider border-b border-gray-100 mb-1 flex justify-between items-center">
                                        <span>Danh sách Mã Giảm Giá</span>
                                        <button type="button" onclick="toggleVoucherDropdown('cart')" class="text-espresso/40 hover:text-coral font-bold text-sm">✕</button>
                                    </div>
                                    <div class="space-y-1.5">
                                        {{-- Tùy chọn 1: Không dùng voucher --}}
                                        <button type="button" onclick="removeVoucher()" class="w-full text-left p-2.5 rounded-xl flex items-center justify-between text-xs transition-colors hover:bg-red-50 border border-gray-100 {{ !session()->has('voucher') ? 'bg-gray-100 font-bold' : '' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-espresso">Không sử dụng mã giảm giá</span>
                                            </div>
                                            @if(!session()->has('voucher'))
                                                <span class="text-[10px] text-gray-500 font-bold">✓ Đang chọn</span>
                                            @endif
                                        </button>

                                        {{-- Các mã giảm giá khả dụng --}}
                                        @foreach($availableVouchers as $v)
                                            @php
                                                $isCurrent = session()->has('voucher') && session('voucher')['code'] === $v->code;
                                                $isEligible = $v->is_eligible ?? true;
                                            @endphp
                                            @if($isEligible)
                                                <button type="button" onclick="applyVoucherCode('{{ $v->code }}')" class="w-full text-left p-2.5 rounded-xl flex items-center justify-between text-xs transition-colors {{ $isCurrent ? 'bg-coral/10 border border-coral/30 font-bold' : 'hover:bg-gray-50 border border-gray-100' }}">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-mono font-black text-espresso uppercase">{{ $v->code }}</span>
                                                            <span class="text-coral font-extrabold">-{{ number_format($v->discount_amount, 0, ',', '.') }}đ</span>
                                                        </div>
                                                        <span class="text-[10px] text-espresso/50 block">Đơn tối thiểu: {{ number_format($v->min_order, 0, ',', '.') }}đ</span>
                                                    </div>
                                                    @if($isCurrent)
                                                        <span class="text-[10px] text-coral font-black bg-white px-2 py-0.5 rounded border border-coral/20">✓ Đang dùng</span>
                                                    @else
                                                        <span class="text-[10px] text-white bg-coral px-2.5 py-1 rounded-lg font-bold">Chọn mã</span>
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" onclick="alertIneligibleVoucher('{{ number_format($v->missing_amount, 0, ',', '.') }}')" class="w-full text-left p-2.5 rounded-xl flex items-center justify-between text-xs transition-colors bg-gray-50/70 border border-dashed border-gray-200 hover:bg-amber-50/60 opacity-75">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-mono font-black text-gray-500 uppercase">{{ $v->code }}</span>
                                                            <span class="text-xs text-amber-700 font-bold">
                                                                ({{ $v->discount_type === 'percent' ? 'Giảm '.$v->discount_value.'%' : 'Giảm '.number_format($v->discount_value, 0, ',', '.').'đ' }})
                                                            </span>
                                                        </div>
                                                        <span class="text-[10px] text-amber-700 font-bold block mt-0.5">Cần mua thêm {{ number_format($v->missing_amount, 0, ',', '.') }}đ</span>
                                                    </div>
                                                    <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-1 rounded-lg border border-amber-200">Chưa đủ điều kiện</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-espresso/80">
                            <span>Tạm tính ({{ $totalItems }} món)</span>
                            <span class="font-medium">{{ number_format($subTotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between text-espresso/80">
                            <span>Giảm giá (Voucher)</span>
                            <span class="font-medium text-coral">- {{ number_format(session()->has('voucher') ? session('voucher')['discount_amount'] : 0, 0, ',', '.') }} đ</span>
                        </div>
                    </div>

                    @php
                        $discount = session()->has('voucher') ? session('voucher')['discount_amount'] : 0;
                        $finalTotal = max(0, $subTotal - $discount);
                    @endphp
                    <div class="flex justify-between items-end mb-8 pt-6 border-t border-espresso/10">
                        <span class="font-bold text-espresso">Tổng cộng</span>
                        <span class="font-black text-3xl text-coral">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block text-center w-full py-4 bg-coral text-white rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all">
                        Tiến hành Thanh toán
                    </a>
                    
                    <a href="{{ route('product.index') }}" class="block text-center mt-4 text-sm text-espresso/60 hover:text-coral hover:underline">
                        ← Tiếp tục chọn món
                    </a>
                </div>
            </div>
        </div>

        @else
        <div class="bg-white rounded-[32px] p-12 text-center shadow-sm border border-espresso/5 max-w-2xl mx-auto mt-8">
            <svg class="w-32 h-32 mx-auto mb-6 text-espresso/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <h2 class="text-2xl font-serif font-bold text-espresso mb-3">Giỏ hàng đang trống!</h2>
            <p class="text-espresso/60 mb-8">Bạn chưa chọn món nước nào. Hãy quay lại thực đơn để chọn cho mình những ly nước thật ngon nhé!</p>
            <a href="{{ route('product.index') }}" class="inline-block bg-coral text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all">
                Xem thực đơn ngay
            </a>
        </div>
        @endif

        {{-- ========================================= --}}
        {{-- MODAL CHỈNH SỬA TOPPING TRONG GIỎ HÀNG --}}
        {{-- ========================================= --}}
        <div id="edit-topping-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
            <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeEditToppingModal()"></div>
            
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-2xl flex flex-col scale-95 opacity-0 transition-all max-h-[90vh]" id="edit-topping-modal-content">
                
                {{-- Nút X tắt --}}
                <button onclick="closeEditToppingModal()" class="absolute top-6 right-6 text-espresso/40 hover:text-coral z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                {{-- Header Modal --}}
                <div class="p-8 pb-0 shrink-0">
                    <h2 class="font-serif font-black text-2xl text-espresso mb-1">Tùy chỉnh Đồ uống</h2>
                    <p id="modal-product-name" class="text-espresso/60 text-sm mb-6 font-medium">Đang tải...</p>
                    
                    <div class="flex gap-6 border-b border-gray-200 shrink-0">
                        <button onclick="switchCartModalTab('modal-topping')" id="btn-modal-topping" class="pb-3 text-sm font-bold border-b-2 border-coral text-coral transition-colors">Topping ăn kèm</button>
                        <button onclick="switchCartModalTab('modal-ice')" id="btn-modal-ice" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Lượng Đá</button>
                        <button onclick="switchCartModalTab('modal-sugar')" id="btn-modal-sugar" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Lượng Đường</button>
                    </div>
                </div>
                
                {{-- Khu vực nội dung Tab có thể Scroll --}}
                <div class="flex-1 overflow-y-auto p-8 pt-6 custom-scrollbar">
                    
                    {{-- Tab 1: Topping --}}
                    <div id="modal-topping" class="cart-modal-tab-content space-y-3">
                        <div id="modal-topping-list"></div>
                    </div>

                    {{-- Tab 2: Lượng Đá --}}
                    <div id="modal-ice" class="cart-modal-tab-content hidden">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(['100' => '100% Đá (Mặc định)', '70' => '70% Đá', '50' => '50% Đá', '20' => '20% Đá', '0' => '0% Đá (Không đá)'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="modal_ice_level" value="{{ $val }}" class="peer sr-only" onchange="calculateModalPrice()">
                                <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">{{ $label }}</div>
                            </label>
                            @endforeach
                            <label class="cursor-pointer col-span-2">
                                <input type="radio" name="modal_ice_level" value="0_full" class="peer sr-only" onchange="calculateModalPrice()">
                                <div class="px-4 py-4 rounded-xl border border-gray-200 peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50 flex justify-between items-center px-6">
                                    <span>0% Đá (Nước đầy ly)</span>
                                    <span class="text-coral bg-white px-3 py-1 rounded-lg border border-coral/20">+3.000đ</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Tab 3: Lượng Đường --}}
                    <div id="modal-sugar" class="cart-modal-tab-content hidden">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(['100' => '100% Đường (Mặc định)', '70' => '70% Đường', '50' => '50% Đường', '20' => '20% Đường', '0' => '0% Đường'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="modal_sugar_level" value="{{ $val }}" class="peer sr-only">
                                <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">{{ $label }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Nút Xác Nhận Cố định Dưới Đáy --}}
                <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-between items-center shrink-0 rounded-b-[32px]">
                    <input type="hidden" id="current-cart-key">
                    <div>
                        <span class="text-xs text-espresso/60 font-medium">Tạm tính (1 ly):</span>
                        <div id="modal-total-price" class="text-coral font-black text-2xl">0đ</div>
                    </div>
                    <button onclick="submitToppingChanges()" class="bg-coral text-white px-8 py-3.5 rounded-xl font-bold hover:bg-[#d5523b] shadow-md transition-all">
                        Lưu thay đổi
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e8634a; border-radius: 10px; }
</style>

<script>
    const csrfToken = '{{ csrf_token() }}';

    function removeCartItem(cartKey) {
        if(confirm('Bạn có chắc chắn muốn xóa món này khỏi giỏ hàng?')) {
            fetch('{{ route('cart.remove') }}', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ _token: csrfToken, cart_key: cartKey })
            }).then(res => res.json()).then(data => {
                if(data.success) { window.location.reload(); } else { alert(data.message); }
            });
        }
    }

    function updateCartItemQty(cartKey, change) {
        fetch('{{ route('cart.update') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ _token: csrfToken, cart_key: cartKey, change: change })
        }).then(res => res.json()).then(data => {
            if(data.success) { window.location.reload(); } else { alert(data.message); }
        });
    }

    function switchCartModalTab(tabId) {
        document.querySelectorAll('.cart-modal-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="btn-modal-"]').forEach(el => {
            el.classList.remove('text-coral', 'border-coral');
            el.classList.add('text-gray-400', 'border-transparent');
        });

        document.getElementById(tabId).classList.remove('hidden');
        let activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.classList.remove('text-gray-400', 'border-transparent');
        activeBtn.classList.add('text-coral', 'border-coral');
    }

    // BIẾN LƯU GIÁ GỐC ĐỂ TÍNH TIỀN REAL-TIME
    let currentModalItemPrice = 0;

    function openEditToppingModal(cartKey) {
        fetch('{{ route('cart.getItem') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ _token: csrfToken, cart_key: cartKey })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('current-cart-key').value = cartKey;
                document.getElementById('modal-product-name').innerText = data.item_name + ' (' + data.size_name + ')';
                currentModalItemPrice = parseFloat(data.item_price) || 0;
                
                document.querySelectorAll('input[name="modal_ice_level"]').forEach(radio => {
                    radio.checked = (radio.value === (data.ice_level || '100'));
                });
                document.querySelectorAll('input[name="modal_sugar_level"]').forEach(radio => {
                    radio.checked = (radio.value === (data.sugar_level || '100'));
                });

                switchCartModalTab('modal-topping');
                
                let html = '';
                if(data.toppings.length === 0) {
                    html = '<p class="text-center text-gray-500 py-8 italic border border-dashed border-gray-200 rounded-xl">Sản phẩm này không có topping nào để thêm.</p>';
                } else {
                    data.toppings.forEach(top => {
                        let formattedPrice = new Intl.NumberFormat('vi-VN').format(top.price);
                        let imgUrl = top.image ? top.image : 'https://via.placeholder.com/200';
                        html += `
                        <div class="flex items-center justify-between p-3 border border-espresso/10 rounded-xl hover:bg-[#FAF7F2] transition-colors group">
                            <div class="flex items-center gap-3"><img src="${imgUrl}" class="w-12 h-12 rounded-full object-cover">
                                <div><span class="block font-medium text-espresso">${top.name}</span><span class="font-bold text-coral">+${formattedPrice}đ</span></div>
                            </div>
                            <div class="flex items-center justify-between border border-espresso/20 rounded-full h-10 w-28 bg-white shrink-0 overflow-hidden">
                                <button type="button" onclick="updateModalToppingQty(${top.topping_id}, -1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">-</button>
                                <input type="text" id="modal-topping-qty-${top.topping_id}" data-price="${top.price}" value="${top.qty}" readonly class="modal-topping-input w-12 h-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 p-0 m-0 leading-none">
                                <button type="button" onclick="updateModalToppingQty(${top.topping_id}, 1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">+</button>
                            </div>
                        </div>`;
                    });
                }
                document.getElementById('modal-topping-list').innerHTML = html;
                
                // Gọi hàm tính tiền ngay khi bật bảng
                calculateModalPrice();

                const modal = document.getElementById('edit-topping-modal');
                const modalContent = document.getElementById('edit-topping-modal-content');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else { alert(data.message); }
        });
    }

    // HÀM TÍNH TOÁN REAL-TIME CHO BẢNG MODAL TRONG GIỎ HÀNG
    function calculateModalPrice() {
        let total = currentModalItemPrice;
        
        // Cộng tiền Topping
        document.querySelectorAll('.modal-topping-input').forEach(input => {
            let qty = parseInt(input.value) || 0;
            if (qty > 0) {
                let price = parseFloat(input.getAttribute('data-price')) || 0;
                total += (price * qty);
            }
        });

        // Cộng tiền Đá
        let iceLevel = document.querySelector('input[name="modal_ice_level"]:checked')?.value;
        if (iceLevel === '0_full') {
            total += 3000;
        }

        document.getElementById('modal-total-price').innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    }

    function updateModalToppingQty(id, change) {
        let input = document.getElementById('modal-topping-qty-' + id);
        let val = parseInt(input.value) + change;
        if(val >= 0) {
            input.value = val;
            calculateModalPrice(); // Gọi tính tiền mỗi khi bấm nút + / -
        }
    }

    function closeEditToppingModal() {
        const modal = document.getElementById('edit-topping-modal');
        const modalContent = document.getElementById('edit-topping-modal-content');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function submitToppingChanges() {
        let cartKey = document.getElementById('current-cart-key').value;
        let toppingsData = {};
        
        document.querySelectorAll('.modal-topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if(qty > 0) {
                let topId = input.id.replace('modal-topping-qty-', '');
                toppingsData[topId] = qty;
            }
        });

        let iceLevel = document.querySelector('input[name="modal_ice_level"]:checked')?.value || '100';
        let sugarLevel = document.querySelector('input[name="modal_sugar_level"]:checked')?.value || '100';

        fetch('{{ route('cart.updateToppings') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ 
                _token: csrfToken, 
                cart_key: cartKey, 
                toppings: toppingsData,
                ice_level: iceLevel,
                sugar_level: sugarLevel
            })
        }).then(res => res.json()).then(data => { if(data.success) { window.location.reload(); } else { alert(data.message); } });
    }

    function alertIneligibleVoucher(missingText) {
        alert('Bạn cần mua thêm ' + missingText + 'đ để sử dụng voucher này nhé!');
    }

    function toggleVoucherDropdown(page) {
        const menu = document.getElementById('voucher-dropdown-menu-' + page);
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function applyVoucherCode(code) {
        const codeInput = document.getElementById('voucher-input');
        if (codeInput) codeInput.value = code;
        fetch('{{ route('cart.applyVoucher') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ voucher_code: code })
        }).then(res => res.json()).then(data => { if (data.success) { window.location.reload(); } else { alert(data.message); } });
    }

    function applyVoucher() {
        const codeInput = document.getElementById('voucher-input');
        const code = codeInput ? codeInput.value.trim() : '';
        if (!code) { alert('Vui lòng nhập mã giảm giá!'); return; }

        fetch('{{ route('cart.applyVoucher') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ voucher_code: code })
        }).then(res => res.json()).then(data => { if (data.success) { window.location.reload(); } else { alert(data.message); } });
    }

    function removeVoucher() {
        fetch('{{ route('cart.removeVoucher') }}', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({})
        }).then(res => res.json()).then(data => { if (data.success) { window.location.reload(); } else { alert(data.message); } });
    }

    function filterCartItems() {
        const query = (document.getElementById('cart-search-input')?.value || '').toLowerCase().trim();
        const clearBtn = document.getElementById('btn-clear-cart-search');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', query === '');
        }

        let visibleCount = 0;
        document.querySelectorAll('.cart-item-card').forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const size = card.getAttribute('data-size') || '';
            if (query === '' || name.includes(query) || size.includes(query)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('no-search-results');
        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function clearCartSearch() {
        const input = document.getElementById('cart-search-input');
        if (input) {
            input.value = '';
            filterCartItems();
        }
    }
</script>
@endsection