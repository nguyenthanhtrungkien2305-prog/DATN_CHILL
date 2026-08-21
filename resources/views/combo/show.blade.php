@extends('layouts.app')

@section('title', 'Gói Combo ' . $combo->name . ' - Chill Chill')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.03); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
</style>

<div class="bg-[#FAF7F2] py-12 min-h-screen">
    
    @php
        $reviewCount = $reviews->count();
        $avgRating = $reviewCount > 0 ? round($reviews->avg('rating'), 1) : 0;
        $roundedAvg = round($avgRating);

        $count5 = $reviews->where('rating', 5)->count();
        $count4 = $reviews->where('rating', 4)->count();
        $count3 = $reviews->where('rating', 3)->count();
        $count2 = $reviews->where('rating', 2)->count();
        $count1 = $reviews->where('rating', 1)->count();
    @endphp

    <div class="max-w-6xl mx-auto px-4 md:px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex text-xs md:text-sm text-espresso/60 mb-6">
            <a href="/" class="hover:text-coral transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('combo.index') }}" class="hover:text-coral transition-colors">Gói Combo</a>
            <span class="mx-2">/</span>
            <span class="text-espresso font-medium line-clamp-1">{{ $combo->name }}</span>
        </nav>

        {{-- KHỐI CHI TIẾT COMBO --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start bg-white p-6 md:p-8 rounded-[32px] shadow-lg mb-12 border border-espresso/5">
            
            {{-- KHU VỰC ẢNH COMBO --}}
            <div class="order-1 md:order-1 md:col-span-5 flex flex-col items-center gap-4 w-full">
                <div class="bg-cream rounded-[28px] overflow-hidden w-full max-w-[420px] aspect-square relative shadow-inner group border border-espresso/5">
                    <img id="main-image" src="{{ format_image_url($combo->image_url ?? $combo->image, '/images/logo1.jpg', $combo->name) }}" alt="{{ $combo->name }}" class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105" onerror="this.onerror=null; this.src='/images/logo1.jpg';" />
                    
                    @if($combo->original_price > $combo->price)
                        @php
                            $percent = round((($combo->original_price - $combo->price) / $combo->original_price) * 100);
                        @endphp
                        <span class="absolute top-4 left-4 bg-gradient-to-r from-red-500 to-coral text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                            🔥 TIẾT KIỆM {{ $percent }}%
                        </span>
                    @endif
                </div>
            </div>

            {{-- KHU VỰC THÔNG TIN GÓI COMBO --}}
            <div class="order-2 md:order-2 md:col-span-7 flex flex-col h-full">
                <span class="inline-block bg-orange-100 text-orange-600 border border-orange-200 text-xs font-bold px-3 py-1 rounded-full w-max mb-3 uppercase tracking-widest">
                    🎁 Gói Combo Tiết Kiệm Độc Quyền
                </span>
                
                <h1 class="font-serif font-bold text-3xl md:text-4xl text-espresso mb-3 leading-snug">{{ $combo->name }}</h1>
                
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex text-yellow-400 text-sm">
                        {{ str_repeat('★', $roundedAvg) }}{{ str_repeat('☆', 5 - $roundedAvg) }}
                    </div>
                    <span class="text-espresso/60 text-sm">
                        @if($reviewCount > 0)
                            ({{ number_format($avgRating, 1) }}/5 sao - {{ $reviewCount }} đánh giá thực tế)
                        @else
                            (Chưa có đánh giá thực tế)
                        @endif
                    </span>
                </div>

                <div class="mb-5 flex items-baseline gap-4">
                    <span class="text-3xl md:text-4xl font-black text-coral">
                        {{ number_format($combo->price, 0, ',', '.') }} đ
                    </span>
                    @if($combo->original_price > $combo->price)
                        <span class="text-base text-espresso/40 line-through font-medium">
                            {{ number_format($combo->original_price, 0, ',', '.') }} đ
                        </span>
                    @endif
                </div>

                <p class="text-espresso/80 leading-relaxed mb-6 text-sm md:text-base">{{ $combo->description ?? 'Gói kết hợp đồ uống & bánh ngọt chuẩn vị với mức giá cực kỳ ưu đãi.' }}</p>

                {{-- DANH SÁCH MÓN BAO GỒM TRONG COMBO (ĐẶC THÙ RIÊNG CỦA COMBO) --}}
                <div class="bg-amber-50/80 p-4 md:p-5 rounded-2xl border border-amber-200/80 mb-6 space-y-2.5">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5 border-b border-amber-200/60 pb-2">
                        <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Sản phẩm có trong Combo:
                    </h3>

                    <ul class="divide-y divide-amber-200/40 max-h-56 md:max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($combo->products as $prod)
                            <li class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ format_image_url($prod->image_url, '/images/logo1.jpg', $prod->name) }}" alt="{{ $prod->name }}" class="w-10 h-10 object-cover rounded-lg border border-amber-200 shrink-0" onerror="this.onerror=null; this.src='/images/logo1.jpg';">
                                    <span class="text-sm font-bold text-espresso line-clamp-1">{{ $prod->name }}</span>
                                </div>
                                <span class="font-bold text-xs text-coral bg-white px-2.5 py-1 rounded-lg border border-coral/20 shrink-0 ml-2">
                                    x{{ $prod->pivot->quantity }} phần
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- SỐ LƯỢNG VÀ NÚT THÊM VÀO GIỎ --}}
                <div class="mt-auto pt-2 flex gap-4">
                    <div class="flex items-center justify-between border border-espresso/20 rounded-full h-14 w-32 shrink-0 bg-[#FAF7F2] overflow-hidden">
                        <button type="button" onclick="updateComboQuantity(-1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-xl transition-colors">-</button>
                        <input type="text" id="combo-quantity" value="1" readonly class="w-12 h-full text-center bg-transparent border-none outline-none font-bold text-espresso focus:ring-0 p-0 m-0 leading-none">
                        <button type="button" onclick="updateComboQuantity(1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-xl transition-colors">+</button>
                    </div>
                    
                    <button type="button" onclick="submitAddComboToCart()" class="flex-1 bg-coral text-white h-14 rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all flex items-center justify-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB NỘI DUNG CHI TIẾT & ĐÁNH GIÁ (TƯƠNG TỰ TRANG SẢN PHẨM) --}}
        <div class="bg-white rounded-[40px] shadow-sm p-8 md:p-12 mb-16 border border-espresso/5">
            <div class="flex flex-wrap gap-8 border-b border-espresso/10 mb-8">
                <button onclick="switchTab('desc')" id="btn-tab-desc" class="pb-4 font-bold text-lg text-coral border-b-2 border-coral transition-colors">Chi tiết Gói Combo</button>
                <button onclick="switchTab('review')" id="btn-tab-review" class="pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors">Đánh giá ({{ $reviewCount }})</button>
            </div>

            {{-- Tab Chi Tiết --}}
            <div id="tab-desc" class="tab-content text-espresso/80 leading-relaxed space-y-4">
                <p>{{ $combo->description ?? 'Gói Combo kết hợp tuyệt vời dành cho bạn và người thân.' }}</p>
                <p>Thành phần các món nguyên chất 100% tự nhiên, được sơ chế tươi sạch mỗi ngày. Giá trọn gói ưu đãi giảm trực tiếp trên tổng hóa đơn.</p>
            </div>

            {{-- Tab Đánh Giá --}}
            <div id="tab-review" class="tab-content hidden">
                <div class="bg-white p-8 rounded-[32px] border border-espresso/5 shadow-sm">
                    <h3 class="font-serif font-bold text-xl text-espresso mb-6 uppercase tracking-widest">Đánh giá về Combo</h3>
                    
                    <div class="bg-[#fffbf8] border border-[#f9ede5] p-6 mb-8 flex flex-col md:flex-row items-center gap-8 rounded-sm">
                        <div class="text-center md:w-1/4 shrink-0">
                            <div class="text-[#ee4d2d] mb-1">
                                <span class="text-4xl font-black">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-xl font-medium"> trên 5</span>
                            </div>
                            <div class="text-[#ee4d2d] text-2xl tracking-widest">
                                {{ str_repeat('★', $roundedAvg) }}{{ str_repeat('☆', 5 - $roundedAvg) }}
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-3 md:w-3/4">
                            <button type="button" onclick="filterReviews('all', this)" class="filter-btn px-6 py-1.5 border border-[#ee4d2d] text-[#ee4d2d] bg-white rounded-sm text-sm transition-colors">Tất Cả</button>
                            <button type="button" onclick="filterReviews(5, this)" class="filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors">5 Sao ({{ $count5 }})</button>
                            <button type="button" onclick="filterReviews(4, this)" class="filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors">4 Sao ({{ $count4 }})</button>
                            <button type="button" onclick="filterReviews(3, this)" class="filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors">3 Sao ({{ $count3 }})</button>
                            <button type="button" onclick="filterReviews(2, this)" class="filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors">2 Sao ({{ $count2 }})</button>
                            <button type="button" onclick="filterReviews(1, this)" class="filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors">1 Sao ({{ $count1 }})</button>
                        </div>
                    </div>

                    <div class="max-h-[500px] overflow-y-auto custom-scrollbar pr-4 space-y-6" id="review-list-container">
                        @forelse($reviews as $review)
                            <div class="review-item border-b border-gray-100 pb-6 last:border-0 last:pb-0" data-rating="{{ $review->rating }}">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 shadow-sm shrink-0">
                                        <img src="{{ $review->user_avatar ? asset($review->user_avatar) : 'https://i.pravatar.cc/150?u=' . ($review->user_id ?? 1) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-bold text-espresso">{{ $review->user_name ?? 'Khách hàng Chill Chill' }}</h4>
                                            <span class="text-xs text-gray-400 font-medium">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="text-[#ee4d2d] text-sm mt-0.5 mb-2">
                                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                        </div>
                                        @if($review->comment)
                                            <p class="text-espresso/80 text-sm mb-3 leading-relaxed">{{ $review->comment }}</p>
                                        @endif
                                        @if(isset($review->image) && $review->image)
                                            <img src="{{ asset($review->image) }}" class="w-32 h-32 object-cover rounded-xl border border-gray-200 cursor-pointer hover:opacity-90">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12" id="empty-state-default">
                                <p class="text-gray-400 italic mb-2">Chưa có đánh giá riêng cho gói combo này.</p>
                                <p class="text-espresso/50 text-sm font-medium">Hãy là người đầu tiên đặt và trải nghiệm ngay nhé!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- KHỐI CÁC GÓI COMBO LIÊN QUAN (TƯƠNG TỰ SẢN PHẨM LIÊN QUAN) --}}
        @if($otherCombos->count() > 0)
        <div>
            <h2 class="font-serif font-bold text-3xl text-espresso mb-8">Gói Combo liên quan khác</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($otherCombos as $other)
                    <article class="product-card bg-white rounded-[24px] p-4 flex flex-col relative group border border-transparent hover:border-coral/20 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="w-full aspect-square rounded-[16px] overflow-hidden bg-cream relative mb-4">
                            <a href="{{ route('combo.show', $other->combo_id) }}" class="block w-full h-full">
                                <img src="{{ format_image_url($other->image_url ?? $other->image, '/images/logo1.jpg', $other->name) }}" alt="{{ $other->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.onerror=null; this.src='/images/logo1.jpg';" />
                            </a>
                            @if($other->original_price > $other->price)
                                @php
                                    $relPercent = round((($other->original_price - $other->price) / $other->original_price) * 100);
                                @endphp
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm">
                                    -{{ $relPercent }}%
                                </span>
                            @endif
                        </div>
                        <h3 class="font-serif font-bold text-lg text-espresso mb-1 group-hover:text-coral transition-colors line-clamp-1">
                            <a href="{{ route('combo.show', $other->combo_id) }}">{{ $other->name }}</a>
                        </h3>
                        <p class="text-xs text-espresso/60 mb-3 line-clamp-2">{{ $other->description ?? 'Combo ưu đãi hấp dẫn dành cho bạn.' }}</p>
                        <div class="mt-auto flex items-center justify-between border-t border-espresso/5 pt-3">
                            <span class="text-coral font-black text-lg">{{ number_format($other->price, 0, ',', '.') }} đ</span>
                            <a href="{{ route('combo.show', $other->combo_id) }}" class="text-xs font-bold text-espresso hover:text-coral">Xem chi tiết →</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<script>
    function updateComboQuantity(change) {
        const qtyInput = document.getElementById('combo-quantity');
        let currentVal = parseInt(qtyInput.value) || 1;
        currentVal += change;
        if (currentVal < 1) currentVal = 1;
        qtyInput.value = currentVal;
    }

    function submitAddComboToCart() {
        const qty = parseInt(document.getElementById('combo-quantity').value) || 1;
        fetch("{{ route('cart.addCombo') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                combo_id: {{ $combo->combo_id }},
                quantity: qty
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const cartBadges = document.querySelectorAll('.cart-count-badge, #cart-count');
                cartBadges.forEach(b => b.textContent = data.cart_count);
                alert("🎉 " + data.message);
            } else {
                alert("⚠️ " + (data.message || "Có lỗi xảy ra"));
            }
        })
        .catch(err => {
            console.error(err);
            alert("⚠️ Không thể kết nối với máy chủ!");
        });
    }

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        document.getElementById('btn-tab-desc').className = tabName === 'desc' 
            ? 'pb-4 font-bold text-lg text-coral border-b-2 border-coral transition-colors' 
            : 'pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors';

        document.getElementById('btn-tab-review').className = tabName === 'review' 
            ? 'pb-4 font-bold text-lg text-coral border-b-2 border-coral transition-colors' 
            : 'pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors';
    }

    function filterReviews(rating, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.className = 'filter-btn px-6 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-sm text-sm hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-colors';
        });
        btn.className = 'filter-btn px-6 py-1.5 border border-[#ee4d2d] text-[#ee4d2d] bg-white rounded-sm text-sm transition-colors';

        const items = document.querySelectorAll('.review-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemRating = item.getAttribute('data-rating');
            if (rating === 'all' || itemRating == rating) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('empty-state-default');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }
</script>
@endsection
