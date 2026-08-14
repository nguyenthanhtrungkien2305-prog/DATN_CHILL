@extends('layouts.app')

@section('title', 'Chuyện Nhà - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen pb-24 overflow-hidden">

    {{-- 1. HERO SECTION (Banner toàn màn hình) --}}
    <section class="relative w-full h-[60vh] md:h-[70vh] flex items-center justify-center text-center">
        <img src="{{ asset('images/anhgioithieu1.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Banner Chuyện Nhà">
        <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px]"></div>
        
        <div class="relative z-10 max-w-3xl px-6 reveal-zoom">
            <h3 class="text-espresso font-bold uppercase tracking-[0.2em] mb-4 text-sm md:text-base animate-bounce-slow">Chuyện "Chill Chill"</h3>
            <h1 class="font-serif font-black text-4xl md:text-5xl lg:text-6xl text-espresso leading-tight mb-6 drop-shadow-sm">
                MỖI NỤ CƯỜI LÀ MỘT CÂU CHUYỆN - VÀ "CHILL CHILL" LÀ NƠI LƯU GIỮ TẤT CẢ
            </h1>
            <p class="text-espresso/80 font-medium md:text-lg">
                Chill Chill sẽ là nơi mọi người xích lại gần nhau, đề cao giá trị kết nối con người và sẻ chia thân tình bên những tách cà phê, ly trà đượm hương, truyền cảm hứng về lối sống hiện đại.
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 space-y-32 pt-24">

        {{-- 2. SECTION 1: Chữ TRÁI - Ảnh PHẢI (Có sticker và ảnh xếp chồng) --}}
        <section class="flex flex-col md:flex-row items-center gap-12 lg:gap-24">
            {{-- Cột Chữ --}}
            <div class="w-full md:w-5/12 reveal-left">
                <h2 class="font-serif font-black text-4xl md:text-5xl text-espresso mb-6">Chuyện "Nhà Chill"</h2>
                <div class="text-espresso/80 space-y-4 leading-relaxed">
                    <p>Đến Chill Chill đâu chỉ là thưởng thức hương vị trọn vẹn của một món nước. Từng ly trà, tách cà phê còn là chất xúc tác để những câu chuyện thêm đậm đà, những tiếng cười thêm rộn rã.</p>
                    <p>Chúng tôi mang những nguyên bản để gửi gắm vào từng sản phẩm, mong rằng mỗi chi nhánh sẽ là góc nhà nhỏ đong đầy hơi ấm, nơi bạn nạp lại năng lượng để bắt đầu niềm vui mới.</p>
                </div>
            </div>
            
            {{-- Cột Ảnh (Hiệu ứng Collage) --}}
            <div class="w-full md:w-7/12 relative mt-12 md:mt-0 reveal-right">
                <img src="{{ asset('images/anhnen2.png') }}" class="rounded-[32px] w-4/5 ml-auto shadow-2xl object-cover aspect-[4/3] hover-lift" alt="Không gian quán">
                
                {{-- Ảnh đè lên góc trái dưới --}}
                <img src="{{ asset('images/anhcauchuyen2.png') }}" class="absolute -bottom-10 left-0 w-1/2 rounded-[24px] border-8 border-[#FAF7F2] shadow-xl object-cover aspect-square transform -rotate-3 hover:rotate-0 hover-rotate transition-all duration-500" alt="Góc Chill">

            </div>
        </section>


        {{-- 3. SECTION 2: Ảnh TRÁI - Chữ PHẢI (Reverse) --}}
        <section class="flex flex-col md:flex-row-reverse items-center gap-12 lg:gap-24">
            {{-- Cột Chữ --}}
            <div class="w-full md:w-5/12 reveal-right">
                <div class="flex items-center gap-4 mb-4">
                    <h2 class="font-serif font-black text-4xl md:text-5xl text-espresso leading-tight">Nguyên bản từ giá trị hạt cà phê chất lượng</h2>
                </div>
                <div class="text-espresso/80 space-y-4 leading-relaxed">
                    <p>Hành trình của Chill Chill bắt đầu từ những hạt cà phê Robusta và Arabica hảo hạng nhất vùng cao nguyên.</p>
                    <p>Từng hạt cà phê được lựa chọn kỹ lưỡng, rang xay đúng chuẩn để giữ lại vị mộc mạc, đậm đà nguyên bản. Dù là Phin truyền thống hay Espresso hiện đại, mỗi ly cà phê đều mang theo tâm huyết của người pha chế.</p>
                </div>
            </div>
            
            {{-- Cột Ảnh --}}
            <div class="w-full md:w-7/12 relative mt-12 md:mt-0 reveal-left">
                <img src="{{ asset('images/anhgioithieu2.jpg') }}" class="rounded-[32px] w-4/5 shadow-2xl object-cover aspect-[4/3] hover-lift" alt="Hạt cà phê">
                
                {{-- Ảnh đè góc phải dưới --}}
                <img src="{{ asset('images/anhcauchuyen1.png') }}" class="absolute -bottom-12 right-0 w-[45%] rounded-[24px] border-8 border-[#FAF7F2] shadow-xl object-cover aspect-[4/5] transform rotate-3 hover:rotate-0 hover-rotate transition-all duration-500" alt="Pha cà phê">
    
            </div>
        </section>


        {{-- 4. SECTION 3: Chữ TRÁI - Ảnh PHẢI --}}
        <section class="flex flex-col md:flex-row items-center gap-12 lg:gap-24">
            {{-- Cột Chữ --}}
            <div class="w-full md:w-5/12 reveal-left">
                <div class="flex items-center gap-4 mb-4">
                    <h2 class="font-serif font-black text-4xl md:text-5xl text-espresso leading-tight">Chất lượng khởi nguồn từ những đồi trà tuyển chọn</h2>
                </div>
                <div class="text-espresso/80 space-y-4 leading-relaxed">
                    <p>Giữa những đồi trà xanh mướt trong sương sớm, Chill Chill tìm thấy nguồn cảm hứng cho những thức uống thanh mát của mình.</p>
                    <p>Những búp trà non được hái bằng tay, ủ theo công thức đặc biệt để giữ được hương thơm dịu nhẹ, chát ngọt hậu vị. Kết hợp cùng trái cây tươi nguyên bản, mỗi ly trà là một bản giao hưởng của vị giác.</p>
                </div>
            </div>
            
            {{-- Cột Ảnh --}}
            <div class="w-full md:w-7/12 relative mt-12 md:mt-0 reveal-zoom">
                <img src="{{ asset('images/tra.png') }}" class="rounded-[32px] w-5/6 ml-auto shadow-2xl object-cover aspect-[16/9] hover-lift" alt="Đồi trà">
                
                {{-- Tranh/Khung nhỏ góc trái dưới --}}
                <img src="{{ asset('images/anhgioithieu3.jpg') }}" class="absolute -bottom-8 left-4 w-40 h-40 rounded-full border-8 border-[#FAF7F2] shadow-xl object-cover hover:scale-110 transition-transform duration-500" alt="Món trà tuyển chọn">
                
                {{-- Sticker mộc mạc --}}
                <div class="absolute top-10 left-10 bg-white border-2 border-dashed border-espresso text-espresso px-4 py-1 rounded-sm transform -rotate-6 font-serif italic font-bold hover-rotate">
                    Trà xanh Oolong
                </div>
            </div>
        </section>

    </div>

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