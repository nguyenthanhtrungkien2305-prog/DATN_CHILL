@extends('layouts.app')

@section('title', 'Liên Hệ - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen pb-24">

    {{-- ========================================== --}}
    {{-- PHẦN 1: BẢN ĐỒ TOÀN MÀN HÌNH (HERO MAP) --}}
    {{-- ========================================== --}}
    <section class="relative w-full h-[50vh] min-h-[400px]">
        {{-- Iframe Google Map (Bạn có thể thay đổi src thành địa chỉ thực tế của bạn) --}}
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.325316304561!2d106.69488347460341!3d10.78637698936302!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3484f27113%3A0xc3afb65492d27c62!2zTmjDoCBUaOG7nSDEkOG7qWMgQsOgIFPDoGkgR8Oybg!5e0!3m2!1svi!2s!4v1711200000000!5m2!1svi!2s" 
                class="w-full h-full border-0 filter grayscale-[20%] contrast-125 opacity-90" 
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        {{-- Lớp phủ gradient để bản đồ chìm xuống một chút --}}
        <div class="absolute inset-0 bg-gradient-to-b from-espresso/20 to-transparent pointer-events-none"></div>
    </section>

    {{-- ========================================== --}}
    {{-- PHẦN 2: THẺ LIÊN HỆ XẾP CHỒNG (OVERLAPPING CARD) --}}
    {{-- ========================================== --}}
    <section class="max-w-6xl mx-auto px-6 relative -mt-32 z-10 reveal">
        <div class="bg-white rounded-[40px] shadow-2xl flex flex-col md:flex-row overflow-hidden border border-espresso/5">
            
            {{-- Cột Trái: Thông tin liên hệ (Nền tối sang trọng) --}}
            <div class="w-full md:w-5/12 bg-espresso text-cream p-10 md:p-14 flex flex-col relative overflow-hidden">
                {{-- Background Pattern mờ --}}
                <svg class="absolute -bottom-16 -right-16 w-64 h-64 text-white/5 transform rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm3.98-10.165a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z" /></svg>

                <div class="relative z-10">
                    <h2 class="font-serif font-black text-4xl mb-2 text-white">Kết nối nhé!</h2>
                    <p class="text-cream/70 mb-12">Chúng tôi luôn sẵn sàng lắng nghe và giải đáp mọi thắc mắc từ bạn.</p>

                    <ul class="space-y-8">
                        <li class="flex items-start gap-4 group">
                            <div class="w-12 h-12 shrink-0 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-coral transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">Địa chỉ</h4>
                                <p class="text-cream/70 text-sm leading-relaxed">123 Đường Cà Phê, Phường Bến Nghé,<br>Quận 1, TP. Hồ Chí Minh</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-4 group">
                            <div class="w-12 h-12 shrink-0 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-coral transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">Điện thoại</h4>
                                <p class="text-cream/70 text-sm">1800 6936</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-4 group">
                            <div class="w-12 h-12 shrink-0 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-coral transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">Email</h4>
                                <p class="text-cream/70 text-sm">support@chillchill.vn</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Mạng xã hội --}}
                <div class="mt-auto pt-12 relative z-10 flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-espresso transition-colors text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-espresso transition-colors text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Cột Phải: Form Gửi Lời Nhắn (Nền sáng) --}}
            <div class="w-full md:w-7/12 p-10 md:p-14 bg-white">
                <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Gửi lời nhắn</h3>
                <p class="text-espresso/60 mb-8">Hãy để lại thông tin, Chill Chill sẽ liên hệ với bạn trong thời gian sớm nhất.</p>

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tên --}}
                        <div>
                            <label class="block text-sm font-bold text-espresso mb-2 uppercase tracking-wide">Họ và tên</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/30" required>
                        </div>
                        
                        {{-- Điện thoại --}}
                        <div>
                            <label class="block text-sm font-bold text-espresso mb-2 uppercase tracking-wide">Điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại của bạn" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/30">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email liên hệ" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/30" required>
                    </div>

                    {{-- Nội dung --}}
                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2 uppercase tracking-wide">Nội dung tin nhắn</label>
                        <textarea name="message" rows="4" placeholder="Bạn muốn nhắn nhủ điều gì tới Chill Chill?" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/30 resize-none" required>{{ old('message') }}</textarea>
                    </div>

                    {{-- Nút Submit --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-coral text-white rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all duration-300 flex items-center justify-center gap-3 group">
                            Gửi tin nhắn
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </section>

</div>
@endsection