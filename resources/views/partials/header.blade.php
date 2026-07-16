<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-3 bg-espresso shadow-md">
    <div class="max-w-7xl mx-auto px-6 h-12 flex items-center justify-between">
        
        {{-- ========================================== --}}
        {{-- BÊN TRÁI: Logo (Đã thu nhỏ không gian để menu xích sang) --}}
        {{-- ========================================== --}}
        <div class="shrink-0 flex items-center justify-start lg:w-1/5">
            <a href="/" class="flex items-center group">
                <img src="https://i.ibb.co/30XNqj5/chill-chill-logo-no-bg.png" alt="Chill Chill Logo" class="h-10 md:h-12 w-auto object-contain filter drop-shadow-md group-hover:scale-105 transition-transform" />
            </a>
        </div>

        {{-- ========================================== --}}
        {{-- Ở GIỮA: Menu điều hướng --}}
        {{-- Đổi justify-center thành justify-start và thêm pl-4 để dịch sang trái 1 xíu --}}
        {{-- ========================================== --}}
        <div class="hidden lg:flex flex-1 items-center justify-start lg:pl-4 xl:pl-8">
            <nav class="flex gap-4 xl:gap-8 items-center">
                {{-- Trang chủ --}}
                <a href="/" 
                   class="whitespace-nowrap uppercase tracking-wider transition-all duration-300 text-xs xl:text-sm inline-block
                   {{ request()->is('/') ? 'text-coral font-bold scale-110' : 'text-white/80 font-medium hover:text-coral hover:scale-105' }}">
                   Trang chủ
                </a>
                
                {{-- Nhà Chill (Sản phẩm) --}}
                <a href="{{ route('product.index') }}" 
                   class="whitespace-nowrap uppercase tracking-wider transition-all duration-300 text-xs xl:text-sm inline-block
                   {{ request()->routeIs('product.index') ? 'text-coral font-bold scale-110' : 'text-white/80 font-medium hover:text-coral hover:scale-105' }}">
                   Nhà Chill
                </a>
                
                {{-- Chuyện Nhà Chill (Story) --}}
                <a href="{{ route('post.story') }}" 
                   class="whitespace-nowrap uppercase tracking-wider transition-all duration-300 text-xs xl:text-sm inline-block
                   {{ request()->routeIs('post.story') ? 'text-coral font-bold scale-110' : 'text-white/80 font-medium hover:text-coral hover:scale-105' }}">
                   Chuyện Nhà Chill
                </a>
                
                {{-- Bài viết Chill (Blog/Tin tức) --}}
                <a href="{{ route('post.index') }}" 
                   class="whitespace-nowrap uppercase tracking-wider transition-all duration-300 text-xs xl:text-sm inline-block
                   {{ request()->routeIs('post.index') ? 'text-coral font-bold scale-110' : 'text-white/80 font-medium hover:text-coral hover:scale-105' }}">
                   Bài viết
                </a>
                
                {{-- Liên hệ Chill --}}
                <a href="{{ route('contact') }}" 
                   class="whitespace-nowrap uppercase tracking-wider transition-all duration-300 text-xs xl:text-sm inline-block
                   {{ request()->routeIs('contact') ? 'text-coral font-bold scale-110' : 'text-white/80 font-medium hover:text-coral hover:scale-105' }}">
                   Liên hệ
                </a>
            </nav>
        </div>

        {{-- ========================================== --}}
        {{-- BÊN PHẢI: Tìm kiếm, Giỏ hàng, Tài khoản --}}
        {{-- ========================================== --}}
        <div class="flex items-center justify-end gap-2 xl:gap-3 lg:w-1/4 shrink-0">
            
            {{-- Form Tìm kiếm (Desktop) --}}
            <form action="{{ route('product.index') }}" method="GET" class="hidden md:flex items-center bg-white/10 rounded-full px-4 py-1.5 w-full max-w-[150px] xl:max-w-[200px] border border-white/20 focus-within:border-coral transition-colors">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm..." class="bg-transparent border-none outline-none text-white text-sm placeholder-white/60 w-full focus:ring-0" />
                <button type="submit" class="bg-transparent border-none p-0 flex shrink-0">
                    <svg class="w-4 h-4 text-white/70 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>

            {{-- Nút Tìm kiếm Mobile --}}
            <button onclick="openMobileMenu()" class="md:hidden w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-coral transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </button>
            
            {{-- Nút Giỏ hàng --}}
<a href="{{ route('cart.index') }}" class="w-10 h-10 shrink-0 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-coral transition-colors relative">
    
    {{-- ĐẾM TỔNG SỐ LƯỢNG SẢN PHẨM TRONG GIỎ HÀNG --}}
    @php
        $cartCacheKey = auth()->check()
            ? 'cart:u:' . auth()->id()
            : 'cart:g:' . (request()->cookie('cart_token') ?? '');
        $cartCount = 0;
        foreach (\Illuminate\Support\Facades\Cache::get($cartCacheKey, []) as $item) {
            $cartCount += $item['quantity'];
        }
    @endphp
    
    {{-- HIỂN THỊ CHẤM ĐỎ VÀ SỐ LƯỢNG --}}
    <span id="cart-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-coral rounded-full border-2 border-espresso text-[10px] font-bold flex items-center justify-center shadow-sm {{ $cartCount > 0 ? '' : 'hidden' }}">
        {{ $cartCount }}
    </span>
    
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
</a>           {{-- Nút Tài Khoản (Đã thêm logic kiểm tra đăng nhập) --}}
            @auth
                {{-- ĐÃ ĐĂNG NHẬP: Hiện ảnh Avatar hoặc chữ cái đầu của tên, click vào ra trang Profile --}}
                <a href="{{ route('user.profile') }}" title="Tài khoản của tôi" class="hidden sm:flex shrink-0 w-10 h-10 rounded-full bg-white/10 items-center justify-center text-white hover:bg-coral transition-colors overflow-hidden border border-white/20">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span class="font-bold text-sm uppercase">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    @endif
                </a>
            @else
                {{-- CHƯA ĐĂNG NHẬP: Hiện icon user mặc định, click vào ra trang Login --}}
                <a href="{{ route('login') }}" title="Đăng nhập / Đăng ký" class="hidden sm:flex shrink-0 w-10 h-10 rounded-full bg-white/10 items-center justify-center text-white hover:bg-coral transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </a>
            @endauth

            {{-- Nút Hamburger (Chỉ hiện trên Mobile) --}}
            <button id="hamburger-btn" onclick="openMobileMenu()" class="lg:hidden shrink-0 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-coral transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
            {{-- Nút Trở về Trang Admin (CHỈ HIỆN KHI LÀ ADMIN) --}}
            @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="hidden sm:flex shrink-0 px-4 h-10 rounded-full bg-coral items-center justify-center text-white font-bold text-sm hover:bg-[#d5523b] transition-colors border border-coral/50 shadow-lg">
                    Trang Admin →
                </a>
            @endif
        </div>
    </div>
</header>

{{-- Mobile Menu Drawer --}}
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeMobileMenu()"></div>
    <div class="absolute top-0 left-0 h-full w-72 bg-espresso flex flex-col pt-20 pb-8 px-6 shadow-2xl overflow-y-auto">
        <button onclick="closeMobileMenu()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-coral transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <form action="{{ route('product.index') }}" method="GET" class="flex items-center gap-2 mb-6">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm sản phẩm..." class="flex-1 px-4 py-2.5 bg-white/10 border border-white/20 rounded-full text-white text-sm placeholder-white/50 focus:outline-none focus:border-coral" />
            <button type="submit" class="w-10 h-10 rounded-full bg-coral flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </button>
        </form>

        <nav class="flex flex-col gap-1">
            <a href="/" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium uppercase tracking-wider text-sm transition-colors {{ request()->is('/') ? 'bg-coral/20 text-coral' : '' }}">Trang chủ</a>
            <a href="{{ route('product.index') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium uppercase tracking-wider text-sm transition-colors {{ request()->routeIs('product.index') ? 'bg-coral/20 text-coral' : '' }}">Nhà Chill</a>
            <a href="{{ route('post.story') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium uppercase tracking-wider text-sm transition-colors {{ request()->routeIs('post.story') ? 'bg-coral/20 text-coral' : '' }}">Chuyện Nhà Chill</a>
            <a href="{{ route('post.index') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium uppercase tracking-wider text-sm transition-colors {{ request()->routeIs('post.index') ? 'bg-coral/20 text-coral' : '' }}">Bài viết</a>
            <a href="{{ route('contact') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium uppercase tracking-wider text-sm transition-colors {{ request()->routeIs('contact') ? 'bg-coral/20 text-coral' : '' }}">Liên hệ</a>
        </nav>

        <div class="mt-6 border-t border-white/10 pt-6 flex flex-col gap-2">
            @auth
                <a href="{{ route('user.profile') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Tài khoản của tôi
                </a>
                <a href="{{ route('user.orders') }}" class="py-3 px-4 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Đơn hàng của tôi
                </a>
                <a href="{{ route('logout') }}" class="py-3 px-4 rounded-xl text-red-400 hover:text-white hover:bg-red-500/20 font-medium text-sm transition-colors">Đăng xuất</a>
            @else
                <a href="{{ route('login') }}" class="py-3 px-4 rounded-xl bg-coral text-white hover:bg-[#d5523b] font-bold text-sm transition-colors text-center">Đăng nhập / Đăng ký</a>
            @endauth
        </div>
    </div>
</div>

<script>
    function openMobileMenu() {
        document.getElementById('mobile-menu').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileMenu() {
        document.getElementById('mobile-menu').classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>