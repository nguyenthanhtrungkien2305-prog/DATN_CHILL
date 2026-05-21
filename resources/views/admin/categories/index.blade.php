<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
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
            <h2 class="text-xl font-semibold text-gray-800">Danh sách Danh mục</h2>
        </header>

        <div class="p-8">
            {{-- Thông báo --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-500">Quản lý các nhóm đồ uống, thức ăn của cửa hàng.</p>
                <a href="{{ route('categories.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm danh mục mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 font-medium w-20">ID</th>
                            <th class="p-4 font-medium w-32">Hình ảnh</th>
                            <th class="p-4 font-medium">Tên danh mục</th>
                            <th class="p-4 font-medium text-center w-40">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach($categories as $cat)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4">{{ $cat->category_id }}</td>
                            <td class="p-4">
                                <img src="{{ $cat->image ?? 'https://via.placeholder.com/150' }}" alt="{{ $cat->name }}" class="w-16 h-12 rounded object-cover border">
                            </td>
                            <td class="p-4 font-medium text-gray-900 text-base">{{ $cat->name }}</td>
                            <td class="p-4 flex justify-center gap-4 mt-1">
                                <a href="{{ route('categories.edit', $cat->category_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                                
                                <form action="{{ route('categories.destroy', $cat->category_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{-- Phân trang --}}
                <div class="p-4 border-t">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </main>

</body>
</html>