@extends('layouts.app')

@section('title', 'Tin Tức & Blog - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen pb-24 overflow-hidden">

    {{-- ========================================== --}}
    {{-- HERO SECTION & BỘ LỌC (TABS) --}}
    {{-- ========================================== --}}
    <section class="relative pt-24 pb-16 bg-gradient-to-b from-[#FFF0D4]/60 to-[#FAF7F2]">
        <div class="max-w-3xl mx-auto text-center px-6 relative z-10 reveal">
            <h1 class="font-serif font-black text-4xl md:text-5xl lg:text-6xl text-espresso mb-6">Góc Cà Phê & Blog</h1>
            <p class="text-espresso/80 font-medium md:text-lg mb-10 leading-relaxed">
                Tại chuyên mục Tin tức & Blog, Chill Chill kể những câu chuyện xoay quanh hạt cà phê – từ hành trình chọn lọc, rang xay đến ly cà phê trọn vị trên tay bạn. Mỗi bài viết là một lát cắt nhỏ trong hành trình mang nụ cười đến từ hương vị nguyên bản.
            </p>
            
            {{-- Tabs / Bộ lọc Danh mục --}}
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('post.index') }}" 
                   class="px-7 py-2.5 rounded-full font-bold text-xs tracking-widest uppercase transition-all shadow-xs {{ empty($selectedCatSlug) ? 'bg-coral text-white shadow-md' : 'bg-white text-coral border border-coral hover:bg-coral hover:text-white' }}">
                    Tất cả
                </a>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <a href="{{ route('post.index', ['category' => $cat->slug]) }}" 
                           class="px-7 py-2.5 rounded-full font-bold text-xs tracking-widest uppercase transition-all shadow-xs {{ ($selectedCatSlug ?? '') === $cat->slug ? 'bg-coral text-white shadow-md' : 'bg-white text-coral border border-coral hover:bg-coral hover:text-white' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Sticker xoay tròn trang trí --}}
        <div class="hidden md:flex absolute top-20 right-[15%] lg:right-[20%] bg-coral text-white w-24 h-24 rounded-full items-center justify-center font-bold text-center leading-tight shadow-xl rotate-animation border-2 border-white/40">
            Khám phá<br>ngay
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- LƯỚI BÀI VIẾT (ASYMMETRIC GRID) --}}
    {{-- ========================================== --}}
    <section class="max-w-7xl mx-auto px-6 py-8">
        @if(!isset($posts) || $posts->isEmpty())
            <div class="bg-white rounded-[32px] p-12 text-center shadow-sm border border-espresso/5 max-w-xl mx-auto">
                <span class="text-4xl mb-4 block">📰</span>
                <h3 class="font-serif font-bold text-2xl text-espresso mb-2">Chưa có bài viết nào</h3>
                <p class="text-espresso/60 text-sm">Các bài viết mới nhất sẽ sớm được cập nhật tại chuyên mục này!</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @foreach($posts as $index => $p)
                    {{-- Bài viết thứ nhất (index 0) tạo điểm nhấn nổi bật trên khung lớn --}}
                    @if($index === 0 && $posts->currentPage() == 1 && empty($selectedCatSlug))
                        <article class="reveal-zoom hover-lift lg:col-span-3 bg-[#FFF9ED] rounded-[32px] overflow-hidden group shadow-sm hover:shadow-xl transition-all duration-400 border border-espresso/5 grid grid-cols-1 lg:grid-cols-12">
                            <a href="{{ route('post.show', $p->slug) }}" class="lg:col-span-7 aspect-[16/10] overflow-hidden relative block">
                                <img src="{{ format_image_url($p->thumbnail, '/images/banner1.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $p->title }}" onerror="this.onerror=null; this.src='/images/banner1.png';">
                            </a>
                            <div class="lg:col-span-5 p-8 md:p-10 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-center mb-4 text-xs font-bold uppercase tracking-widest">
                                        <span class="text-coral bg-coral/10 px-3 py-1 rounded-full border border-coral/20">{{ $p->category_name ?? 'Blog' }}</span>
                                        <span class="text-espresso/40">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</span>
                                    </div>
                                    <h3 class="font-serif font-bold text-2xl md:text-3xl text-espresso mb-4 group-hover:text-coral transition-colors leading-snug">
                                        <a href="{{ route('post.show', $p->slug) }}">{{ $p->title }}</a>
                                    </h3>
                                    <p class="text-espresso/70 text-sm leading-relaxed line-clamp-4">
                                        {{ Str::limit(strip_tags($p->content), 200) }}
                                    </p>
                                </div>
                                <div class="mt-6 pt-6 border-t border-espresso/10 flex justify-between items-center">
                                    <span class="text-xs font-medium text-espresso/60">Tác giả: <strong class="text-espresso">{{ $p->author_name ?? 'Chill Chill' }}</strong></span>
                                    <a href="{{ route('post.show', $p->slug) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-coral uppercase tracking-wider group-hover:translate-x-1 transition-transform">
                                        Đọc tiếp →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @else
                        {{-- Thẻ bài viết chuẩn --}}
                        <article class="reveal-up hover-lift bg-[#FFF9ED] rounded-[32px] overflow-hidden group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-400 border border-espresso/5 flex flex-col">
                            <a href="{{ route('post.show', $p->slug) }}" class="w-full aspect-[4/3] overflow-hidden relative block">
                                <img src="{{ format_image_url($p->thumbnail, '/images/caphe.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $p->title }}" onerror="this.onerror=null; this.src='/images/caphe.png';">
                            </a>
                            <div class="p-6 md:p-8 flex-1 flex flex-col">
                                <div class="flex justify-between items-center mb-3 text-xs font-bold uppercase tracking-widest">
                                    <span class="text-coral">{{ $p->category_name ?? 'Blog' }}</span>
                                    <span class="text-espresso/40">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</span>
                                </div>
                                <h3 class="font-serif font-bold text-lg md:text-xl text-espresso mb-3 group-hover:text-coral transition-colors leading-tight line-clamp-2">
                                    <a href="{{ route('post.show', $p->slug) }}">{{ $p->title }}</a>
                                </h3>
                                <p class="text-espresso/70 text-xs md:text-sm leading-relaxed line-clamp-3 mb-6">
                                    {{ Str::limit(strip_tags($p->content), 120) }}
                                </p>
                                <div class="mt-auto pt-4 border-t border-espresso/5 flex justify-between items-center">
                                    <span class="text-[11px] text-espresso/50">Tác giả: {{ $p->author_name ?? 'Chill Chill' }}</span>
                                    <a href="{{ route('post.show', $p->slug) }}" class="text-xs font-bold text-coral hover:underline">Chi tiết →</a>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-12 flex justify-center">
                {{ $posts->links() }}
            </div>
        @endif
    </section>

    <style>
        .rotate-animation {
            animation: slowSpin 10s linear infinite;
        }
        @keyframes slowSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</div>
@endsection