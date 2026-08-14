@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden py-12 lg:py-20 bg-gradient-to-b from-cream/50 via-white to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left Text Content -->
                <div class="space-y-6 text-center lg:text-left">
                    @if(!empty($heroBanner->badge))
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-coral/10 border border-coral/20 text-coral font-semibold text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            {{ $heroBanner->badge }}
                        </div>
                    @endif

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-extrabold text-espresso tracking-tight leading-tight">
                        {!! $heroBanner->title ?? 'Thư giãn từng nét <br class="hidden sm:inline"><span class="text-transparent bg-clip-text bg-gradient-to-r from-coral via-amber-600 to-espresso">Giao hòa cảm xúc</span>' !!}
                    </h1>

                    <p class="text-base sm:text-lg text-espresso/70 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                        {{ $heroBanner->description ?? 'Nơi dừng chân lý tưởng cho những tách cà phê nguyên chất đậm đà và ly trà sữa ngọt ngào. Gọi món ngay để nhận ưu đãi giao tận nơi!' }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ $heroBanner->button_link ?? route('product.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-coral text-white font-bold text-base shadow-lg shadow-coral/30 hover:bg-[#d5523b] hover:shadow-xl hover:-translate-y-0.5 transition-all text-center">
                            {{ $heroBanner->button_text ?? 'Khám phá Menu ngay' }}
                        </a>
                        @if(!empty($heroBanner->button_secondary_text))
                            <a href="{{ $heroBanner->button_secondary_link ?? '#best-sellers' }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white border border-coral/20 text-espresso font-bold text-base hover:bg-cream/50 transition-all text-center shadow-sm">
                                {{ $heroBanner->button_secondary_text }}
                            </a>
                        @endif
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8 border-t border-espresso/10 max-w-md mx-auto lg:mx-0">
                        <div>
                            <span class="block text-2xl font-black text-coral font-serif">100%</span>
                            <span class="text-xs text-espresso/60 font-medium">Hạt Cà Phê Moka</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-coral font-serif">15+</span>
                            <span class="text-xs text-espresso/60 font-medium">Món Signature</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-coral font-serif">4.9★</span>
                            <span class="text-xs text-espresso/60 font-medium">Đánh giá khách hàng</span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual Element (Sản Phẩm Nổi Bật Banner) -->
                <div class="relative flex justify-center">
                    <div class="absolute -top-10 -left-10 w-72 h-72 bg-coral/15 rounded-full blur-3xl -z-10 animate-pulse"></div>
                    <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-amber-200/30 rounded-full blur-3xl -z-10"></div>

                    @if(isset($featuredProduct) && $featuredProduct)
                        <div class="relative bg-white/90 backdrop-blur-md p-5 rounded-[40px] shadow-2xl max-w-md w-full border border-coral/15 group">
                            <a href="{{ route('product.show', $featuredProduct->slug) }}" class="block overflow-hidden rounded-[32px] h-80 bg-cream">
                                <img src="{{ !empty($featuredProduct->image_url) ? (\Illuminate\Support\Str::startsWith($featuredProduct->image_url, ['http://', 'https://']) ? $featuredProduct->image_url : asset($featuredProduct->image_url)) : 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=800&auto=format&fit=crop' }}"
                                     alt="{{ $featuredProduct->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            {{-- Floating Product Card - Click vào thẻ để tự động thêm vào giỏ hàng --}}
                            <div onclick="quickAddToCart({{ $featuredProduct->product_id }})" 
                                 class="absolute -bottom-6 -left-6 bg-white/95 backdrop-blur-md p-4 rounded-3xl shadow-xl flex items-center gap-3 border border-espresso/10 cursor-pointer hover:scale-105 transition-all group/card"
                                 title="Bấm để thêm {{ $featuredProduct->name }} vào giỏ hàng">
                                <div class="w-12 h-12 rounded-2xl bg-espresso text-cream flex items-center justify-center shrink-0 shadow-sm group-hover/card:bg-coral transition-colors">
                                    <svg class="w-6 h-6 text-coral group-hover/card:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-espresso text-sm group-hover/card:text-coral transition-colors">{{ $featuredProduct->name }}</h4>
                                    <p class="text-xs text-coral font-black">{{ number_format($featuredProduct->price, 0, ',', '.') }}đ</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="relative bg-white/90 backdrop-blur-md p-5 rounded-[40px] shadow-2xl max-w-md w-full border border-coral/15">
                            <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=800&auto=format&fit=crop"
                                 alt="Featured Drink"
                                 class="w-full h-80 object-cover rounded-[32px] shadow-md">
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    <!-- Quick Highlights Bar -->
    <div class="bg-espresso text-white py-6 shadow-md">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-white/10">
            <div class="px-4">
                <h4 class="font-serif font-bold text-2xl text-coral">50+</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Cửa hàng toàn quốc</p>
            </div>
            <div class="px-4">
                <h4 class="font-serif font-bold text-2xl text-coral">100%</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Cà phê nguyên chất</p>
            </div>
            <div class="px-4">
                <h4 class="font-serif font-bold text-2xl text-coral">30p</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Giao hàng tốc hành</p>
            </div>
            <div class="px-4">
                <h4 class="font-serif font-bold text-2xl text-coral">24/7</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Hỗ trợ khách hàng</p>
            </div>
        </div>
    </div>

    <!-- Best Sellers Section (Cập nhật dữ liệu liên tục từ số lượng đơn bán) -->
    <section id="best-sellers" class="py-16 bg-[#FAF7F2]/60 border-y border-espresso/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-coral font-bold text-xs uppercase tracking-widest bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Cập nhật lượt mua liên tục</span>
                <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Món Bán Chạy Trong Tuần</h2>
                <p class="text-espresso/60 text-sm mt-1">Những món được khách hàng yêu thích và lựa chọn nhiều nhất trên toàn hệ thống.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-3xl p-4 border border-espresso/5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 h-48 bg-cream">
                                <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                    <img src="{{ !empty($product->image_url) ? (\Illuminate\Support\Str::startsWith($product->image_url, ['http://', 'https://']) ? $product->image_url : asset($product->image_url)) : 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?q=80&w=600&auto=format&fit=crop' }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>
                                <span class="absolute top-3 right-3 bg-coral text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md">
                                    🔥 {{ $product->total_sold > 0 ? 'Đã bán ' . number_format($product->total_sold) : 'Best Seller' }}
                                </span>
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}" class="font-bold text-espresso hover:text-coral text-lg transition-colors line-clamp-1">
                                {{ $product->name }}
                            </a>
                            <p class="text-xs text-espresso/60 mt-1 line-clamp-2">{{ $product->description ?? 'Hương vị thơm ngon khó cưỡng, chuẩn vị cà phê nhà làm.' }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-espresso/5">
                            <span class="text-lg font-black text-coral">{{ number_format($product->price, 0, ',', '.') }}đ</span>

                            <button onclick="quickAddToCart({{ $product->product_id }})"
                                    class="p-2.5 rounded-xl bg-coral/10 text-coral hover:bg-coral hover:text-white transition-all shadow-sm"
                                    title="Thêm nhanh vào giỏ hàng">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8 text-espresso/60 text-sm">Chưa có sản phẩm nào trong danh sách bán chạy.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Categories / Menu Section ("Thế Giới Hương Vị") -->
    <section id="menu" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs uppercase tracking-widest font-bold text-coral bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Thực đơn đặc sắc</span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Thế Giới Hương Vị</h2>
            <p class="text-espresso/60 text-sm sm:text-base mt-2">Mỗi ly nước được pha chế tỉ mỉ từ nguyên liệu tươi ngon nhất.</p>
        </div>

        {{-- Lưới Grid Danh mục bất đối xứng --}}
        <div class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 h-auto md:h-[600px]">

            @if(isset($categories[0]))
            <a href="{{ route('product.index', ['category' => $categories[0]->category_id]) }}" class="block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="{{ $categories[0]->image ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=800&q=80' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 via-espresso/30 to-transparent"></div>
                <div class="absolute bottom-8 left-8 z-10">
                    <h3 class="font-serif font-bold text-4xl text-white mb-2">{{ $categories[0]->name }}</h3>
                    <span class="inline-block bg-white/20 backdrop-blur-md text-white px-5 py-2 rounded-full text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                        Khám phá ngay &rarr;
                    </span>
                </div>
            </a>
            @endif

            @if(isset($categories[1]))
            <a href="{{ route('product.index', ['category' => $categories[1]->category_id]) }}" class="block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="{{ $categories[1]->image ?? 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=600&q=80' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 to-transparent"></div>
                <div class="absolute bottom-8 left-8 z-10">
                    <h3 class="font-serif font-bold text-2xl text-white mb-2">{{ $categories[1]->name }}</h3>
                </div>
            </a>
            @endif

            @if(isset($categories[2]))
            <a href="{{ route('product.index', ['category' => $categories[2]->category_id]) }}" class="block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="{{ $categories[2]->image ?? 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 to-transparent"></div>
                <div class="absolute bottom-8 left-8 z-10">
                    <h3 class="font-serif font-bold text-2xl text-white mb-2">{{ $categories[2]->name }}</h3>
                </div>
            </a>
            @endif

            @if(isset($categories[3]))
            <a href="{{ route('product.index', ['category' => $categories[3]->category_id]) }}" class="block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full bg-cream/80 flex items-center justify-between p-8">
                <div class="z-10 max-w-sm">
                    <h3 class="font-serif font-bold text-3xl text-espresso mb-3">{{ $categories[3]->name }}</h3>
                    <p class="text-espresso/70 mb-4">Khám phá hương vị đặc trưng được chọn lọc tỉ mỉ mỗi ngày.</p>
                    <span class="inline-block text-coral font-bold underline underline-offset-4 group-hover:text-espresso transition-colors">Khám phá ngay</span>
                </div>
                <img src="{{ $categories[3]->image ?? 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=600&q=80' }}" class="absolute right-0 top-0 h-full w-1/2 object-cover object-left mask-image-gradient transition-transform duration-700 group-hover:scale-105" />
            </a>
            @endif

        </div>

        <div class="text-center mt-12">
            <a href="{{ route('product.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-white border border-coral/30 text-coral font-bold hover:bg-coral hover:text-white transition-all shadow-sm">
                Xem toàn bộ Thực đơn <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>

    <!-- Combo Section ("Gói Combo Tiết Kiệm") -->
    <section id="combo-section" class="py-16 bg-gradient-to-b from-cream/40 via-orange-50/30 to-white border-b border-espresso/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-coral font-bold text-xs uppercase tracking-widest bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Tiết Kiệm Độc Quyền</span>
                    <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Gói Combo Tiết Kiệm - Uống Là Mê!</h2>
                    <p class="text-espresso/70 text-sm sm:text-base mt-2">Chọn ngay combo đồ uống & bánh ngọt yêu thích với giá ưu đãi cực sốc!</p>
                </div>
                <a href="{{ route('combo.index') }}" class="inline-flex items-center gap-2 bg-espresso text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-coral transition-colors shrink-0">
                    <span>Xem tất cả Combo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($combos as $combo)
                    <div class="bg-white rounded-3xl p-5 border border-coral/20 shadow-md hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                        @if($combo->original_price > $combo->price)
                            @php
                                $percent = round((($combo->original_price - $combo->price) / $combo->original_price) * 100);
                            @endphp
                            <div class="absolute top-4 left-4 z-10 bg-gradient-to-r from-red-500 to-coral text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-1.348l-8 8a1 1 0 00-.246.979 1 1 0 00.706.707l8 8a1 1 0 001.45-1.348L5.592 11h11.816a1 1 0 00.992-1.127l-.4-3.2A1 1 0 0017 6H8.223l4.172-3.447z" clip-rule="evenodd"/></svg>
                                GIẢM {{ $percent }}%
                            </div>
                        @endif

                        <div>
                            <a href="{{ route('combo.show', $combo->combo_id) }}" class="block relative overflow-hidden rounded-2xl mb-4 h-52 bg-cream">
                                <img src="{{ !empty($combo->image_url) ? (\Illuminate\Support\Str::startsWith($combo->image_url, ['http://', 'https://']) ? $combo->image_url : asset($combo->image_url)) : 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop' }}"
                                     alt="{{ $combo->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            <a href="{{ route('combo.show', $combo->combo_id) }}" class="block font-bold text-espresso text-xl hover:text-coral transition-colors">
                                {{ $combo->name }}
                            </a>
                            <p class="text-xs text-espresso/60 mt-1 line-clamp-2 min-h-[32px]">{{ $combo->description ?? 'Gói kết hợp ưu đãi tiết kiệm dành riêng cho bạn.' }}</p>

                            {{-- Món bao gồm trong Combo --}}
                            <div class="mt-4 pt-3 border-t border-espresso/10">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso/50 block mb-2">Các món có trong Combo:</span>
                                <ul class="space-y-1.5 max-h-36 overflow-y-auto pr-1 custom-scrollbar">
                                    @foreach($combo->products as $prod)
                                        <li class="flex items-center justify-between text-xs text-espresso bg-cream/50 px-3 py-1.5 rounded-xl border border-coral/10">
                                            <div class="flex items-center gap-2 font-medium min-w-0">
                                                <svg class="w-3.5 h-3.5 text-coral shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="line-clamp-1">{{ $prod->name }}</span>
                                            </div>
                                            <span class="font-bold text-coral bg-white px-2 py-0.5 rounded-md border border-coral/20 shrink-0">x{{ $prod->pivot->quantity }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-5 mt-4 border-t border-espresso/10">
                            <div>
                                @if($combo->original_price > $combo->price)
                                    <span class="text-xs text-espresso/40 line-through block font-medium">
                                        {{ number_format($combo->original_price, 0, ',', '.') }}đ
                                    </span>
                                @endif
                                <span class="text-xl font-black text-coral">
                                    {{ number_format($combo->price, 0, ',', '.') }}đ
                                </span>
                            </div>

                            <button onclick="addComboToCart({{ $combo->combo_id }})"
                                    class="px-5 py-3 rounded-full bg-coral text-white font-bold text-xs hover:bg-[#d5523b] shadow-lg shadow-coral/30 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <span>Đặt Combo Ngay</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 bg-white rounded-3xl border border-espresso/10">
                        <p class="text-espresso/60 text-sm">Chưa có gói Combo nào được cập nhật. Quản trị viên vui lòng tạo combo trong trang Admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <script>
        function addComboToCart(comboId) {
            fetch("{{ route('cart.addCombo') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    combo_id: comboId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartBadges = document.querySelectorAll('.cart-count-badge, #cart-count');
                    cartBadges.forEach(b => b.textContent = data.cart_count);
                    alert("🎉 " + data.message);
                } else {
                    alert("⚠️ " + (data.message || "Có lỗi xảy ra khi thêm combo vào giỏ hàng."));
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("⚠️ Không thể kết nối với máy chủ!");
            });
        }
    </script>

    <!-- Storytelling / About Section -->
    <section id="about" class="py-20 relative overflow-hidden bg-[#FAF7F2]/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-cream/90 via-white to-amber-50/70 rounded-[40px] p-8 sm:p-12 lg:p-16 border border-espresso/5 shadow-sm relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs uppercase tracking-widest font-bold text-coral bg-white px-3.5 py-1.5 rounded-full border border-coral/20 shadow-xs">Câu chuyện của chúng tôi</span>
                        <h2 class="text-3xl sm:text-4xl font-serif font-extrabold text-espresso tracking-tight">Không Gian Chill, Tách Trà Đượm</h2>
                        <p class="text-espresso/70 leading-relaxed text-sm sm:text-base">
                            Tại **Chill Chill**, mỗi ly đồ uống không chỉ là sự pha trộn của nguyên liệu chất lượng cao, mà còn gửi gắm tình yêu và niềm đam mê tạo nên những giây phút thư thái nhất cho bạn.
                        </p>
                        <p class="text-espresso/70 leading-relaxed text-sm sm:text-base">
                            Từ những hạt cà phê rang xay thượng hạng cho đến các dòng trà trái cây thanh mát, chúng tôi tin rằng mỗi tách đồ uống đều sẽ mang lại cho bạn cảm giác tuyệt vời.
                        </p>

                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <div class="flex items-center gap-3 bg-white/80 p-3.5 rounded-2xl border border-espresso/5 shadow-xs">
                                <div class="w-9 h-9 rounded-xl bg-coral/10 text-coral flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs font-bold text-espresso">100% Nguyên liệu tươi</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/80 p-3.5 rounded-2xl border border-espresso/5 shadow-xs">
                                <div class="w-9 h-9 rounded-xl bg-coral/10 text-coral flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-espresso">Giao hàng siêu tốc</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=600&q=80" alt="Café Interior" class="rounded-3xl shadow-md w-full h-48 sm:h-64 object-cover">
                                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=80" alt="Espresso Pour" class="rounded-3xl shadow-md w-full h-36 sm:h-48 object-cover">
                            </div>
                            <div class="space-y-4 pt-6">
                                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80" alt="Pastries" class="rounded-3xl shadow-md w-full h-36 sm:h-48 object-cover">
                                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=600&q=80" alt="Latte Art" class="rounded-3xl shadow-md w-full h-48 sm:h-64 object-cover">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Customer Reviews Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-bold text-coral bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Khách hàng nói gì</span>
                <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Trải Nghiệm Thực Tế</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if(isset($reviews) && count($reviews) > 0)
                    @foreach($reviews as $review)
                        <div class="bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
                            <div>
                                <div class="flex text-amber-500 gap-1 mb-4 text-sm">
                                    @for($i = 0; $i < ($review->rating ?? 5); $i++) ★ @endfor
                                </div>
                                <p class="text-sm text-espresso/80 leading-relaxed italic">
                                    "{{ $review->content }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-espresso/10">
                                <img src="{{ $review->avatar ?? 'https://i.pravatar.cc/150' }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-espresso">{{ $review->name }}</h4>
                                    <span class="text-xs text-espresso/50">Khách hàng thân thiết</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
                        <div>
                            <div class="flex text-amber-500 gap-1 mb-4 text-sm">
                                ★★★★★
                            </div>
                            <p class="text-sm text-espresso/80 leading-relaxed italic">
                                "Chill Chill đã trở thành điểm đến quen thuộc của mình mỗi cuối tuần. Trà sữa oolong lài chuẩn vị—thơm đậm trà, ngọt vừa phải!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-espresso/10">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="text-sm font-bold text-espresso">Nguyễn Phương Thảo</h4>
                                <span class="text-xs text-espresso/50">Khách hàng quen thuộc</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
                        <div>
                            <div class="flex text-amber-500 gap-1 mb-4 text-sm">
                                ★★★★★
                            </div>
                            <p class="text-sm text-espresso/80 leading-relaxed italic">
                                "Không gian ấm cúng, tối giản đúng chất chill. Bánh ngọt ăn cùng cà phê muối ngon xuất sắc."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-espresso/10">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="text-sm font-bold text-espresso">Trần Minh Anh</h4>
                                <span class="text-xs text-espresso/50">Khách hàng thành viên</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
                        <div>
                            <div class="flex text-amber-500 gap-1 mb-4 text-sm">
                                ★★★★★
                            </div>
                            <p class="text-sm text-espresso/80 leading-relaxed italic">
                                "Đặt hàng online giao siêu nhanh, đóng gói cẩn thận. Ly nước đến nơi vẫn lạnh sâu và giữ tròn hương vị."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-espresso/10">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="text-sm font-bold text-espresso">Phạm Hoàng Yến</h4>
                                <span class="text-xs text-espresso/50">Food Blogger</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>

    <!-- Promotion Voucher Banner -->
    @if(isset($promoBanner) && $promoBanner)
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="rounded-[32px] overflow-hidden shadow-2xl relative bg-espresso">
                <img src="{{ !empty($promoBanner->image_url) ? (\Illuminate\Support\Str::startsWith($promoBanner->image_url, ['http://', 'https://']) ? $promoBanner->image_url : asset($promoBanner->image_url)) : 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop' }}" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay" />
                <div class="relative z-10 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-white text-center md:text-left">
                        @if($promoBanner->badge)
                            <span class="inline-block bg-coral text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">{{ $promoBanner->badge }}</span>
                        @endif
                        <h3 class="font-serif font-bold text-3xl md:text-5xl mb-3">{{ $promoBanner->title }}</h3>
                        <p class="font-sans text-white/80 text-base md:text-lg">{!! nl2br(e($promoBanner->description)) !!}</p>
                    </div>
                    <a href="{{ $promoBanner->button_link ?? route('cart.index') }}" class="shrink-0 bg-coral text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-white hover:text-espresso transition-colors duration-300 shadow-lg">{{ $promoBanner->button_text ?? 'Đổi mã ngay' }}</a>
                </div>
            </div>
        </section>
    @else
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="rounded-[32px] overflow-hidden shadow-2xl relative bg-espresso">
                <img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay" />
                <div class="relative z-10 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-white text-center md:text-left">
                        <span class="inline-block bg-coral text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">ƯU ĐÃI THÁNG 8</span>
                        <h3 class="font-serif font-bold text-3xl md:text-5xl mb-3">Giảm 20% toàn bộ đơn hàng!</h3>
                        <p class="font-sans text-white/80 text-base md:text-lg">Nhập mã <strong class="text-coral bg-white px-2.5 py-1 rounded-md mx-1 shadow-xs">CHILL20</strong> khi thanh toán online.</p>
                    </div>
                    <a href="{{ route('cart.index') }}" class="shrink-0 bg-coral text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-white hover:text-espresso transition-colors duration-300 shadow-lg">Đổi mã ngay</a>
                </div>
            </div>
        </section>
    @endif

    <!-- Store Locations Section -->
    <section id="store" class="py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto bg-[#FAF7F2] rounded-[40px] my-12 border border-espresso/5">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-coral font-bold tracking-widest uppercase text-sm mb-2 block">Hệ thống trạm dừng</span>
                <h2 class="font-serif font-bold text-espresso text-3xl sm:text-4xl mb-6">Ghé Thăm Cửa Hàng Gần Bạn</h2>
                <p class="text-espresso/70 mb-8 text-base leading-relaxed">Hơn 50 cửa hàng trên toàn quốc với không gian mở, xanh mát, là nơi lý tưởng để làm việc hoặc tán gẫu cùng bạn bè.</p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3 text-espresso">
                        <svg class="w-6 h-6 text-coral shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><strong>Quận 1:</strong> 123 Đường Cà Phê, Phường Bến Nghé</span>
                    </li>
                    <li class="flex items-start gap-3 text-espresso">
                        <svg class="w-6 h-6 text-coral shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><strong>Quận 3:</strong> 45 Trà Xanh, Phường Võ Thị Sáu</span>
                    </li>
                </ul>
                <a href="{{ route('product.index') }}" class="inline-block bg-espresso text-white font-medium px-8 py-3.5 rounded-full hover:bg-coral transition-colors">Khám phá Thực đơn</a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl w-full h-[400px] border border-espresso/10">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4946681007846!2d106.6983053!3d10.7733743!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f40a3b49e59%3A0xa1bd14e483a602db!2sCh%E1%BB%A3%20B%E1%BA%BFn%20Th%C3%A0nh!5e0!3m2!1svi!2s!4v1709210000000!5m2!1svi!2s" width="100%" height="100%" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
@endsection