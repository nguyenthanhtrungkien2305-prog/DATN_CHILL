<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Chill Chill')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Plus Jakarta Sans', 'Be Vietnam Pro', 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden antialiased relative">

    {{-- BACKDROP DÙNG CHO MOBILE SIDEBAR --}}
    <div id="admin-sidebar-backdrop" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity"></div>

    {{-- SIDEBAR (Responsive Slide-over Drawer trên Mobile & Tablet) --}}
    <aside id="admin-sidebar" class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0 fixed lg:static top-0 bottom-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-full">
        <div class="h-16 flex items-center justify-between px-4 border-b border-white/10 shrink-0">
            <h1 class="text-lg font-bold tracking-widest text-[#e8634a]">CHILL CHILL ADMIN</h1>
            <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        {{-- Thêm custom-scrollbar để menu cuộn mượt mà nếu thêm nhiều mục --}}
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
            
            {{-- Tổng quan --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.dashboard') || request()->is('admin') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Tổng quan</span>
            </a>

            {{-- Bán hàng (POS) - Trực tiếp không cần Check-in --}}
            <a href="{{ route('admin.pos') }}" target="_blank"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Bán hàng (POS)</span>
            </a>
            
            {{-- Quản lý Sản phẩm --}}
            <a href="{{ route('products.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('products.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Sản phẩm</span>
            </a>

            {{-- Theo dõi lượt bán & Giảm giá kích cầu --}}
            <a href="{{ route('admin.product_sales.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.product_sales.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Lượt bán & Giảm giá</span>
            </a>

            {{-- Quản lý Combo --}}
            <a href="{{ route('combos.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('combos.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Combo</span>
            </a>
            
            {{-- Quản lý Danh mục --}}
            <a href="{{ route('categories.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('categories.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Danh mục</span>
            </a>

            {{-- Quản lý Bài viết --}}
            <a href="{{ route('posts.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('posts.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Bài viết</span>
            </a>

            {{-- Quản lý Voucher --}}
            <a href="{{ route('vouchers.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('vouchers.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Voucher</span>
            </a>

            {{-- Quản lý Banner --}}
            <a href="{{ route('banners.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('banners.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Banner</span>
            </a>
            
            {{-- Quản lý Đơn hàng --}}
            <a href="{{ route('admin.orders.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap hover:bg-white/10">
                <span>Quản lý Đơn hàng</span>
            </a>

            {{-- QUẢN LÝ NHÂN VIÊN --}}
            <a href="{{ route('admin.staff.manager') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.staff.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Nhân viên</span>
            </a>
            
            {{-- Quản lý Người dùng --}}
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Người dùng</span>
            </a>

            {{-- Quản lý Phản hồi --}}
            <a href="{{ route('feedbacks.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('feedbacks.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Quản lý Phản hồi</span>
            </a>

            {{-- Hỗ trợ trực tuyến --}}
            <a href="{{ route('admin.chats.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.chats.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span>Hỗ trợ trực tuyến</span>
            </a>
            
        </nav>
        <div class="p-4 border-t border-white/10 shrink-0">
            <a href="/" class="block text-center px-4 py-2 bg-white/10 rounded hover:bg-white/20 transition text-sm">← Về trang web</a>
        </div>
    </aside>

    {{-- MAIN CONTENT CONTAINER --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full bg-gray-100 relative">
        {{-- TOPBAR CHO MOBILE/TABLET --}}
        <header class="lg:hidden bg-[#2B2623] text-white h-14 px-4 flex items-center justify-between shrink-0 shadow-md">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleAdminSidebar()" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-sm tracking-wider text-[#e8634a]">CHILL CHILL ADMIN</span>
            </div>
            <a href="/" class="text-xs bg-white/10 px-2.5 py-1 rounded text-white hover:bg-white/20">Trang chủ</a>
        </header>

        @yield('content')
    </main>

    {{-- SCRIPT TOGGLE ADMIN SIDEBAR --}}
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>

    {{-- CSS thanh cuộn tinh tế cho Sidebar --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.3); }
    </style>
</body>
</html>