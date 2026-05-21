@extends('layouts.app')

@section('title', 'Tin Tức & Blog - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen pb-24 overflow-hidden">

    {{-- ========================================== --}}
    {{-- HERO SECTION & BỘ LỌC (TABS) --}}
    {{-- ========================================== --}}
    <section class="relative pt-24 pb-16 bg-gradient-to-b from-[#FFF0D4]/60 to-[#FAF7F2]">
        <div class="max-w-3xl mx-auto text-center px-6 relative z-10 reveal">
            <h1 class="font-serif font-black text-4xl md:text-5xl lg:text-6xl text-espresso mb-6">Coffeeholic</h1>
            <p class="text-espresso/80 font-medium md:text-lg mb-10 leading-relaxed">
                Tại chuyên mục Coffeeholic, Chill Chill kể những câu chuyện xoay quanh hạt cà phê – từ hành trình chọn lọc, rang xay đến ly cà phê trọn vị trên tay bạn. Mỗi bài viết là một lát cắt nhỏ trong hành trình mang nụ cười đến từ hương vị nguyên bản.
            </p>
            
            {{-- Tabs / Bộ lọc --}}
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#" class="px-8 py-2.5 rounded-full bg-coral text-white font-bold text-sm tracking-widest uppercase shadow-md hover:bg-[#d5523b] transition-colors">Coffeeholic</a>
                <a href="#" class="px-8 py-2.5 rounded-full bg-white text-coral border border-coral font-bold text-sm tracking-widest uppercase hover:bg-coral hover:text-white transition-colors">Teaholic</a>
                <a href="#" class="px-8 py-2.5 rounded-full bg-white text-coral border border-coral font-bold text-sm tracking-widest uppercase hover:bg-coral hover:text-white transition-colors">Blog</a>
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
        {{-- Sử dụng grid 4 cột. Bài to chiếm 2 cột, 2 bài nhỏ mỗi bài chiếm 1 cột --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {{-- Bài viết nổi bật (Lớn, bên trái) --}}
            <article class="reveal lg:col-span-2 bg-[#FFF9ED] rounded-[32px] overflow-hidden group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-400 border border-espresso/5 flex flex-col">
                <div class="w-full aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Sài Gòn Xưa">
                </div>
                <div class="p-8 md:p-10 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-4 text-xs font-bold uppercase tracking-widest">
                        <span class="text-coral">Coffeeholic</span>
                        <span class="text-espresso/40">06/12/2026</span>
                    </div>
                    <h3 class="font-serif font-bold text-3xl md:text-4xl text-espresso mb-4 group-hover:text-coral transition-colors leading-snug">
                        BẮT GẶP SÀI GÒN XƯA TRONG MÓN UỐNG HIỆN ĐẠI CỦA GIỚI TRẺ
                    </h3>
                    <p class="text-espresso/70 leading-relaxed line-clamp-3 mt-auto">
                        Dẫu qua bao nhiêu lớp sóng thời gian, người ta vẫn có thể tìm lại những dấu ấn thăng trầm của một Sài Gòn xưa cũ. Trên những góc phố, trong các bức ảnh, trong vô số tác phẩm văn chương... và dĩ nhiên trong cả những hương vị cà phê thân thuộc.
                    </p>
                </div>
            </article>

            {{-- Bài viết nhỏ 1 (Bên phải) --}}
            <article class="reveal lg:col-span-1 bg-[#FFF9ED] rounded-[32px] overflow-hidden group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-400 border border-espresso/5 flex flex-col" style="transition-delay: 100ms;">
                <div class="w-full aspect-[4/3] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Signature">
                </div>
                <div class="p-6 md:p-8 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-3 text-[10px] md:text-xs font-bold uppercase tracking-widest">
                        <span class="text-coral">Coffeeholic</span>
                        <span class="text-espresso/40">05/12/2026</span>
                    </div>
                    <h3 class="font-serif font-bold text-xl md:text-2xl text-espresso mb-3 group-hover:text-coral transition-colors leading-tight">
                        UỐNG GÌ KHI TỚI SIGNATURE BY CHILL CHILL?
                    </h3>
                    <p class="text-espresso/70 text-sm leading-relaxed line-clamp-4 mt-auto">
                        Vừa qua, Chill Chill chính thức khai trương cửa hàng SIGNATURE chuyên phục vụ cà phê đặc sản. Cùng khám phá ngay menu độc đáo đang gây bão giới trẻ Sài Thành nhé.
                    </p>
                </div>
            </article>

            {{-- Bài viết nhỏ 2 (Bên phải) --}}
            <article class="reveal lg:col-span-1 bg-[#FFF9ED] rounded-[32px] overflow-hidden group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-400 border border-espresso/5 flex flex-col" style="transition-delay: 200ms;">
                <div class="w-full aspect-[4/3] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1572490122747-3968b75cc699?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Espresso">
                </div>
                <div class="p-6 md:p-8 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-3 text-[10px] md:text-xs font-bold uppercase tracking-widest">
                        <span class="text-coral">Coffeeholic</span>
                        <span class="text-espresso/40">01/12/2026</span>
                    </div>
                    <h3 class="font-serif font-bold text-xl md:text-2xl text-espresso mb-3 group-hover:text-coral transition-colors leading-tight">
                        CÀ PHÊ SỮA ESPRESSO CHILL CHILL - RẤT LỚN RẤT VỊ NGON
                    </h3>
                    <p class="text-espresso/70 text-sm leading-relaxed line-clamp-4 mt-auto">
                        Cà phê sữa Espresso là một lon cà phê sữa giải khát với hương vị cà phê đậm đà từ 100% cà phê Robusta cùng vị sữa béo ngậy tuyệt hảo.
                    </p>
                </div>
            </article>
            
        </div>
    </section>

    {{-- Kéo thêm style nhỏ cho sticker xoay tròn --}}
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