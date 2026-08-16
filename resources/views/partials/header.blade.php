<header id="navbar" class="fixed top-3 left-0 right-0 z-50 transition-all duration-300 px-3 sm:px-6">
    <div class="max-w-7xl mx-auto">
        {{-- Transparent Floating Header Container --}}
        <div class="bg-transparent px-1 sm:px-2 py-1 flex items-center justify-between transition-all duration-300">
            
            {{-- ========================================== --}}
            {{-- BÊN TRÁI: Logo & Tên thương hiệu Nghệ thuật (Nằm ngoài box) --}}
            {{-- ========================================== --}}
            <div class="shrink-0 flex items-center">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="/images/logo1.png" alt="Chill Chill Logo" class="h-14 sm:h-[72px] md:h-[82px] w-auto object-contain rounded-full border-2 border-white/90 shadow-md group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null; this.src='/images/logo1.jpg';" />
                    <span class="font-script font-bold text-3xl sm:text-4xl text-espresso tracking-wide hidden sm:inline group-hover:text-coral transition-colors drop-shadow-[0_2px_10px_rgba(255,255,255,0.95)]">Chill Chill</span>
                </a>
            </div>

            {{-- ========================================== --}}
            {{-- Ở GIỮA: Floating Nav Bar (Menu Đồ Uống) --}}
            {{-- ========================================== --}}
            <div class="hidden lg:flex items-center bg-white/90 backdrop-blur-md rounded-full px-3 py-1.5 border border-white/80 shadow-md">
                <nav class="flex items-center gap-1 sm:gap-1.5">
                    {{-- Trang chủ --}}
                    <a href="/" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->is('/') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Trang chủ
                    </a>
                    
                    {{-- Thực đơn / Sản phẩm --}}
                    <a href="{{ route('product.index') }}" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->routeIs('product.index') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Thực đơn
                    </a>

                    {{-- Gói Combo --}}
                    <a href="{{ route('combo.index') }}" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->routeIs('combo.index') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Gói Combo
                    </a>

                    {{-- Chuyện Nhà Chill --}}
                    <a href="{{ route('post.story') }}" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->routeIs('post.story') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Giới thiệu
                    </a>
                    
                    {{-- Bài viết --}}
                    <a href="{{ route('post.index') }}" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->routeIs('post.index') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Bài viết
                    </a>
                    
                    {{-- Liên hệ --}}
                    <a href="{{ route('contact') }}" 
                       class="whitespace-nowrap transition-all duration-300 text-xs uppercase tracking-wider inline-block px-4 py-2 rounded-full
                       {{ request()->routeIs('contact') ? 'bg-coral text-white font-bold shadow-md shadow-coral/25' : 'text-espresso/70 font-semibold hover:text-coral hover:bg-white/60' }}">
                       Liên hệ
                    </a>
                </nav>
            </div>

            {{-- ========================================== --}}
            {{-- BÊN PHẢI: Nút Giỏ hàng & Đăng nhập --}}
            {{-- ========================================== --}}
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/80 shadow-md">
                
                {{-- Nút Giỏ hàng --}}
                <a href="{{ route('cart.index') }}" class="w-9 h-9 shrink-0 rounded-full bg-white border border-gray-200/80 flex items-center justify-center text-espresso hover:text-coral hover:border-coral transition-colors relative shadow-2xs">
                    @php
                        $cartCount = 0;
                        $cartItems = session('cart');
                        if (!$cartItems && auth()->check()) {
                            $cartCacheKey = 'cart:u:' . auth()->id();
                            $cartItems = \Illuminate\Support\Facades\Cache::get($cartCacheKey, []);
                        }
                        if ($cartItems) {
                            foreach ($cartItems as $item) {
                                $cartCount += $item['quantity'] ?? 0;
                            }
                        }
                    @endphp
                    
                    <span id="cart-badge" class="absolute -top-1 -right-1 bg-coral text-white text-[10px] font-extrabold rounded-full px-1.5 py-0.2 shadow-sm border border-white {{ $cartCount > 0 ? '' : 'hidden' }}">
                        {{ $cartCount }}
                    </span>
                    
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </a>

                {{-- Nút Tài Khoản --}}
                @auth
                    <a href="{{ route('user.profile') }}" title="Tài khoản của tôi" class="hidden sm:flex shrink-0 items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200/80 text-espresso hover:border-coral transition-all shadow-2xs text-xs font-semibold">
                        <div class="w-6 h-6 rounded-full bg-coral/20 text-coral flex items-center justify-center text-[10px] font-bold overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <span class="max-w-[85px] truncate text-espresso font-bold">{{ Auth::user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" title="Đăng nhập tài khoản" class="hidden sm:flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-gray-200/80 text-espresso text-xs font-bold hover:border-coral transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <span>Đăng nhập</span>
                    </a>
                @endauth

                {{-- Nút Admin (nếu có quyền Admin) --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="hidden xl:flex shrink-0 px-3 py-1.5 rounded-full bg-espresso text-white font-bold text-xs hover:bg-espresso-light transition-colors border border-espresso/50 shadow-sm">
                        Admin &rarr;
                    </a>
                @endif

                {{-- Nút Hamburger Mobile --}}
                <button id="hamburger-btn" onclick="openMobileMenu()" class="lg:hidden shrink-0 w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-espresso hover:text-coral transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- Mobile Menu Drawer --}}
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-espresso/50 backdrop-blur-xs" onclick="closeMobileMenu()"></div>
    <div class="absolute top-0 left-0 h-full w-72 bg-white flex flex-col pt-16 pb-8 px-6 shadow-2xl overflow-y-auto">
        <button onclick="closeMobileMenu()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-espresso hover:bg-coral hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <form action="{{ route('product.index') }}" method="GET" class="flex items-center gap-2 mb-6">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm sản phẩm..." class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-full text-espresso text-xs placeholder-espresso/40 focus:outline-none focus:border-coral" />
            <button type="submit" class="w-8 h-8 rounded-full bg-coral flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </button>
        </form>

        <nav class="flex flex-col gap-1">
            <a href="/" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->is('/') ? 'bg-coral/10 text-coral font-bold' : '' }}">Trang chủ</a>
            <a href="{{ route('product.index') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->routeIs('product.index') ? 'bg-coral/10 text-coral font-bold' : '' }}">Thực đơn</a>
            <a href="{{ route('combo.index') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->routeIs('combo.index') ? 'bg-coral/10 text-coral font-bold' : '' }}">Gói Combo</a>
            <a href="{{ url('/#best-sellers') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors">Món bán chạy</a>
            <a href="{{ route('post.story') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->routeIs('post.story') ? 'bg-coral/10 text-coral font-bold' : '' }}">Giới thiệu</a>
            <a href="{{ route('post.index') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->routeIs('post.index') ? 'bg-coral/10 text-coral font-bold' : '' }}">Bài viết</a>
            <a href="{{ route('contact') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium uppercase tracking-wider text-xs transition-colors {{ request()->routeIs('contact') ? 'bg-coral/10 text-coral font-bold' : '' }}">Liên hệ</a>
        </nav>

        <div class="mt-6 border-t border-gray-100 pt-6 flex flex-col gap-2">
            @auth
                <a href="{{ route('user.profile') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium text-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Tài khoản của tôi
                </a>
                <a href="{{ route('user.orders') }}" class="py-2.5 px-4 rounded-xl text-espresso/80 hover:text-coral hover:bg-cream/40 font-medium text-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Đơn hàng của tôi
                </a>
                <a href="{{ route('logout') }}" class="py-2.5 px-4 rounded-xl text-red-500 hover:bg-red-50 font-medium text-xs transition-colors">Đăng xuất</a>
            @else
                <a href="{{ route('login') }}" class="py-2.5 px-4 rounded-xl bg-coral text-white text-center font-bold text-xs shadow-md">Đăng nhập / Đăng ký</a>
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