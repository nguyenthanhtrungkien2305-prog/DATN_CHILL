<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản Phẩm - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">ADMIN CHILL</h1>
        </div>
               <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
          <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->is('admin') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">📊</span> <span>Tổng quan</span>
            </a>
            
            {{-- Quản lý Sản phẩm --}}
            <a href="{{ route('products.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('products.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">☕</span> <span>Quản lý Sản phẩm</span>
            </a>
            
            {{-- Quản lý Danh mục --}}
            <a href="{{ route('categories.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('categories.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">📁</span> <span>Quản lý Danh mục</span>
            </a>

            {{-- Quản lý Topping --}}
            <a href="{{ route('toppings.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('toppings.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">🍡</span> <span>Quản lý Topping</span>
            </a>

            {{-- Quản lý Voucher --}}
            <a href="{{ route('vouchers.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('vouchers.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">🎟️</span> <span>Quản lý Voucher</span>
            </a>
            
            {{-- Quản lý Đơn hàng --}}
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap hover:bg-white/10">
                <span class="text-xl">🛒</span> <span>Quản lý Đơn hàng</span>
            </a>
            
            {{-- Quản lý Người dùng --}}
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap hover:bg-white/10">
                <span class="text-xl">👥</span> <span>Quản lý Người dùng</span>
            </a>
        </nav>
            <a href="/" class="block px-4 py-3 rounded-lg hover:bg-white/10 transition mt-auto border-t border-white/10">← Về trang web</a>
        </nav>
    </aside>

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Danh sách Sản phẩm</h2>
        </header>

        <div class="p-8">
            {{-- Thông báo thành công khi Thêm/Sửa/Xóa --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                {{-- Form Tìm Kiếm --}}
<form action="{{ route('products.index') }}" method="GET" class="w-1/3 flex gap-2">
    <div class="relative w-full">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc ID sản phẩm..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
        
        {{-- Nút X (Clear) hiện ra khi có từ khóa --}}
        @if(request('search'))
            <a href="{{ route('products.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 font-bold" title="Xóa tìm kiếm">✕</a>
        @endif
    </div>
    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Tìm</button>
</form>
                <a href="{{ route('products.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm sản phẩm mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 font-medium">ID</th>
                            <th class="p-4 font-medium">Hình ảnh</th>
                            <th class="p-4 font-medium">Tên sản phẩm</th>
                            <th class="p-4 font-medium">Danh mục</th>
                            <th class="p-4 font-medium">Trạng thái</th>
                            <th class="p-4 font-medium text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach($products as $product)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4">{{ $product->product_id }}</td>
                            <td class="p-4">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover border">
                            </td>
                            <td class="p-4 font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="p-4">{{ $product->category_name }}</td>
                            <td class="p-4">
                                @if($product->status == 1)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Đang bán</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Ngừng bán</span>
                                @endif
                            </td>
                            <td class="p-4 flex justify-center gap-3">
                                {{-- Nút Sửa --}}
                                <a href="{{ route('products.edit', $product->product_id) }}" class="text-blue-500 hover:text-blue-700">Sửa</a>
                                
                                {{-- Nút Xóa (Phải dùng Form vì method DELETE) --}}
                                <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{-- Phân trang của Laravel --}}
                <div class="p-4 border-t">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </main>

</body>
</html>