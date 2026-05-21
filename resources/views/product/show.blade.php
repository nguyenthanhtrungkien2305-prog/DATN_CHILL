@extends('layouts.app')

@section('title', $product->name . ' - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    
    {{-- KIỂM TRA XEM SẢN PHẨM NÀY CÓ PHẢI LÀ TOPPING KHÔNG --}}
    @php
        $categoryName = \DB::table('categories')->where('category_id', $product->category_id)->value('name');
        $isToppingCategory = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');
    @endphp
    <div class="max-w-7xl mx-auto px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex text-sm text-espresso/60 mb-8">
            <a href="/" class="hover:text-coral transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('product.index') }}" class="hover:text-coral transition-colors">Thực đơn</a>
            <span class="mx-2">/</span>
            <span class="text-espresso font-medium">{{ $product->name }}</span>
        </nav>

        {{-- ========================================= --}}
        {{-- PHẦN 1: THÔNG TIN SẢN PHẨM CHÍNH --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start bg-white p-8 md:p-12 rounded-[40px] shadow-xl mb-16">
            
            {{-- Cột Trái: Ảnh sản phẩm --}}
            <div class="order-1 md:order-1">
                <div class="bg-cream rounded-[32px] overflow-hidden aspect-square relative shadow-inner group">
                    <img id="main-image" src="{{ $product->image_url ?? 'https://via.placeholder.com/600' }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply transition-transform duration-700 group-hover:scale-105" />
                </div>
            </div>

            {{-- Cột Phải: Thông tin & Nút mua --}}
            <div class="order-2 md:order-2 flex flex-col h-full">
                <span class="inline-block bg-coral/10 text-coral text-xs font-bold px-3 py-1 rounded-full w-max mb-4 uppercase tracking-widest">Đặc trưng</span>
                
                <h1 class="font-serif font-bold text-4xl md:text-5xl text-espresso mb-4">{{ $product->name }}</h1>
                
                {{-- Đánh giá nhanh --}}
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex text-coral text-sm">★★★★★</div>
                    <span class="text-espresso/60 text-sm">(0 đánh giá)</span>
                </div>

                {{-- Giá tiền --}}
                <div class="mb-6 flex items-end gap-4">
                    <span id="product-price" class="text-4xl font-black text-espresso">
                        {{ $variants->count() > 0 ? number_format($variants[0]->price, 0, ',', '.') : 0 }} đ
                    </span>
                </div>

                <p class="text-espresso/80 leading-relaxed mb-8 line-clamp-3">{{ $product->description }}</p>

                {{-- Chọn Kích cỡ (Size) --}}
                @if($variants->count() > 0)
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-espresso uppercase tracking-wider mb-3">Chọn Kích Cỡ</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($variants as $index => $variant)
                            <label class="cursor-pointer">
                                <input type="radio" name="size" class="peer sr-only" 
                                       value="{{ $variant->variant_id }}" 
                                       data-price="{{ $variant->price }}" 
                                       {{ $index === 0 ? 'checked' : '' }}>
                                <div class="px-6 py-2 rounded-full border border-espresso/20 text-espresso peer-checked:bg-espresso peer-checked:text-white peer-checked:border-espresso hover:border-espresso transition-all">
                                    {{ $variant->size_name }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- NÚT GỌI POPUP TOPPING (Chỉ hiện nếu sản phẩm có topping) --}}
                @if($toppings->count() > 0 && !$isToppingCategory)
                <div class="mb-6 pt-6 border-t border-espresso/10">
                    <button type="button" onclick="openToppingModal()" class="w-full py-4 rounded-xl border-2 border-dashed border-coral text-coral font-bold flex items-center justify-center gap-2 hover:bg-coral/5 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Thêm Topping bạn nhé!
                    </button>
                    
                    {{-- KHU VỰC HIỂN THỊ TOPPING ĐÃ CHỌN (Ban đầu ẩn) --}}
                    <div id="selected-toppings-container" class="flex flex-wrap gap-2 mt-4 hidden">
                        </div>
                </div>
                @endif

                {{-- Hành động --}}
                <div class="mt-auto pt-4 flex gap-4">
                    {{-- Bộ đếm số lượng Món chính --}}
                    <div class="flex items-center border border-espresso/20 rounded-full px-2 h-14 w-32 shrink-0 bg-[#FAF7F2]">
                        <button type="button" onclick="updateQuantity(-1)" class="w-10 h-10 flex items-center justify-center text-espresso font-bold hover:text-coral">-</button>
                        <input type="number" id="quantity" value="1" min="1" class="w-full text-center bg-transparent border-none outline-none font-bold text-espresso focus:ring-0 appearance-none pointer-events-none">
                        <button type="button" onclick="updateQuantity(1)" class="w-10 h-10 flex items-center justify-center text-espresso font-bold hover:text-coral">+</button>
                    </div>
                    
                    {{-- Nút Thêm vào giỏ (Thêm trực tiếp) --}}
                    <button type="button" onclick="submitAddToCart()" class="flex-1 bg-coral text-white h-14 rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all flex items-center justify-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- PHẦN 2: HỆ THỐNG TABS (MÔ TẢ, TOPPING, ĐÁNH GIÁ) --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-[40px] shadow-sm p-8 md:p-12 mb-16 border border-espresso/5">
            {{-- Thanh điều hướng Tabs --}}
            <div class="flex flex-wrap gap-8 border-b border-espresso/10 mb-8">
                <button onclick="switchTab('desc')" id="btn-tab-desc" class="pb-4 font-bold text-lg text-coral border-b-2 border-coral transition-colors">Chi tiết sản phẩm</button>
                
                @if($toppings->count() > 0 && !$isToppingCategory)
                <button onclick="switchTab('topping')" id="btn-tab-topping" class="pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors">Topping ăn kèm</button>
                @endif
                
                <button onclick="switchTab('review')" id="btn-tab-review" class="pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors">Đánh giá (0)</button>
            </div>

            {{-- Nội dung Tab 1: Chi tiết --}}
            <div id="tab-desc" class="tab-content text-espresso/80 leading-relaxed space-y-4">
                <p>{{ $product->description }}</p>
                <p>Thành phần 100% tự nhiên, được lựa chọn kỹ lưỡng. Thích hợp để thưởng thức vào mọi thời điểm trong ngày.</p>
            </div>

            {{-- Nội dung Tab 2: Topping (Các sản phẩm mua kèm) --}}
            @if($toppings->count() > 0 && !$isToppingCategory)
            <div id="tab-topping" class="tab-content hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($toppings as $top)
                    <div class="border border-espresso/10 rounded-2xl p-4 text-center hover:border-coral transition-colors cursor-pointer group">
                        <img src="{{ $top->image ?? 'https://via.placeholder.com/200' }}" class="w-16 h-16 rounded-full mx-auto object-cover mb-3 group-hover:scale-110 transition-transform">
                        <h4 class="font-bold text-espresso mb-1 text-sm">{{ $top->name }}</h4>
                        <p class="text-coral font-bold text-sm">+{{ number_format($top->price, 0, ',', '.') }}đ</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Nội dung Tab 3: Đánh giá --}}
            <div id="tab-review" class="tab-content hidden text-center py-8">
                <p class="text-espresso/60 italic">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên trải nghiệm!</p>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- PHẦN 3: SẢN PHẨM LIÊN QUAN --}}
        {{-- ========================================= --}}
        <div>
            <h2 class="font-serif font-bold text-3xl text-espresso mb-8">Có thể bạn sẽ thích</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($relatedProducts as $related)
                    <article class="product-card bg-white rounded-[24px] p-4 flex flex-col relative group border border-transparent hover:border-coral/20 shadow-sm">
                        <div class="w-full aspect-square rounded-[16px] overflow-hidden bg-cream relative mb-4">
                            <a href="{{ route('product.show', $related->slug) }}" class="block w-full h-full">
                                <img src="{{ $related->image_url ?? 'https://via.placeholder.com/400' }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            </a>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-espresso mb-1 group-hover:text-coral transition-colors">
                            <a href="{{ route('product.show', $related->slug) }}">{{ $related->name }}</a>
                        </h3>
                        <p class="text-sm text-espresso/60 mb-3 line-clamp-1">{{ $related->description }}</p>
                        <div class="mt-auto">
                            <span class="text-espresso font-bold text-lg">Từ {{ number_format($related->min_price, 0, ',', '.') }} đ</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ========================================= --}}
{{-- MODAL (POP-UP) CHỌN TOPPING KHI MUA --}}
{{-- ========================================= --}}
@if($toppings->count() > 0 && !$isToppingCategory)
<div id="topping-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
    {{-- Nền đen mờ (Click vào nền sẽ đóng pop-up) --}}
    <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeToppingModal()"></div>
    
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg p-8 transform transition-all scale-95 opacity-0" id="topping-modal-content">
        
        <button onclick="closeToppingModal()" class="absolute top-6 right-6 text-espresso/40 hover:text-coral">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <h2 class="font-serif font-black text-2xl text-espresso mb-2">Thêm chút Topping nhé?</h2>
        <p class="text-espresso/60 text-sm mb-6">Món nước của bạn sẽ ngon hơn rất nhiều nếu có thêm nhai nhai giòn giòn đấy!</p>

        {{-- Danh sách Topping (Có nút Cộng Trừ) --}}
        <div class="space-y-3 mb-8 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
            @foreach($toppings as $top)
            <div class="flex items-center justify-between p-3 border border-espresso/10 rounded-xl hover:bg-[#FAF7F2] transition-colors group">
                <div class="flex items-center gap-3">
                    <img src="{{ $top->image ?? 'https://via.placeholder.com/200' }}" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <span class="block font-medium text-espresso">{{ $top->name }}</span>
                        <span class="font-bold text-coral">+{{ number_format($top->price, 0, ',', '.') }}đ</span>
                    </div>
                </div>
                
                {{-- Nút tăng giảm số lượng cho TỪNG LOẠI Topping --}}
                <div class="flex items-center border border-espresso/20 rounded-full h-10 w-28 bg-white shrink-0">
                    <button type="button" onclick="updateToppingQty({{ $top->topping_id }}, -1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg">-</button>
                    
                    <input type="number" id="topping-qty-{{ $top->topping_id }}" 
                           data-name="{{ $top->name }}" 
                           data-price="{{ $top->price }}"
                           value="0" min="0" 
                           class="topping-input w-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 appearance-none pointer-events-none p-0">
                    
                    <button type="button" onclick="updateToppingQty({{ $top->topping_id }}, 1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg">+</button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Nút Xác nhận đóng Pop-up và tính tiền --}}
        <button onclick="applyToppings()" class="w-full py-4 bg-coral text-white rounded-full font-bold hover:bg-[#d5523b] shadow-lg transition-all">
            Xong & Đóng
        </button>
    </div>
</div>
@endif

{{-- ========================================= --}}
{{-- JAVASCRIPT XỬ LÝ GIAO DIỆN --}}
{{-- ========================================= --}}
<style>
    /* Làm đẹp thanh cuộn cho danh sách topping */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e8634a; border-radius: 10px; }
</style>

<script>
    // BIẾN TOÀN CỤC LƯU GIÁ
    let currentVariantPrice = 0;
    let currentToppingPrice = 0;

    // 1. Hàm tính lại Tổng tiền (Size + Topping)
    function calculateTotalPrice() {
        let variantRadio = document.querySelector('input[name="size"]:checked');
        if(variantRadio) {
            currentVariantPrice = parseFloat(variantRadio.getAttribute('data-price'));
        }

        let total = currentVariantPrice + currentToppingPrice;
        document.getElementById('product-price').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    // Gắn sự kiện thay đổi Size
    document.querySelectorAll('input[name="size"]').forEach(radio => {
        radio.addEventListener('change', calculateTotalPrice);
    });
    
    // Gọi tính giá lần đầu khi load trang
    window.addEventListener('DOMContentLoaded', calculateTotalPrice);

    // 2. Tăng giảm số lượng Món chính
    function updateQuantity(change) {
        let input = document.getElementById('quantity');
        let newVal = parseInt(input.value) + change;
        if(newVal >= 1) input.value = newVal;
    }

    // 3. Hệ thống chuyển Tabs
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="btn-tab-"]').forEach(el => {
            el.classList.remove('text-coral', 'border-coral');
            el.classList.add('text-espresso/50', 'border-transparent');
        });

        document.getElementById('tab-' + tabId).classList.remove('hidden');
        let activeBtn = document.getElementById('btn-tab-' + tabId);
        activeBtn.classList.remove('text-espresso/50', 'border-transparent');
        activeBtn.classList.add('text-coral', 'border-coral');
    }

    // 4. Xử lý Modal (Pop-up) Topping
    const modal = document.getElementById('topping-modal');
    const modalContent = document.getElementById('topping-modal-content');

    function openToppingModal() {
        if(modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closeToppingModal() {
        if(modalContent) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    // 5. Tăng/Giảm số lượng cho từng loại Topping trong Pop-up
    function updateToppingQty(toppingId, change) {
        let input = document.getElementById('topping-qty-' + toppingId);
        let currentVal = parseInt(input.value) || 0;
        let newVal = currentVal + change;
        
        if (newVal >= 0) {
            input.value = newVal;
        }
    }

    // 6. Áp dụng Topping (Được gọi khi bấm "Xong & Đóng" ở Modal)
    function applyToppings() {
        let container = document.getElementById('selected-toppings-container');
        let html = '';
        currentToppingPrice = 0; 
        let hasToppings = false;

        document.querySelectorAll('.topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if (qty > 0) {
                hasToppings = true;
                let name = input.getAttribute('data-name');
                let price = parseFloat(input.getAttribute('data-price'));
                
                currentToppingPrice += (price * qty);
                
                html += `
                    <div class="px-4 py-1.5 bg-coral/10 text-coral border border-coral/20 rounded-full text-sm font-medium flex items-center gap-1 shadow-sm">
                        ${name} <span class="text-xs bg-coral text-white px-1.5 rounded-full ml-1">x${qty}</span>
                    </div>
                `;
            }
        });

        if (hasToppings) {
            container.innerHTML = html;
            container.classList.remove('hidden');
        } else {
            container.innerHTML = '';
            container.classList.add('hidden');
        }

        calculateTotalPrice();
        closeToppingModal();
    }

    // 7. Chốt đơn Thêm vào giỏ hàng (GỬI DỮ LIỆU LÊN SERVER)
    function submitAddToCart() {
        let productId = '{{ $product->product_id }}';
        let mainQty = parseInt(document.getElementById('quantity').value);
        
        let variantId = document.querySelector('input[name="size"]:checked');
        if(!variantId) {
            alert('Vui lòng chọn Kích cỡ!');
            return;
        }
        variantId = variantId.value;

        let toppingsData = {};
        document.querySelectorAll('.topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if (qty > 0) {
                let topId = input.id.replace('topping-qty-', '');
                toppingsData[topId] = qty;
            }
        });

        let payload = {
            _token: '{{ csrf_token() }}',
            product_id: productId,
            variant_id: variantId,
            quantity: mainQty,
            toppings: toppingsData
        };

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Hiển thị thông báo thành công đẹp mắt hơn (Tạm thời dùng alert)
                alert('🎉 Thành công: ' + data.message);
                
                // Cập nhật số lượng trên icon Header (Nếu bạn có thẻ span id="cart-count")
                // let cartCountEl = document.getElementById('cart-count');
                // if(cartCountEl) cartCountEl.innerText = data.cart_count;
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi hệ thống:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại sau!');
        });
    }
</script>
@endsection