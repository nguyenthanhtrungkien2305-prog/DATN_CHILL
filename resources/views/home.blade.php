@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-6 pt-4 pb-16 flex flex-col md:flex-row gap-6 min-h-[75vh] items-stretch">
    <div class="w-full md:w-7/12 rounded-[40px] overflow-hidden relative min-h-[400px] shadow-xl group">
        <img src="https://images.unsplash.com/photo-1485808191679-5f86510681a2?q=80&w=1200&auto=format&fit=crop" alt="Cà phê và Bánh ngọt" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
    </div>

    <div class="w-full md:w-5/12 bg-[#C4A484] rounded-[40px] p-10 md:p-14 flex flex-col justify-center relative overflow-hidden shadow-xl">
        <svg class="absolute -bottom-16 -right-16 w-80 h-80 text-white/10 transform rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm3.98-10.165a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z" /></svg>
        <div class="relative z-10 text-espresso">
            <span class="inline-block px-4 py-1.5 rounded-full bg-white/30 text-espresso text-sm font-semibold tracking-widest uppercase mb-6 backdrop-blur-sm border border-white/40">Khơi nguồn cảm hứng</span>
            <h1 class="font-serif font-bold text-5xl lg:text-6xl leading-[1.1] mb-6">
                Hương vị <br />
                <span class="text-white drop-shadow-sm italic">tinh tế</span>
                <br />
                mỗi ngày.
            </h1>
            <p class="font-sans text-espresso/80 text-lg mb-8 leading-relaxed font-medium">
                Tận hưởng sự kết hợp hoàn hảo giữa những hạt cà phê rang xay thượng hạng và bánh ngọt mềm mịn, được chuẩn bị bằng cả trái tim.
            </p>
            <button class="bg-white text-espresso font-bold text-lg px-8 py-4 rounded-full hover:bg-espresso hover:text-white transition-all duration-300 shadow-xl flex items-center gap-3 w-max group">
                Đặt món ngay
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </button>
        </div>
    </div>
</section>

<div class="bg-coral text-white py-6">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-white/20">
        <div class="px-4">
            <h4 class="font-serif font-bold text-2xl">50+</h4>
            <p class="text-sm font-light">Cửa hàng toàn quốc</p>
        </div>
        <div class="px-4">
            <h4 class="font-serif font-bold text-2xl">100%</h4>
            <p class="text-sm font-light">Cà phê nguyên chất</p>
        </div>
        <div class="px-4">
            <h4 class="font-serif font-bold text-2xl">30p</h4>
            <p class="text-sm font-light">Giao hàng tốc hành</p>
        </div>
        <div class="px-4">
            <h4 class="font-serif font-bold text-2xl">24/7</h4>
            <p class="text-sm font-light">Hỗ trợ khách hàng</p>
        </div>
    </div>
</div>

<section id="menu" class="py-24 px-6 max-w-7xl mx-auto">
    <div class="text-center mb-16">
        <span class="text-coral font-bold tracking-widest uppercase text-sm mb-2 block">Thực đơn</span>
        <h2 class="reveal font-serif font-bold text-espresso text-4xl md:text-5xl">Thế giới hương vị</h2>
    </div>

   {{-- Lưới Grid bất đối xứng --}}
    <div class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 h-auto md:h-[600px]">
        
        {{-- Item 1: Chiếm 2 cột, nằm trên --}}
        @if(isset($categories[0]))
        <a href="{{ route('product.index', ['category' => $categories[0]->category_id]) }}" class="reveal block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full">
            <img src="{{ $categories[0]->image ?? 'https://via.placeholder.com/800x400' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 via-espresso/20 to-transparent"></div>
            <div class="absolute bottom-8 left-8 z-10">
                <h3 class="font-serif font-bold text-4xl text-white mb-2">{{ $categories[0]->name }}</h3>
                <span class="inline-block bg-white/20 backdrop-blur-md text-white px-5 py-2 rounded-full text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                    Xem ngay →
                </span>
            </div>
        </a>
        @endif

        {{-- Item 2: Chiếm 1 cột, nằm trên góc phải --}}
        @if(isset($categories[1]))
        <a href="{{ route('product.index', ['category' => $categories[1]->category_id]) }}" class="reveal block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full" style="transition-delay: 100ms">
            <img src="{{ $categories[1]->image ?? 'https://via.placeholder.com/400x400' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 to-transparent"></div>
            <div class="absolute bottom-8 left-8 z-10">
                <h3 class="font-serif font-bold text-2xl text-white mb-2">{{ $categories[1]->name }}</h3>
            </div>
        </a>
        @endif

        {{-- Item 3: Chiếm 1 cột, nằm dưới góc trái --}}
        @if(isset($categories[2]))
        <a href="{{ route('product.index', ['category' => $categories[2]->category_id]) }}" class="reveal block relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full" style="transition-delay: 200ms">
            <img src="{{ $categories[2]->image ?? 'https://via.placeholder.com/400x400' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 to-transparent"></div>
            <div class="absolute bottom-8 left-8 z-10">
                <h3 class="font-serif font-bold text-2xl text-white mb-2">{{ $categories[2]->name }}</h3>
            </div>
        </a>
        @endif

        {{-- Item 4: Chiếm 2 cột, nằm dưới, kết hợp chữ + ảnh nửa bên --}}
        @if(isset($categories[3]))
        <a href="{{ route('product.index', ['category' => $categories[3]->category_id]) }}" class="reveal block md:col-span-2 relative rounded-[32px] overflow-hidden group cursor-pointer shadow-lg h-64 md:h-full bg-cream-light flex items-center justify-between p-8" style="transition-delay: 300ms">
            <div class="z-10 max-w-sm">
                <h3 class="font-serif font-bold text-3xl text-espresso mb-3">{{ $categories[3]->name }}</h3>
                <p class="text-espresso/70 mb-4">Khám phá hương vị đặc trưng được chọn lọc.</p>
                <span class="inline-block text-coral font-bold underline underline-offset-4 group-hover:text-espresso transition-colors">Khám phá ngay</span>
            </div>
            <img src="{{ $categories[3]->image ?? 'https://via.placeholder.com/400' }}" class="absolute right-0 top-0 h-full w-1/2 object-cover object-left mask-image-gradient transition-transform duration-700 group-hover:scale-105" />
        </a>
        @endif

    </div>
</section>

<section id="promotions" class="py-16 px-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-end mb-12">
        <div>
            <span class="text-coral font-bold tracking-widest uppercase text-sm mb-2 block">Mua nhiều nhất</span>
            <h2 class="reveal font-serif font-bold text-espresso text-4xl">Gợi ý cho bạn</h2>
        </div>
        <a href="#" class="hidden md:block text-coral font-medium border-b border-coral pb-1 hover:text-espresso hover:border-espresso transition-colors">Xem tất cả</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach($products as $index => $product)
            <article class="reveal product-card bg-white rounded-[24px] p-4 flex flex-col relative group border border-transparent hover:border-coral/20" style="transition-delay: {{ $index * 100 }}ms">
                
                <div class="w-full aspect-square rounded-[16px] overflow-hidden bg-cream relative mb-4">
                    {{-- Thẻ a bọc quanh ảnh để click vào chi tiết --}}
                    <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                        <img src="{{ $product->image_url ?? 'https://via.placeholder.com/400' }}" class="product-image w-full h-full object-cover" alt="{{ $product->name }}" />
                    </a>
                    
                    {{-- Nút giỏ hàng --}}
                    <button type="button" onclick="quickAddToCart({{ $product->product_id }})" class="absolute z-10 bottom-4 right-4 bg-white text-espresso w-10 h-10 rounded-full flex items-center justify-center shadow-lg opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-coral hover:text-white" title="Thêm nhanh vào giỏ hàng">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>
                
                <h3 class="font-serif font-bold text-xl text-espresso mb-1 group-hover:text-coral transition-colors">
                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <p class="text-sm text-espresso/60 mb-3 line-clamp-2">{{ $product->description }}</p>
                
                <div class="mt-auto flex items-center justify-between">
                    <span class="text-espresso font-bold text-lg">Từ {{ number_format($product->price, 0, ',', '.') }} đ</span>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="py-24 bg-white mt-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="text-coral font-bold tracking-widest uppercase text-sm mb-2 block">Khách hàng nói gì</span>
            <h2 class="reveal font-serif font-bold text-espresso text-4xl">Trải nghiệm thực tế</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(isset($reviews) && count($reviews) > 0)
                @foreach($reviews as $index => $review)
                    <div class="reveal bg-[#FAF7F2] p-8 rounded-[32px] relative" style="transition-delay: {{ $index * 150 }}ms">
                        <span class="text-6xl text-coral/20 font-serif absolute top-4 left-6">"</span>
                        
                        <div class="flex text-coral mb-4">
                            @for($i = 0; $i < $review->rating; $i++) ★ @endfor
                        </div>
                        
                        <p class="text-espresso/80 italic mb-6">"{{ $review->content }}"</p>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                                <img src="{{ $review->avatar ?? 'https://i.pravatar.cc/150' }}" alt="Avatar" />
                            </div>
                            <div>
                                <h4 class="font-bold text-espresso">{{ $review->name }}</h4>
                                <p class="text-xs text-espresso/50">Khách hàng</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="md:col-span-3 text-center text-espresso/50 italic">
                    Chưa có đánh giá nào.
                </div>
            @endif
        </div>
    </div>
</section>

<section class="py-12 px-6 max-w-7xl mx-auto -mt-8 relative z-10">
    <div class="reveal rounded-[32px] overflow-hidden shadow-2xl relative bg-espresso">
        <img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" />
        <div class="relative z-10 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-white">
                <span class="inline-block bg-coral text-white text-xs font-bold px-3 py-1 rounded-full mb-4">ƯU ĐÃI THÁNG 3</span>
                <h3 class="font-serif font-bold text-4xl md:text-5xl mb-4">Giảm 20% toàn bộ đơn hàng!</h3>
                <p class="font-sans text-white/80 text-lg">Nhập mã <strong class="text-coral bg-white px-2 py-1 rounded-md mx-1">CHILL20</strong> khi thanh toán online.</p>
            </div>
            <button class="shrink-0 bg-coral text-white font-bold text-lg px-10 py-4 rounded-full hover:bg-white hover:text-espresso transition-colors duration-300 shadow-lg">Đổi mã ngay</button>
        </div>
    </div>
</section>

<section id="store" class="py-24 px-6 max-w-7xl mx-auto bg-[#FAF7F2]">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div class="reveal">
            <span class="text-coral font-bold tracking-widest uppercase text-sm mb-2 block">Hệ thống</span>
            <h2 class="font-serif font-bold text-espresso text-4xl mb-6">Ghé thăm trạm dừng chân của bạn</h2>
            <p class="text-espresso/70 mb-8 text-lg">Hơn 50 cửa hàng trên toàn quốc với không gian mở, xanh mát, là nơi lý tưởng để chạy deadline hoặc tán gẫu cùng bạn bè.</p>
            <ul class="space-y-4 mb-8">
                <li class="flex items-start gap-3 text-espresso">
                    <svg class="w-6 h-6 text-coral shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span><strong>Quận 1:</strong> 123 Đường Cà Phê, Phường Bến Nghé</span>
                </li>
                <li class="flex items-start gap-3 text-espresso">
                    <svg class="w-6 h-6 text-coral shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span><strong>Quận 3:</strong> 45 Trà Xanh, Phường Võ Thị Sáu</span>
                </li>
            </ul>
            <button class="bg-espresso text-white font-medium px-8 py-3 rounded-full hover:bg-coral transition-colors">Xem tất cả địa chỉ</button>
        </div>
        <div class="reveal rounded-[32px] overflow-hidden shadow-xl w-full h-[450px]">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4946681007846!2d106.6983053!3d10.7733743!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f40a3b49e59%3A0xa1bd14e483a602db!2sCh%E1%BB%A3%20B%E1%BA%BFn%20Th%C3%A0nh!5e0!3m2!1svi!2s!4v1709210000000!5m2!1svi!2s" width="100%" height="100%" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
@endsection