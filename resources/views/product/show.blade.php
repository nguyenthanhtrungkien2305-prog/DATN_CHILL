@extends('layouts.app')

@section('title', $product->name . ' - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    
    @php
        $categoryName = \DB::table('categories')->where('category_id', $product->category_id)->value('name');
        $isBanhNgot = $isBanhNgot ?? ($categoryName && (str_contains(mb_strtolower($categoryName), 'bánh') || str_contains(mb_strtolower($categoryName), 'cake')));
        $isToppingCategory = $isToppingCategory ?? ($categoryName && (str_contains(mb_strtolower($categoryName), 'topping') || str_contains(mb_strtolower($categoryName), 'kèm')));
        
        $reviewCount = $reviews->count();
        $avgRating = $reviewCount > 0 ? round($reviews->avg('rating'), 1) : 0;
        $roundedAvg = round($avgRating);

        $count5 = $reviews->where('rating', 5)->count();
        $count4 = $reviews->where('rating', 4)->count();
        $count3 = $reviews->where('rating', 3)->count();
        $count2 = $reviews->where('rating', 2)->count();
        $count1 = $reviews->where('rating', 1)->count();
    @endphp

    <div class="max-w-5xl mx-auto px-4 md:px-6">
        
        <nav class="flex text-xs md:text-sm text-espresso/60 mb-6">
            <a href="/" class="hover:text-coral transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('product.index') }}" class="hover:text-coral transition-colors">Thực đơn</a>
            <span class="mx-2">/</span>
            <span class="text-espresso font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start bg-white p-6 md:p-8 rounded-[32px] shadow-lg mb-12">
            
            {{-- KHU VỰC ẢNH SẢN PHẨM & ẢNH PHỤ (CĂN GIỮA NẰM TRONG KHU VỰC ẢNH) --}}
            <div class="order-1 md:order-1 md:col-span-5 flex flex-col items-center gap-4 w-full">
                <div class="bg-cream rounded-[28px] overflow-hidden w-full max-w-[380px] aspect-square relative shadow-inner group border border-espresso/5">
                    <img id="main-image" src="{{ $gallery[0] ?? $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply transition-all duration-300 group-hover:scale-105" />
                </div>

                {{-- DANH SÁCH ẢNH PHỤ CĂN GIỮA BÊN DƯỚI ẢNH CHÍNH (TỐI ĐA 4 ẢNH PHỤ) --}}
                @if(count($gallery) > 0)
                <div class="flex flex-wrap justify-center items-center gap-3 w-full max-w-[380px]">
                    @foreach($gallery as $index => $img)
                        <div class="thumb-item bg-cream rounded-2xl overflow-hidden w-16 h-16 md:w-18 md:h-18 shrink-0 aspect-square cursor-pointer border-2 transition-all duration-300 transform {{ $index === 0 ? 'border-coral ring-2 ring-coral/40 scale-105 shadow-md opacity-100' : 'border-gray-200/60 opacity-70 hover:opacity-100 hover:border-coral' }}" onclick="changeMainImage(this, '{{ $img }}')">
                            <img src="{{ $img }}" class="w-full h-full object-cover mix-blend-multiply pointer-events-none" />
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- KHU VỰC THÔNG TIN SẢN PHẨM (VỪA VẶN KHUNG HÌNH) --}}
            <div class="order-2 md:order-2 md:col-span-7 flex flex-col h-full">
                <span class="inline-block bg-coral/10 text-coral text-xs font-bold px-3 py-1 rounded-full w-max mb-4 uppercase tracking-widest">Đặc trưng</span>
                
                <h1 class="font-serif font-bold text-4xl md:text-5xl text-espresso mb-4">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex text-yellow-400 text-sm">
                        {{ str_repeat('★', $roundedAvg) }}{{ str_repeat('☆', 5 - $roundedAvg) }}
                    </div>
                    <span class="text-espresso/60 text-sm">({{ $avgRating }}/5 sao - {{ $reviewCount }} đánh giá)</span>
                </div>

                <div class="mb-6 flex items-end gap-4">
                    <span id="product-price" class="text-4xl font-black text-espresso">
                        {{ $variants->count() > 0 ? number_format($variants[0]->price, 0, ',', '.') : 0 }} đ
                    </span>
                </div>

                <p class="text-espresso/80 leading-relaxed mb-8 line-clamp-3">{{ $product->description }}</p>

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

                @if(!$isToppingCategory && !$isBanhNgot)
                <div class="mb-6 pt-6 border-t border-espresso/10">
                    <button type="button" id="customization-btn" onclick="openToppingModal()" class="w-full py-4 rounded-xl border-2 border-dashed border-coral text-coral font-bold flex items-center justify-center gap-2 hover:bg-coral/5 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        <span id="customization-btn-text">+ Tùy chỉnh Đồ uống & Topping</span>
                    </button>
                    
                    <div id="selected-toppings-container" class="mt-3 hidden"></div>
                </div>
                @endif

                <div class="mt-auto pt-4 flex gap-4">
                    <div class="flex items-center justify-between border border-espresso/20 rounded-full h-14 w-32 shrink-0 bg-[#FAF7F2] overflow-hidden">
                        <button type="button" onclick="updateQuantity(-1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-xl transition-colors">-</button>
                        <input type="text" id="quantity" value="1" readonly class="w-12 h-full text-center bg-transparent border-none outline-none font-bold text-espresso focus:ring-0 p-0 m-0 leading-none">
                        <button type="button" onclick="updateQuantity(1)" class="w-10 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-xl transition-colors">+</button>
                    </div>
                    
                    <button type="button" onclick="submitAddToCart()" class="flex-1 bg-coral text-white h-14 rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all flex items-center justify-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[40px] shadow-sm p-8 md:p-12 mb-16 border border-espresso/5">
            <div class="flex flex-wrap gap-8 border-b border-espresso/10 mb-8">
                <button onclick="switchTab('desc')" id="btn-tab-desc" class="pb-4 font-bold text-lg text-coral border-b-2 border-coral transition-colors">Chi tiết sản phẩm</button>
                <button onclick="switchTab('review')" id="btn-tab-review" class="pb-4 font-bold text-lg text-espresso/50 border-b-2 border-transparent hover:text-coral transition-colors">Đánh giá ({{ $reviewCount }})</button>
            </div>

            <div id="tab-desc" class="tab-content text-espresso/80 leading-relaxed space-y-4">
                <p>{{ $product->description }}</p>
                <p>Thành phần 100% tự nhiên, được lựa chọn kỹ lưỡng. Thích hợp để thưởng thức vào mọi thời điểm trong ngày.</p>
            </div>

            <div id="tab-review" class="tab-content hidden">
                <div class="bg-white p-8 rounded-[32px] border border-espresso/5 shadow-sm">
                    <h3 class="font-serif font-bold text-xl text-espresso mb-6 uppercase tracking-widest">Đánh giá sản phẩm</h3>
                    
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
                                        <img src="{{ $review->user->avatar ? asset($review->user->avatar) : 'https://i.pravatar.cc/150?u='.$review->user_id }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-bold text-espresso">{{ $review->user->name ?? 'Khách hàng ẩn danh' }}</h4>
                                            <span class="text-xs text-gray-400 font-medium">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="text-[#ee4d2d] text-sm mt-0.5 mb-2">
                                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                        </div>
                                        @if($review->comment)
                                            <p class="text-espresso/80 text-sm mb-3 leading-relaxed">{{ $review->comment }}</p>
                                        @endif
                                        @if($review->image)
                                            <img src="{{ asset($review->image) }}" class="w-32 h-32 object-cover rounded-xl border border-gray-200 cursor-pointer hover:opacity-90">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12" id="empty-state-default">
                                <p class="text-gray-400 italic mb-2">Chưa có đánh giá nào cho sản phẩm này.</p>
                                <p class="text-espresso/50 text-sm font-medium">Hãy là người đầu tiên thưởng thức và đánh giá nhé!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

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
{{-- MODAL (POP-UP) TÙY CHỈNH ĐỒ UỐNG & TOPPING --}}
{{-- ========================================= --}}
@if(!$isToppingCategory && !$isBanhNgot)
<div id="topping-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeToppingModal()"></div>
    
    {{-- MỞ RỘNG MODAL: max-w-lg đổi thành max-w-2xl --}}
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-2xl p-8 transform transition-all scale-95 opacity-0 flex flex-col max-h-[90vh]" id="topping-modal-content">
        
        <button onclick="closeToppingModal()" class="absolute top-6 right-6 text-espresso/40 hover:text-coral z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <h2 class="font-serif font-black text-2xl text-espresso mb-2">Tùy chỉnh đồ uống</h2>
        <p class="text-espresso/60 text-sm mb-6">Thêm topping và điều chỉnh lượng đường, đá theo ý thích của bạn!</p>

        {{-- Thanh Tabs trong Modal --}}
        <div class="flex gap-6 border-b border-gray-200 mb-6 shrink-0">
            <button onclick="switchModalTab('modal-topping')" id="btn-modal-topping" class="pb-3 text-sm font-bold border-b-2 border-coral text-coral transition-colors">Topping ăn kèm</button>
            <button onclick="switchModalTab('modal-ice')" id="btn-modal-ice" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Lượng Đá</button>
            <button onclick="switchModalTab('modal-sugar')" id="btn-modal-sugar" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-coral transition-colors">Lượng Đường</button>
        </div>

        {{-- Nội dung Tab 1: Topping --}}
        <div id="modal-topping" class="modal-tab-content space-y-3 mb-6 overflow-y-auto pr-2 custom-scrollbar flex-1">
            @if($toppings->count() > 0)
                @foreach($toppings as $top)
                <div class="flex items-center justify-between p-3 border border-espresso/10 rounded-xl hover:bg-[#FAF7F2] transition-colors group">
                    <div class="flex items-center gap-3">
                        <img src="{{ $top->image ?? 'https://via.placeholder.com/200' }}" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <span class="block font-medium text-espresso">{{ $top->name }}</span>
                            <span class="font-bold text-coral">+{{ number_format($top->price, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border border-espresso/20 rounded-full h-10 w-28 bg-white shrink-0 overflow-hidden">
                        <button type="button" onclick="updateToppingQty({{ $top->topping_id }}, -1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">-</button>
                        <input type="text" id="topping-qty-{{ $top->topping_id }}" data-name="{{ $top->name }}" data-price="{{ $top->price }}" value="0" readonly class="topping-input w-12 h-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 p-0 m-0 leading-none">
                        <button type="button" onclick="updateToppingQty({{ $top->topping_id }}, 1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">+</button>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-center text-gray-400 italic py-8">Không có topping đi kèm cho sản phẩm này.</p>
            @endif
        </div>

        {{-- Nội dung Tab 2: Lượng Đá --}}
        <div id="modal-ice" class="modal-tab-content hidden space-y-4 mb-6 overflow-y-auto pr-2 custom-scrollbar flex-1">
            <div class="grid grid-cols-2 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="ice_level" value="100" class="peer sr-only" data-extra-price="0" checked>
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">100% Đá (Mặc định)</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="ice_level" value="70" class="peer sr-only" data-extra-price="0">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">70% Đá</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="ice_level" value="50" class="peer sr-only" data-extra-price="0">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">50% Đá</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="ice_level" value="20" class="peer sr-only" data-extra-price="0">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">20% Đá</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="ice_level" value="0" class="peer sr-only" data-extra-price="0">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">0% Đá (Không đá)</div>
                </label>
                <label class="cursor-pointer col-span-2">
                    <input type="radio" name="ice_level" value="0_full" class="peer sr-only" data-extra-price="3000">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50 flex justify-between items-center px-6">
                        <span>0% Đá (Nước đầy ly)</span>
                        <span class="text-coral bg-white px-3 py-1 rounded-lg border border-coral/20">+3.000đ</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Nội dung Tab 3: Lượng Đường --}}
        <div id="modal-sugar" class="modal-tab-content hidden space-y-4 mb-6 overflow-y-auto pr-2 custom-scrollbar flex-1">
            <div class="grid grid-cols-2 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="sugar_level" value="100" class="peer sr-only" checked>
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">100% Đường (Mặc định)</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="sugar_level" value="70" class="peer sr-only">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">70% Đường</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="sugar_level" value="50" class="peer sr-only">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">50% Đường</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="sugar_level" value="20" class="peer sr-only">
                    <div class="px-4 py-4 rounded-xl border border-gray-200 text-center peer-checked:border-coral peer-checked:bg-coral/5 peer-checked:text-coral font-bold transition-all hover:bg-gray-50">20% Đường</div>
                </label>
            </div>
        </div>

        <button onclick="applyToppings()" class="w-full py-4 bg-coral text-white rounded-full font-bold hover:bg-[#d5523b] shadow-lg transition-all shrink-0">
            Xong & Đóng
        </button>
    </div>
</div>
@endif

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e8634a; border-radius: 10px; }
</style>

<script>
    let currentVariantPrice = 0;
    let currentToppingPrice = 0;
    let currentOptionsPrice = 0; // Thêm biến lưu giá trị phụ thu (như Không đá đầy ly)

    function calculateTotalPrice() {
        let variantRadio = document.querySelector('input[name="size"]:checked');
        if(variantRadio) {
            currentVariantPrice = parseFloat(variantRadio.getAttribute('data-price'));
        }

        let total = currentVariantPrice + currentToppingPrice + currentOptionsPrice;
        document.getElementById('product-price').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    document.querySelectorAll('input[name="size"]').forEach(radio => {
        radio.addEventListener('change', calculateTotalPrice);
    });
    
    // Gắn sự kiện khi khách chọn Lượng Đá để tự động update giá phụ thu
    document.querySelectorAll('input[name="ice_level"]').forEach(radio => {
        radio.addEventListener('change', () => {
            currentOptionsPrice = parseFloat(radio.getAttribute('data-extra-price')) || 0;
            calculateTotalPrice();
        });
    });

    window.addEventListener('DOMContentLoaded', calculateTotalPrice);

    function updateQuantity(change) {
        let input = document.getElementById('quantity');
        let newVal = parseInt(input.value) + change;
        if(newVal >= 1) input.value = newVal;
    }

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

    // Logic chuyển Tab riêng cho trong Modal
    function switchModalTab(tabId) {
        document.querySelectorAll('.modal-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="btn-modal-"]').forEach(el => {
            el.classList.remove('text-coral', 'border-coral');
            el.classList.add('text-gray-400', 'border-transparent');
        });

        document.getElementById(tabId).classList.remove('hidden');
        let activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.classList.remove('text-gray-400', 'border-transparent');
        activeBtn.classList.add('text-coral', 'border-coral');
    }

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

    function updateToppingQty(toppingId, change) {
        let input = document.getElementById('topping-qty-' + toppingId);
        let currentVal = parseInt(input.value) || 0;
        let newVal = currentVal + change;
        if (newVal >= 0) {
            input.value = newVal;
        }
    }

    function applyToppings() {
        let container = document.getElementById('selected-toppings-container');
        let btnText = document.getElementById('customization-btn-text');
        let btnBox = document.getElementById('customization-btn');
        let html = '';
        currentToppingPrice = 0;
        let selectedCount = 0; 
        
        // 1. Hiển thị Topping đã chọn
        document.querySelectorAll('.topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if (qty > 0) {
                selectedCount += qty;
                let name = input.getAttribute('data-name');
                let price = parseFloat(input.getAttribute('data-price'));
                currentToppingPrice += (price * qty);
                
                let priceText = (price * qty) > 0 ? ` (+${new Intl.NumberFormat('vi-VN').format(price * qty)}đ)` : '';
                
                html += `
                    <div class="px-3.5 py-1.5 bg-coral text-white rounded-full text-xs font-bold flex items-center gap-1.5 shadow-xs">
                        <span>✨ ${name}</span>
                        <span class="bg-white/30 text-white px-1.5 py-0.5 rounded-full text-[10px]">x${qty}</span>
                        <span class="text-[11px] font-semibold">${priceText}</span>
                    </div>
                `;
            }
        });

        // 2. Lấy Text của Lượng Đá và Đường để hiển thị ra ngoài
        let iceRadio = document.querySelector('input[name="ice_level"]:checked');
        let sugarRadio = document.querySelector('input[name="sugar_level"]:checked');
        
        currentOptionsPrice = iceRadio ? (parseFloat(iceRadio.getAttribute('data-extra-price')) || 0) : 0;
        
        let iceText = iceRadio ? iceRadio.nextElementSibling.innerText.split('\n')[0].trim() : '';
        let sugarText = sugarRadio ? sugarRadio.nextElementSibling.innerText.trim() : '';

        if (iceText && !iceText.includes('Mặc định') && !iceText.includes('100% Đá')) {
            selectedCount++;
            html += `<div class="px-3.5 py-1.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-full text-xs font-bold shadow-xs">🧊 ${iceText}</div>`;
        }
        if (sugarText && !sugarText.includes('Mặc định') && !sugarText.includes('100% Đường')) {
            selectedCount++;
            html += `<div class="px-3.5 py-1.5 bg-amber-100 text-amber-800 border border-amber-200 rounded-full text-xs font-bold shadow-xs">🍯 ${sugarText}</div>`;
        }

        if (html !== '') {
            container.innerHTML = `
                <div class="w-full bg-[#FAF7F2] border border-coral/25 rounded-2xl p-4 transition-all">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-coral flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Đã chọn tùy chỉnh (${selectedCount}):
                        </span>
                        <button type="button" onclick="openToppingModal()" class="text-xs font-bold text-coral hover:underline flex items-center gap-1">
                            Sửa tùy chỉnh ✎
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        ${html}
                    </div>
                </div>
            `;
            container.classList.remove('hidden');

            if (btnText) {
                btnText.innerHTML = `✓ Đã chọn tùy chỉnh (${selectedCount})`;
            }
            if (btnBox) {
                btnBox.className = "w-full py-3.5 rounded-xl border-2 border-coral bg-coral/10 text-coral font-bold flex items-center justify-center gap-2 hover:bg-coral/20 transition-all shadow-xs";
            }
        } else {
            container.innerHTML = '';
            container.classList.add('hidden');
            if (btnText) {
                btnText.innerHTML = `+ Tùy chỉnh Đồ uống & Topping`;
            }
            if (btnBox) {
                btnBox.className = "w-full py-4 rounded-xl border-2 border-dashed border-coral text-coral font-bold flex items-center justify-center gap-2 hover:bg-coral/5 transition-colors";
            }
        }

        calculateTotalPrice();
        closeToppingModal();
    }

    function submitAddToCart() {
        let productId = '{{ $product->product_id }}';
        let mainQty = parseInt(document.getElementById('quantity').value);
        
        let variantId = document.querySelector('input[name="size"]:checked');
        if(!variantId) {
            alert('Vui lòng chọn Kích cỡ!');
            return;
        }
        variantId = variantId.value;

        // Lấy Topping
        let toppingsData = {};
        document.querySelectorAll('.topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if (qty > 0) {
                let topId = input.id.replace('topping-qty-', '');
                toppingsData[topId] = qty;
            }
        });

        // Lấy lựa chọn Đá và Đường
        let iceLevel = document.querySelector('input[name="ice_level"]:checked')?.value || '100';
        let sugarLevel = document.querySelector('input[name="sugar_level"]:checked')?.value || '100';

        let payload = {
            _token: '{{ csrf_token() }}',
            product_id: productId,
            variant_id: variantId,
            quantity: mainQty,
            toppings: toppingsData,
            ice_level: iceLevel,      // Dữ liệu mới gửi lên server
            sugar_level: sugarLevel   // Dữ liệu mới gửi lên server
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
                showToast('🎉 ' + data.message, 'success');
                updateCartBadge(data.cart_count);
            } else {
                showToast('Lỗi: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Lỗi hệ thống:', error);
            showToast('Có lỗi xảy ra, vui lòng thử lại sau!', 'error');
        });
    }

    function changeMainImage(element, imageUrl) {
        const mainImg = document.getElementById('main-image');
        if (mainImg) {
            mainImg.style.opacity = '0.2';
            mainImg.style.transform = 'scale(0.96)';
            setTimeout(() => {
                mainImg.src = imageUrl;
                mainImg.style.opacity = '1';
                mainImg.style.transform = 'scale(1)';
            }, 150);
        }

        let thumbs = document.querySelectorAll('.thumb-item');
        thumbs.forEach(thumb => {
            thumb.classList.remove('border-coral', 'ring-2', 'ring-coral/40', 'scale-105', 'shadow-md', 'opacity-100');
            thumb.classList.add('border-gray-200/60', 'opacity-70');
        });

        element.classList.remove('border-gray-200/60', 'opacity-70');
        element.classList.add('border-coral', 'ring-2', 'ring-coral/40', 'scale-105', 'shadow-md', 'opacity-100');
    }

    function filterReviews(rating, btnElement) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('border-[#ee4d2d]', 'text-[#ee4d2d]');
            btn.classList.add('border-gray-200', 'text-gray-700');
        });
        btnElement.classList.remove('border-gray-200', 'text-gray-700');
        btnElement.classList.add('border-[#ee4d2d]', 'text-[#ee4d2d]');

        let items = document.querySelectorAll('.review-item');
        let visibleCount = 0;

        items.forEach(item => {
            let itemRating = item.getAttribute('data-rating');
            if (rating === 'all' || itemRating == rating) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let noResultMsg = document.getElementById('no-filter-result');
        if (visibleCount === 0 && items.length > 0) {
            if (!noResultMsg) {
                let msg = document.createElement('div');
                msg.id = 'no-filter-result';
                msg.className = 'text-center py-12 text-gray-400 italic font-medium';
                msg.innerText = 'Không có đánh giá nào ở mức sao này.';
                document.getElementById('review-list-container').appendChild(msg);
            } else {
                noResultMsg.style.display = 'block';
            }
        } else {
            if (noResultMsg) noResultMsg.style.display = 'none';
        }
    }
</script>
@endsection