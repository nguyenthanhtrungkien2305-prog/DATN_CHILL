@extends('layouts.app')

@section('content')
    <!-- Full-Page Hero Banner Section (Tràn đỉnh màn hình, hiển thị trọn vẹn) -->
    <section class="relative w-full overflow-hidden bg-espresso pt-0">
        <div class="relative w-full min-h-[360px] sm:min-h-[520px] lg:min-h-[640px] flex items-center justify-center">
            
            {{-- Full-Width Background Image (Hiển thị đầy đủ không bị xén) --}}
            <img id="hero-banner-bg" 
                 src="{{ format_image_url($heroBanner->image_url ?? '/images/banner1.png', '/images/banner1.png') }}?v=1812" 
                 alt="{{ $heroBanner->title ?? 'Chill Chill Hero Banner' }}" 
                 class="w-full h-auto max-h-[85vh] sm:max-h-screen object-contain sm:object-cover object-center transition-opacity duration-500 ease-in-out" />

            {{-- Nút Chuyển Slide Banner Căn Giữa --}}
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2.5 bg-black/40 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/20 shadow-lg">
                <button onclick="changeHeroBanner(0)" class="h-3 w-8 rounded-full bg-coral transition-all duration-300 hero-banner-dot" title="Banner 1"></button>
                <button onclick="changeHeroBanner(1)" class="h-3 w-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300 hero-banner-dot" title="Banner 2"></button>
                <button onclick="changeHeroBanner(2)" class="h-3 w-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300 hero-banner-dot" title="Banner 3"></button>
            </div>
        </div>
    </section>

    <!-- Quick Highlights Bar -->
    <div class="bg-espresso text-white py-6 shadow-md reveal-zoom">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-white/10">
            <div class="px-4 hover-rotate">
                <h4 class="font-serif font-bold text-2xl text-coral">50+</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Cửa hàng toàn quốc</p>
            </div>
            <div class="px-4 hover-rotate">
                <h4 class="font-serif font-bold text-2xl text-coral">100%</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Cà phê nguyên chất</p>
            </div>
            <div class="px-4 hover-rotate">
                <h4 class="font-serif font-bold text-2xl text-coral">30p</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Giao hàng tốc hành</p>
            </div>
            <div class="px-4 hover-rotate">
                <h4 class="font-serif font-bold text-2xl text-coral">24/7</h4>
                <p class="text-xs font-light text-white/80 mt-0.5">Hỗ trợ khách hàng</p>
            </div>
        </div>
    </div>

    <!-- Best Sellers Section (Cập nhật dữ liệu liên tục từ số lượng đơn bán) -->
    <section id="best-sellers" class="py-16 bg-[#FAF7F2]/60 border-y border-espresso/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal-up">
                <span class="text-coral font-bold text-xs uppercase tracking-widest bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20 animate-pulse-glow">Cập nhật lượt mua liên tục</span>
                <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Nước Uống Bán Chạy Trong Tuần</h2>
                <p class="text-espresso/60 text-sm mt-1">Những món nước giải nhiệt & cà phê được yêu thích nhất trên toàn hệ thống.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="reveal-up hover-lift hover-glow bg-white rounded-3xl p-4 border border-espresso/5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            {{-- Khung chứa ảnh cao hơn (h-64 sm:h-72) để hiển thị đầy đủ hình ảnh --}}
                            <div class="relative overflow-hidden rounded-2xl mb-4 h-64 sm:h-72 bg-cream flex items-center justify-center">
                                <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                    <img src="{{ format_image_url($product->image_url, '/images/logo1.jpg', $product->name) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         onerror="this.onerror=null; this.src='/images/logo1.jpg';" />
                                </a>
                                <span class="absolute top-3 right-3 bg-coral text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md animate-bounce-slow">
                                    🔥 {{ $product->total_sold > 0 ? 'Đã bán ' . number_format($product->total_sold) : 'Best Seller' }}
                                </span>
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}" class="font-bold text-espresso hover:text-coral text-lg transition-colors line-clamp-1">
                                {{ $product->name }}
                            </a>
                            <p class="text-xs text-espresso/60 mt-1 line-clamp-2">{{ $product->description ?? 'Hương vị thơm ngon khó cưỡng, chuẩn vị cà phê nhà làm.' }}</p>
                        </div>

                        {{-- Giá & Nút Mua ngay --}}
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-espresso/5 gap-2">
                            <span class="text-lg font-black text-coral">{{ number_format($product->price, 0, ',', '.') }}đ</span>

                            <div class="flex items-center gap-1.5">
                                {{-- Nút Thêm vào giỏ --}}
                                <button onclick="quickAddToCart({{ $product->product_id }})"
                                        class="p-2 rounded-full bg-coral/10 text-coral hover:bg-coral hover:text-white btn-pulse transition-all shadow-2xs"
                                        title="Thêm nhanh vào giỏ hàng">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>

                                {{-- Nút Mua Ngay --}}
                                <button onclick="quickAddToCart({{ $product->product_id }}); window.location.href='{{ route('cart.index') }}';"
                                        class="px-3.5 py-1.5 rounded-full bg-coral text-white font-bold text-xs uppercase tracking-wider hover:bg-[#d5523b] btn-pulse shadow-md shadow-coral/25 transition-all whitespace-nowrap">
                                    Mua ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8 text-espresso/60 text-sm">Chưa có sản phẩm nào trong danh sách bán chạy.</div>
                @endforelse
            </div>

            {{-- Nút xem tất cả ở giữa dẫn đến trang thực đơn --}}
            <div class="text-center mt-10 reveal-up">
                <a href="{{ route('product.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-coral text-white font-bold text-sm uppercase tracking-wider hover:bg-espresso btn-pulse shadow-md hover:shadow-xl transition-all duration-300">
                    <span>Xem tất cả</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Best Selling Cakes Section ("Bánh Ngọt Bán Chạy") -->
    <section id="best-cakes" class="py-16 bg-white border-b border-espresso/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal-up">
                <span class="text-coral font-bold text-xs uppercase tracking-widest bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Bánh Tươi Mỗi Ngày</span>
                <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Bánh Ngọt Bán Chạy</h2>
                <p class="text-espresso/60 text-sm mt-1">Những món bánh thơm ngon, kết hợp hoàn hảo cùng tách cà phê hay ly trà thơm đậm.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($cakeProducts as $cake)
                    <div class="reveal-up hover-lift hover-glow bg-[#FAF7F2]/40 rounded-3xl p-4 border border-espresso/5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            {{-- Khung chứa ảnh --}}
                            <div class="relative overflow-hidden rounded-2xl mb-4 h-64 sm:h-72 bg-cream flex items-center justify-center">
                                <a href="{{ route('product.show', $cake->slug) }}" class="block w-full h-full">
                                    <img src="{{ format_image_url($cake->image_url, '/images/banhngot.png', $cake->name) }}"
                                         alt="{{ $cake->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         onerror="this.onerror=null; this.src='/images/banhngot.png';" />
                                </a>
                                <span class="absolute top-3 right-3 bg-coral text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md">
                                    🍰 {{ $cake->total_sold > 0 ? 'Đã bán ' . number_format($cake->total_sold) : 'Bánh Bán Chạy' }}
                                </span>
                            </div>

                            <a href="{{ route('product.show', $cake->slug) }}" class="font-bold text-espresso hover:text-coral text-lg transition-colors line-clamp-1">
                                {{ $cake->name }}
                            </a>
                            <p class="text-xs text-espresso/60 mt-1 line-clamp-2">{{ $cake->description ?? 'Hương vị bánh tươi mềm mịn, vị ngọt thanh dịu kích thích vị giác.' }}</p>
                        </div>

                        {{-- Giá & Nút Mua ngay --}}
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-espresso/5 gap-2">
                            <span class="text-lg font-black text-coral">{{ number_format($cake->price, 0, ',', '.') }}đ</span>

                            <div class="flex items-center gap-1.5">
                                {{-- Nút Thêm vào giỏ --}}
                                <button onclick="quickAddToCart({{ $cake->product_id }})"
                                        class="p-2 rounded-full bg-coral/10 text-coral hover:bg-coral hover:text-white btn-pulse transition-all shadow-2xs"
                                        title="Thêm nhanh vào giỏ hàng">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>

                                {{-- Nút Mua Ngay --}}
                                <button onclick="quickAddToCart({{ $cake->product_id }}); window.location.href='{{ route('cart.index') }}';"
                                        class="px-3.5 py-1.5 rounded-full bg-coral text-white font-bold text-xs uppercase tracking-wider hover:bg-[#d5523b] btn-pulse shadow-md shadow-coral/25 transition-all whitespace-nowrap">
                                    Mua ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8 text-espresso/60 text-sm">Chưa có bánh nào trong danh sách.</div>
                @endforelse
            </div>

            {{-- Nút xem tất cả ở giữa dẫn đến trang thực đơn --}}
            <div class="text-center mt-10 reveal-up">
                <a href="{{ route('product.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-coral text-white font-bold text-sm uppercase tracking-wider hover:bg-espresso btn-pulse shadow-md hover:shadow-xl transition-all duration-300">
                    <span>Xem tất cả</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Categories / Menu Section ("Thế Giới Hương Vị") -->
    <section id="menu" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14 reveal-zoom">
            <span class="text-xs uppercase tracking-widest font-bold text-coral bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Thực đơn đặc sắc</span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Thế Giới Hương Vị</h2>
            <p class="text-espresso/60 text-sm sm:text-base mt-2">Mỗi ly nước được pha chế tỉ mỉ từ nguyên liệu tươi ngon nhất.</p>
        </div>

        {{-- Lưới Grid Danh mục bất đối xứng --}}
        <div class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 h-auto md:h-[600px]">

            @if(isset($categories[0]))
            <a href="{{ route('product.index', ['category' => $categories[0]->category_id]) }}" class="reveal-left hover-lift block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="/images/caphe.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute bottom-8 left-8 z-10">
                    <span class="inline-block bg-espresso/80 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-md">
                        Khám phá ngay &rarr;
                    </span>
                </div>
            </a>
            @endif

            @if(isset($categories[1]))
            <a href="{{ route('product.index', ['category' => $categories[1]->category_id]) }}" class="reveal-right hover-lift block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="/images/tra.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute bottom-8 left-8 z-10">
                    <span class="inline-block bg-espresso/80 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-md">
                        Khám phá ngay &rarr;
                    </span>
                </div>
            </a>
            @endif

            @if(isset($categories[2]))
            <a href="{{ route('product.index', ['category' => $categories[1]->category_id]) }}" class="reveal-left hover-lift block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="/images/daxay.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute bottom-8 left-8 z-10">
                    <span class="inline-block bg-espresso/80 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-md">
                        Khám phá ngay &rarr;
                    </span>
                </div>
            </a>
            @endif

            @if(isset($categories[0]))
            <a href="{{ route('product.index', ['category' => $categories[0]->category_id]) }}" class="reveal-right hover-lift block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
                <img src="/images/banhngot.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute bottom-8 left-8 z-10">
                    <span class="inline-block bg-espresso/80 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-md">
                        Khám phá ngay &rarr;
                    </span>
                </div>
            </a>
            @endif

        </div>

        <div class="text-center mt-12 reveal-up">
            <a href="{{ route('product.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-white border border-coral/30 text-coral font-bold hover:bg-coral hover:text-white btn-pulse transition-all shadow-sm">
                Xem toàn bộ Thực đơn <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>

    <!-- Combo Section ("Gói Combo Tiết Kiệm") -->
    <section id="combo-section" class="py-16 bg-gradient-to-b from-cream/40 via-orange-50/30 to-white border-b border-espresso/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 reveal-up">
                <div>
                    <span class="text-coral font-bold text-xs uppercase tracking-widest bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20 animate-pulse-glow">Tiết Kiệm Độc Quyền</span>
                    <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Gói Combo Tiết Kiệm - Uống Là Mê!</h2>
                    <p class="text-espresso/70 text-sm sm:text-base mt-2">Chọn ngay combo đồ uống & bánh ngọt yêu thích với giá ưu đãi cực sốc!</p>
                </div>
                <a href="{{ route('combo.index') }}" class="inline-flex items-center gap-2 bg-espresso text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-coral btn-pulse transition-colors shrink-0">
                    <span>Xem tất cả Combo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($combos as $combo)
                    <div class="reveal-up hover-lift hover-glow bg-white rounded-3xl p-5 border border-coral/20 shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                        @if($combo->original_price > $combo->price)
                            @php
                                $percent = round((($combo->original_price - $combo->price) / $combo->original_price) * 100);
                            @endphp
                            <div class="absolute top-4 left-4 z-10 bg-gradient-to-r from-red-500 to-coral text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1 animate-bounce-slow">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-1.348l-8 8a1 1 0 00-.246.979 1 1 0 00.706.707l8 8a1 1 0 001.45-1.348L5.592 11h11.816a1 1 0 00.992-1.127l-.4-3.2A1 1 0 0017 6H8.223l4.172-3.447z" clip-rule="evenodd"/></svg>
                                GIẢM {{ $percent }}%
                            </div>
                        @endif

                        <div>
                            <a href="{{ route('combo.show', $combo->combo_id) }}" class="block relative overflow-hidden rounded-2xl mb-4 h-52 bg-cream">
                                <img src="{{ asset($combo->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop') }}"
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
                                    class="px-5 py-3 rounded-full bg-coral text-white font-bold text-xs hover:bg-[#d5523b] btn-pulse shadow-lg shadow-coral/30 hover:shadow-xl transition-all flex items-center gap-1.5">
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

            {{-- Nút Xem tất cả Gói Combo ở giữa bên dưới --}}
            <div class="text-center mt-12 reveal-up">
                <a href="{{ route('combo.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-coral text-white font-bold text-sm uppercase tracking-wider hover:bg-espresso btn-pulse shadow-md hover:shadow-xl transition-all duration-300">
                    <span>Xem tất cả Gói Combo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
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
            <div class="bg-gradient-to-r from-cream/90 via-white to-amber-50/70 rounded-[40px] p-8 sm:p-12 lg:p-16 border border-espresso/5 shadow-sm relative reveal-zoom">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                    <div class="lg:col-span-6 space-y-6 reveal-left">
                        <span class="text-xs uppercase tracking-widest font-bold text-coral bg-white px-3.5 py-1.5 rounded-full border border-coral/20 shadow-xs">Câu chuyện của chúng tôi</span>
                        <h2 class="text-3xl sm:text-4xl font-serif font-extrabold text-espresso tracking-tight">Không Gian Chill <br>Tách Cà Phê Đượm</h2>
                        <p class="text-espresso/70 leading-relaxed text-sm sm:text-base">
                            Tại **Chill Chill**, mỗi ly đồ uống không chỉ là sự pha trộn của nguyên liệu chất lượng cao, mà còn gửi gắm tình yêu và niềm đam mê tạo nên những giây phút thư thái nhất cho bạn.
                        </p>
                        <p class="text-espresso/70 leading-relaxed text-sm sm:text-base">
                            Từ những hạt cà phê rang xay thượng hạng cho đến các dòng trà trái cây thanh mát, chúng tôi tin rằng mỗi tách đồ uống đều sẽ mang lại cho bạn cảm giác tuyệt vời.
                        </p>

                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <div class="flex items-center gap-3 bg-white/80 p-3.5 rounded-2xl border border-espresso/5 shadow-xs hover-rotate">
                                <div class="w-9 h-9 rounded-xl bg-coral/10 text-coral flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs font-bold text-espresso">100% Nguyên liệu tươi</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/80 p-3.5 rounded-2xl border border-espresso/5 shadow-xs hover-rotate">
                                <div class="w-9 h-9 rounded-xl bg-coral/10 text-coral flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-espresso">Giao hàng siêu tốc</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-6 reveal-right">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <img src="/images/anhcauchuyen1.png" alt="Café Interior" class="rounded-3xl shadow-md w-full h-48 sm:h-64 object-cover hover-rotate transition-all duration-500">
                                <img src="/images/anhcauchuyen2.png" alt="Espresso Pour" class="rounded-3xl shadow-md w-full h-36 sm:h-48 object-cover hover-rotate transition-all duration-500">
                            </div>
                            <div class="space-y-4 pt-6">
                                <img src="/images/anhcauchuyen3.png" alt="Espresso Pour" class="rounded-3xl shadow-md w-full h-36 sm:h-48 object-cover hover-rotate transition-all duration-500">
                                <img src="/images/anhcauchuyen4.png" alt="Espresso Pour" class="rounded-3xl shadow-md w-full h-36 sm:h-48 object-cover hover-rotate transition-all duration-500">
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

            <div class="text-center max-w-xl mx-auto mb-14 reveal-up">
                <span class="text-xs uppercase tracking-widest font-bold text-coral bg-coral/10 px-3.5 py-1.5 rounded-full border border-coral/20">Khách hàng nói gì</span>
                <h2 class="text-3xl sm:text-4xl font-serif font-bold text-espresso mt-3">Trải Nghiệm Thực Tế</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if(isset($reviews) && count($reviews) > 0)
                    @foreach($reviews as $review)
                        <div class="reveal-up hover-lift bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
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
                    <div class="reveal-up hover-lift bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
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

                    <div class="reveal-up hover-lift bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
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

                    <div class="reveal-up hover-lift bg-[#FAF7F2] p-6 rounded-3xl shadow-sm border border-espresso/5 flex flex-col justify-between">
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
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto reveal-zoom">
            <div class="rounded-[32px] overflow-hidden shadow-2xl relative bg-espresso hover-lift">
                <img src="{{ asset($promoBanner->image_url ?? 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop') }}" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay" />
                <div class="relative z-10 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-white text-center md:text-left">
                        @if($promoBanner->badge)
                            <span class="inline-block bg-coral text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider animate-pulse-glow">{{ $promoBanner->badge }}</span>
                        @endif
                        <h3 class="font-serif font-bold text-3xl md:text-5xl mb-3">{{ $promoBanner->title }}</h3>
                        <p class="font-sans text-white/80 text-base md:text-lg">{!! nl2br(e($promoBanner->description)) !!}</p>
                    </div>
                    <a href="{{ $promoBanner->button_link ?? route('cart.index') }}" class="shrink-0 bg-coral text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-white hover:text-espresso btn-pulse transition-colors duration-300 shadow-lg">{{ $promoBanner->button_text ?? 'Đổi mã ngay' }}</a>
                </div>
            </div>
        </section>
    @else
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto reveal-zoom">
            <div class="rounded-[32px] overflow-hidden shadow-2xl relative bg-espresso hover-lift">
                <img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay" />
                <div class="relative z-10 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-white text-center md:text-left">
                        <span class="inline-block bg-coral text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider animate-pulse-glow">ƯU ĐÃI THÁNG 8</span>
                        <h3 class="font-serif font-bold text-3xl md:text-5xl mb-3">Giảm 20% toàn bộ đơn hàng!</h3>
                        <p class="font-sans text-white/80 text-base md:text-lg">Nhập mã <strong class="text-coral bg-white px-2.5 py-1 rounded-md mx-1 shadow-xs">CHILL20</strong> khi thanh toán online.</p>
                    </div>
                    <a href="{{ route('cart.index') }}" class="shrink-0 bg-coral text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-white hover:text-espresso btn-pulse transition-colors duration-300 shadow-lg">Đổi mã ngay</a>
                </div>
            </div>
        </section>
    @endif



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const heroImages = [
                "{{ format_image_url($heroBanner->image_url ?? '/images/banner1.png', '/images/banner1.png') }}?v=1812",
                "{{ format_image_url('/images/banner2.png') }}?v=1812",
                "{{ format_image_url('/images/banner3.png') }}?v=1812"
            ];
            let currentHeroIdx = 0;

            // Preload images into browser cache so switching is instantaneous
            heroImages.forEach(function(src) {
                const img = new Image();
                img.src = src;
            });

            window.changeHeroBanner = function(idx) {
                currentHeroIdx = idx;
                const bgImg = document.getElementById('hero-banner-bg');
                if (bgImg) {
                    bgImg.style.opacity = '0.2';
                    setTimeout(() => {
                        bgImg.src = heroImages[currentHeroIdx];
                        bgImg.style.opacity = '1';
                    }, 200);
                }

                const dots = document.querySelectorAll('.hero-banner-dot');
                dots.forEach((dot, i) => {
                    if (i === idx) {
                        dot.className = 'h-3 w-8 rounded-full bg-coral transition-all duration-300 hero-banner-dot';
                    } else {
                        dot.className = 'h-3 w-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300 hero-banner-dot';
                    }
                });
            };

            setInterval(() => {
                currentHeroIdx = (currentHeroIdx + 1) % heroImages.length;
                window.changeHeroBanner(currentHeroIdx);
            }, 6000);
        });
    </script>
@endsection