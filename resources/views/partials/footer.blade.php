<footer class="bg-[#FAF7F2] border-t border-espresso/5 text-espresso pt-16 pb-8 px-6 sm:px-12 mt-20">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            
            {{-- Cột 1: Thông tin thương hiệu --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-coral/15 text-coral flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                    </div>
                    <h2 class="font-serif font-black text-2xl text-espresso tracking-tight">
                        Chill Chill
                    </h2>
                </div>
                <p class="text-xs text-espresso/70 leading-relaxed">
                    Điểm đến yêu thích mỗi ngày cho cà phê nguyên chất, trà sữa đậm vị và không gian chill dịu êm.
                </p>
                
                {{-- Mạng xã hội --}}
                <div class="flex items-center gap-2 pt-2">
                    <a href="#" class="w-8 h-8 rounded-full bg-white border border-espresso/10 flex items-center justify-center text-espresso/60 hover:text-coral hover:border-coral transition-colors shadow-2xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white border border-espresso/10 flex items-center justify-center text-espresso/60 hover:text-coral hover:border-coral transition-colors shadow-2xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Cột 2: LIÊN KẾT NHANH --}}
            <div class="flex flex-col gap-3">
                <h3 class="text-xs uppercase tracking-widest font-black text-espresso mb-1">LIÊN KẾT NHANH</h3>
                <a href="{{ url('/') }}" class="text-xs text-espresso/70 hover:text-coral transition-colors font-medium">Trang chủ</a>
                <a href="{{ route('product.index') }}" class="text-xs text-espresso/70 hover:text-coral transition-colors font-medium">Thực đơn</a>
                <a href="{{ url('/#best-sellers') }}" class="text-xs text-espresso/70 hover:text-coral transition-colors font-medium">Món bán chạy</a>
            </div>

            {{-- Cột 3: GIỜ MỞ CỬA --}}
            <div class="flex flex-col gap-3">
                <h3 class="text-xs uppercase tracking-widest font-black text-espresso mb-1">GIỜ MỞ CỬA</h3>
                <div class="flex justify-between items-center text-xs text-espresso/70">
                    <span>Thứ 2 – Thứ 6:</span>
                    <span class="font-bold text-espresso">7:00 AM – 9:30 PM</span>
                </div>
                <div class="flex justify-between items-center text-xs text-espresso/70">
                    <span>Thứ 7 – CN:</span>
                    <span class="font-bold text-espresso">8:00 AM – 10:00 PM</span>
                </div>
                <span class="text-coral font-semibold text-xs flex items-center gap-1 mt-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Giao hàng tận nơi cả ngày
                </span>
            </div>

            {{-- Cột 4: CHILL CLUB --}}
            <div class="flex flex-col gap-3">
                <h3 class="text-xs uppercase tracking-widest font-black text-espresso mb-1">CHILL CLUB</h3>
                <p class="text-xs text-espresso/70 leading-relaxed">
                    Đăng ký nhận ưu đãi đặc biệt và voucher giảm giá mỗi tuần.
                </p>
                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận thông tin!');" class="relative flex items-center mt-1">
                    <input type="email" placeholder="Nhập email của bạn..." class="w-full bg-white border border-espresso/10 rounded-full px-4 py-2.5 text-xs text-espresso placeholder-espresso/40 focus:outline-none focus:border-coral shadow-2xs pr-10">
                    <button type="submit" class="absolute right-1 w-7 h-7 rounded-full bg-coral text-white flex items-center justify-center hover:bg-[#d5523b] transition-all shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="border-t border-espresso/10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-espresso/50 font-medium gap-3">
            <p>© 2026 Chill Chill Coffee & Tea. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-espresso transition-colors">Chính sách bảo mật</a>
                <a href="#" class="hover:text-espresso transition-colors">Điều khoản dịch vụ</a>
            </div>
        </div>
    </div>
</footer>