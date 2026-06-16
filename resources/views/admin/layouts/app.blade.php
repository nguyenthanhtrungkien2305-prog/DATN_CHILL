<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Chill Chill</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR (Menu bên trái) --}}
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">CHILL CHILL ADMIN</h1>
        </div>
       <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            {{-- Tổng quan --}}
            <a href="#" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->is('admin') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                📊 Tổng quan
            </a>
            
            {{-- Quản lý Sản phẩm --}}
            <a href="{{ route('products.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                ☕ Quản lý Sản phẩm
            </a>
            
            {{-- Quản lý Danh mục --}}
            <a href="{{ route('categories.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                📁 Quản lý Danh mục
            </a>

            {{-- Quản lý Topping (Mới thêm) --}}
            <a href="{{ route('toppings.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('toppings.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                🍡 Quản lý Topping
            </a>

            {{-- Quản lý Voucher --}}
            <a href="{{ route('vouchers.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('vouchers.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                🎟️ Quản lý Voucher
            </a>
            
            {{-- Quản lý Đơn hàng --}}
            <a href="#" 
               class="block px-4 py-3 rounded-lg transition-colors hover:bg-white/10">
                🛒 Quản lý Đơn hàng
            </a>
            
            {{-- Quản lý Người dùng --}}
            <a href="#" 
               class="block px-4 py-3 rounded-lg transition-colors hover:bg-white/10">
                👥 Quản lý Người dùng
            </a>

            {{-- Quản lý Phản hồi --}}
            <a href="{{ route('feedbacks.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('feedbacks.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                ✉️ Quản lý Phản hồi
            </a>

            {{-- Hỗ trợ trực tuyến --}}
            <a href="{{ route('admin.chats.index') }}" 
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.chats.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                💬 Hỗ trợ trực tuyến
            </a>
        </nav>
        <div class="p-4 border-t border-white/10">
            <a href="/" class="block text-center px-4 py-2 bg-white/10 rounded hover:bg-white/20 transition">← Về trang web</a>
        </div>
    </aside>

    {{-- MAIN CONTENT (Nội dung chính) --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
    <div class="p-6 md:p-8 w-full flex-1">
        @yield('content')
    </div>
</main>
</body>
</html>