<aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
    <div class="h-16 flex items-center justify-center border-b border-white/10">
        <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">ADMIN CHILL</h1>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">📊</span> <span>Tổng quan</span>
        </a>

        <a href="{{ route('products.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('products.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">☕</span> <span>Quản lý Sản phẩm</span>
        </a>

        <a href="{{ route('categories.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('categories.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">📁</span> <span>Quản lý Danh mục</span>
        </a>

        <a href="{{ route('toppings.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('toppings.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">🍡</span> <span>Quản lý Topping</span>
        </a>

        <a href="{{ route('vouchers.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('vouchers.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">🎟️</span> <span>Quản lý Voucher</span>
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap hover:bg-white/10 opacity-50 cursor-not-allowed">
            <span class="text-xl">🛒</span> <span>Quản lý Đơn hàng</span>
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap hover:bg-white/10 opacity-50 cursor-not-allowed">
            <span class="text-xl">👥</span> <span>Quản lý Người dùng</span>
        </a>

        <a href="{{ route('feedbacks.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('feedbacks.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">✉️</span> <span>Quản lý Phản hồi</span>
        </a>

        <a href="{{ route('admin.chats.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.chats.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">💬</span> <span>Hỗ trợ trực tuyến</span>
        </a>

        <a href="{{ route('admin.ai.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.ai.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
            <span class="text-xl">🤖</span> <span>Trợ lý AI Admin</span>
        </a>
    </nav>
    <a href="/" class="block px-4 py-3 rounded-lg hover:bg-white/10 transition mt-auto border-t border-white/10">← Về trang web</a>
</aside>
