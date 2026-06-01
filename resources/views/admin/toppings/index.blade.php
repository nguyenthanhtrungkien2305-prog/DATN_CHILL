<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Topping - Chill Chill Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">ADMIN CHILL</h1>
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
        </nav>
        <div class="p-4 border-t border-white/10">
            <a href="/" class="block text-center px-4 py-2 bg-white/10 rounded hover:bg-white/20 transition">← Về trang web</a>
        </div>
    </aside>

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Danh sách Topping</h2>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-500">Quản lý các loại topping bán kèm món nước.</p>
                <a href="{{ route('toppings.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm Topping mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 font-medium w-20">ID</th>
                            
                            {{-- THÊM THẺ TH NÀY --}}
                            <th class="p-4 font-medium w-32">Hình ảnh</th>
                            
                            <th class="p-4 font-medium">Tên Topping</th>
                            <th class="p-4 font-medium">Giá tiền</th>
                            <th class="p-4 font-medium text-center w-40">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach($toppings as $top)
                        <tr class="border-b hover:bg-gray-50 transition">
                            {{-- Sử dụng topping_id --}}
                            <td class="p-4">{{ $top->topping_id }}</td>
                            
                            {{-- THÊM THẺ TD NÀY ĐỂ HIỂN THỊ ẢNH --}}
                            <td class="p-4">
                                <img src="{{ $top->image ?? 'https://via.placeholder.com/150' }}" alt="{{ $top->name }}" class="w-16 h-12 rounded object-cover border">
                            </td>
                            
                            <td class="p-4 font-medium text-gray-900 text-base">{{ $top->name }}</td>
                            <td class="p-4 font-bold text-[#e8634a]">{{ number_format($top->price, 0, ',', '.') }} đ</td>
                            <td class="p-4 flex justify-center gap-4 mt-2">
                                {{-- Sử dụng topping_id --}}
                                <a href="{{ route('toppings.edit', $top->topping_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                                
                                {{-- Sử dụng topping_id --}}
                                <form action="{{ route('toppings.destroy', $top->topping_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa topping này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t">{{ $toppings->links() }}</div>
            </div>
        </div>
    </main>
</body>
</html>