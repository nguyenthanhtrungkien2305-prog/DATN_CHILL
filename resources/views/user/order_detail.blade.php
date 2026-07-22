@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_id . ' - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #facc15 !important; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-10 px-4">
    <div class="w-full max-w-5xl bg-white rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-[80vh] min-h-[550px] max-h-[800px]">
        
        {{-- CỘT TRÁI: Menu --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-8 md:p-10 flex flex-col h-full shrink-0 hidden md:flex">
            <h2 class="font-serif font-bold text-2xl text-white mb-8">Tài khoản</h2>
            <nav class="space-y-2 flex-1">
                <a href="{{ route('user.profile') }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="block px-4 py-3 rounded-xl bg-white/10 text-white font-medium transition-colors">Đơn hàng của tôi</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="mt-auto px-4 py-3 text-coral hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- CỘT PHẢI: Chi tiết đơn hàng --}}
        <div class="w-full md:w-2/3 p-6 md:p-10 bg-gray-50/50 h-full overflow-y-auto custom-scrollbar flex flex-col">
            
            {{-- Header & Nút quay lại --}}
            <div class="flex items-center gap-4 mb-8 shrink-0">
                <a href="{{ route('user.orders') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-espresso hover:bg-coral hover:text-white hover:border-coral transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h3 class="font-serif font-bold text-2xl text-espresso leading-none mb-1">Chi tiết đơn hàng</h3>
                    <p class="text-sm text-espresso/60 font-medium">Mã đơn: <span class="text-coral font-bold">#{{ $order->order_id }}</span></p>
                </div>
            </div>

            {{-- THANH TIẾN ĐỘ TRẠNG THÁI --}}
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm mb-6 shrink-0">
                @if($order->status == 'canceled')
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <h4 class="font-black text-xl text-red-600 mb-1">Đơn hàng đã bị hủy</h4>
                        <p class="text-sm text-gray-500 font-medium">Rất tiếc vì sự bất tiện này. Bạn có thể đặt lại món khác nhé!</p>
                    </div>
                @else
                    @php
                        $step = 1; $progress = 0;
                        if($order->status == 'processing') { $step = 2; $progress = 50; }
                        if($order->status == 'completed') { $step = 3; $progress = 100; }
                    @endphp
                    <div class="relative max-w-md mx-auto">
                        <div class="absolute left-0 top-1/2 w-full h-1.5 bg-gray-100 -translate-y-1/2 rounded-full -z-10"></div>
                        <div class="absolute left-0 top-1/2 h-1.5 bg-coral -translate-y-1/2 rounded-full -z-10 transition-all duration-1000" style="width: {{ $progress }}%;"></div>
                        
                        <div class="flex justify-between relative z-10">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg transition-colors border-4 border-white shadow-sm {{ $step >= 1 ? 'bg-coral text-white' : 'bg-gray-200 text-gray-400' }}">1</div>
                                <span class="text-xs font-bold {{ $step >= 1 ? 'text-coral' : 'text-gray-400' }}">Chờ xác nhận</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg transition-colors border-4 border-white shadow-sm {{ $step >= 2 ? 'bg-coral text-white' : 'bg-gray-200 text-gray-400' }}">2</div>
                                <span class="text-xs font-bold {{ $step >= 2 ? 'text-coral' : 'text-gray-400' }}">Đang chuẩn bị</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg transition-colors border-4 border-white shadow-sm {{ $step >= 3 ? 'bg-coral text-white' : 'bg-gray-200 text-gray-400' }}">3</div>
                                <span class="text-xs font-bold {{ $step >= 3 ? 'text-coral' : 'text-gray-400' }}">Hoàn thành</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Thông tin giao hàng --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6 flex items-start gap-4 shrink-0">
                <div class="w-12 h-12 bg-coral/10 text-coral rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-espresso mb-1 uppercase tracking-wider text-xs">Phương thức nhận hàng</h4>
                    @if($order->order_type == 'delivery')
                        <p class="text-sm font-medium text-espresso/80"><span class="font-bold text-espresso">Giao đến:</span> {{ $order->shipping_address ?? 'Không có địa chỉ' }}</p>
                    @else
                        <p class="text-sm font-medium text-espresso/80"><span class="font-bold text-espresso">Tại quán:</span> Dùng tại Bàn số {{ $order->table_number ?? 'Trống' }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">Thời gian đặt: {{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}</p>
                </div>
            </div>

            {{-- DANH SÁCH SẢN PHẨM & ĐÁNH GIÁ (ĐÃ KHẮC PHỤC LỖI FLEXBOX) --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mt-auto">
                <h4 class="font-bold text-espresso uppercase tracking-wider text-sm mb-4">Sản phẩm đã chọn</h4>
                
                <div class="space-y-4">
                    @php 
                        $items = [];
                        if (!empty($order->items)) {
                            $decoded = json_decode($order->items, true);
                            if (is_string($decoded)) {
                                $decoded = json_decode($decoded, true);
                            }
                            if (is_array($decoded)) {
                                $items = $decoded;
                            }
                        }

                        $iceTexts = ['100' => '100% Đá', '70' => '70% Đá', '50' => '50% Đá', '20' => '20% Đá', '0' => 'Không đá', '0_full' => 'Không đá (Đầy ly)'];
                        $sugarTexts = ['100' => '100% Đường', '70' => '70% Đường', '50' => '50% Đường', '20' => '20% Đường', '0' => 'Không đường'];
                    @endphp

                    @forelse($items as $item)
                        @php
                            $realProductId = $item['id'] ?? $item['product_id'] ?? $item['productId'] ?? 0;
                            
                            $isReviewed = false;
                            if (\Schema::hasTable('reviews')) {
                                $isReviewed = \DB::table('reviews')->where('order_id', $order->order_id)->where('product_id', $realProductId)->exists();
                            }
                            
                            $itemName = $item['name'] ?? 'Sản phẩm';
                            $itemQty = $item['quantity'] ?? 1;
                            $itemSize = $item['size_name'] ?? 'Mặc định';
                            $itemImage = $item['image'] ?? 'https://via.placeholder.com/100';
                            
                            $itemPriceRaw = ($item['price'] ?? 0) + ($item['topping_total'] ?? 0);
                            if(isset($item['ice_level']) && $item['ice_level'] === '0_full' && !isset($item['topping_total'])) {
                                $itemPriceRaw += 3000;
                            }
                            $itemPrice = $itemPriceRaw * $itemQty;
                        @endphp
                        
                        <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 rounded-xl border border-gray-50 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start gap-4 w-full sm:w-2/3">
                                <img src="{{ $itemImage }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200 bg-white shrink-0">
                                
                                <div class="flex-1">
                                    <h4 class="font-bold text-espresso">{{ $itemName }}</h4>
                                    <p class="text-xs text-espresso/60 mb-1">Size {{ $itemSize }} | x{{ $itemQty }}</p>
                                    
                                    {{-- VÙNG HIỂN THỊ ĐÁ, ĐƯỜNG VÀ TOPPING --}}
                                    @if((isset($item['ice_level']) && $item['ice_level'] !== '100') || (isset($item['sugar_level']) && $item['sugar_level'] !== '100') || !empty($item['toppings']))
                                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                                            @if(isset($item['ice_level']) && $item['ice_level'] !== '100')
                                                <span class="text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 font-medium">
                                                    🧊 {{ $iceTexts[$item['ice_level']] ?? $item['ice_level'] }}
                                                </span>
                                            @endif
                                            
                                            @if(isset($item['sugar_level']) && $item['sugar_level'] !== '100')
                                                <span class="text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100 font-medium">
                                                    🍯 {{ $sugarTexts[$item['sugar_level']] ?? $item['sugar_level'] }}
                                                </span>
                                            @endif
                                            
                                            @if(!empty($item['toppings']))
                                                @foreach($item['toppings'] as $t_id => $t_data)
                                                    @php
                                                        $t_qty = is_array($t_data) ? ($t_data['qty'] ?? 0) : $t_data;
                                                        $t_name = is_array($t_data) ? ($t_data['name'] ?? 'Topping') : (\DB::table('products')->where('product_id', $t_id)->value('name') ?? 'Topping');
                                                    @endphp
                                                    @if($t_qty > 0)
                                                        <span class="text-[10px] text-espresso/60 italic bg-white inline-block px-1.5 py-0.5 rounded border border-gray-200 shadow-sm">
                                                            + {{ $t_name }} <span class="font-bold text-coral">x{{ $t_qty }}</span>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Giá tiền & Nút đánh giá --}}
                            <div class="shrink-0 flex flex-col sm:items-end justify-between sm:w-1/3">
                                <div class="font-bold text-coral text-sm mb-3 sm:mb-0">{{ number_format($itemPrice, 0, ',', '.') }}đ</div>
                                
                                @if($order->status == 'completed')
                                    @if($isReviewed)
                                        <span class="text-[11px] font-bold text-emerald-500 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 flex items-center justify-center gap-1 shadow-sm w-full sm:w-auto mt-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Đã đánh giá
                                        </span>
                                    @else
                                        <button type="button" onclick="openReviewModal('{{ $realProductId }}', '{{ addslashes($itemName) }}')" class="w-full sm:w-auto text-[11px] font-bold text-coral bg-coral/5 border border-coral/30 px-3 py-1.5 rounded-xl hover:bg-coral hover:text-white transition-all shadow-sm flex items-center justify-center gap-1 mt-2">
                                            ⭐ Viết Đánh Giá
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-sm text-gray-500 font-medium">Không thể hiển thị chi tiết sản phẩm.</p>
                            <p class="text-xs text-gray-400 mt-1">Vui lòng liên hệ quán để kiểm tra lại.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Tổng Tiền --}}
                <div class="mt-6 pt-4 border-t border-gray-200">
                    @if(isset($order->discount_amount) && $order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-espresso/60 mb-2"><span>Giảm giá Voucher:</span><span class="text-coral font-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span></div>
                    @endif
                    <div class="flex justify-between items-end">
                        <span class="font-bold text-espresso uppercase tracking-wider text-sm">Tổng thanh toán:</span>
                        <span class="font-black text-2xl text-coral">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                    @if($order->status == 'pending')
                        <form action="{{ route('user.orders.cancel', $order->order_id) }}" method="POST" class="mt-4 text-right" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-red-50 text-red-500 font-bold text-sm rounded-xl hover:bg-red-500 hover:text-white transition-colors border border-red-100">Hủy đơn hàng</button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL ĐÁNH GIÁ DÙNG CHUNG --}}
@if($order->status == 'completed')
<div id="review-modal" class="fixed inset-0 z-[100] bg-black/60 hidden items-center justify-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-[32px] p-6 md:p-8 max-w-lg w-full mx-4 shadow-2xl relative">
        <button type="button" onclick="closeReviewModal()" class="absolute top-6 right-6 text-gray-400 hover:text-red-500 bg-gray-100 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <h3 class="font-serif font-black text-2xl text-espresso mb-1">Đánh giá món</h3>
        <p id="review-product-name" class="text-coral font-bold mb-6 pb-4 border-b border-gray-100 text-sm">Đang tải...</p>
        
        <form action="{{ route('user.orders.review') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <input type="hidden" name="product_id" id="review-product-id" value="">
            
            <div>
                <label class="block text-sm font-bold text-espresso mb-1">Mức độ hài lòng của bạn</label>
                <div class="star-rating flex flex-row-reverse justify-end gap-1">
                    <input type="radio" id="s5" name="rating" value="5" class="hidden" checked><label for="s5" class="text-4xl text-gray-300 cursor-pointer transition-colors">★</label>
                    <input type="radio" id="s4" name="rating" value="4" class="hidden"><label for="s4" class="text-4xl text-gray-300 cursor-pointer transition-colors">★</label>
                    <input type="radio" id="s3" name="rating" value="3" class="hidden"><label for="s3" class="text-4xl text-gray-300 cursor-pointer transition-colors">★</label>
                    <input type="radio" id="s2" name="rating" value="2" class="hidden"><label for="s2" class="text-4xl text-gray-300 cursor-pointer transition-colors">★</label>
                    <input type="radio" id="s1" name="rating" value="1" class="hidden"><label for="s1" class="text-4xl text-gray-300 cursor-pointer transition-colors">★</label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-espresso mb-2">Chia sẻ cảm nhận của bạn</label>
                <textarea name="comment" rows="3" placeholder="Đồ uống có ngon không?..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-coral focus:outline-none text-espresso text-sm resize-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-espresso mb-2">Đính kèm ảnh thực tế (Tùy chọn)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-coral/10 file:text-coral hover:file:bg-coral hover:file:text-white transition-all cursor-pointer">
            </div>

            <button type="submit" class="w-full py-4 bg-coral text-white font-bold rounded-xl hover:bg-[#d5523b] transition-colors shadow-lg shadow-coral/30 uppercase tracking-widest text-sm">
                Gửi Đánh Giá Ngay
            </button>
        </form>
    </div>
</div>
@endif

<script>
    function openReviewModal(productId, productName) {
        document.getElementById('review-product-id').value = productId;
        document.getElementById('review-product-name').innerText = "Món: " + productName;
        let modal = document.getElementById('review-modal');
        if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    }
    function closeReviewModal() {
        let modal = document.getElementById('review-modal');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }
</script>
@endsection