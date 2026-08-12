<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Chill Chill')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ["Poppins", "sans-serif"],
              serif: ["Playfair Display", "serif"],
            },
            colors: {
              espresso: {
                DEFAULT: "#2B2623",
                light: "#423B37",
              },
              cream: {
                DEFAULT: "#FAF7F2",
                light: "#FFF8E7",
              },
              coral: {
                DEFAULT: "#E8634A",
                hover: "#D5523B",
              },
            },
          }
        }
      }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.4); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR (Menu bên trái đồng bộ) --}}
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-white/10 shrink-0">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">CHILL CHILL ADMIN</h1>
        </div>
        
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
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.orders.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
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
            <a href="/" class="block text-center px-4 py-2 bg-white/10 rounded hover:bg-white/20 transition">← Về trang web</a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col h-screen @yield('main_class', 'overflow-y-auto') w-full relative">
        @hasSection('page_title')
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">@yield('page_title')</h2>
            <div class="flex items-center gap-4">
                @yield('header_actions')
                <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
                <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
            </div>
        </header>
        @endif

        <div class="@yield('content_class', 'p-6 md:p-8 flex-1')">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>