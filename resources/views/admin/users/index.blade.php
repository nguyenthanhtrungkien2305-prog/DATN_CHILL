<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người Dùng - Chill Chill Admin</title>
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
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
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
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-[#e8634a] text-white font-medium' : 'hover:bg-white/10' }}">
                <span class="text-xl">👥</span> <span>Quản lý Người dùng</span>
            </a>
        </nav>
        <a href="/" class="block px-4 py-3 rounded-lg hover:bg-white/10 transition mt-auto border-t border-white/10">← Về trang web</a>
    </aside>

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Danh sách Người dùng</h2>
        </header>

        <div class="p-8">
            {{-- Thông báo thành công --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                {{-- Form Tìm Kiếm Người Dùng --}}
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-1/3 flex gap-2">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email hoặc ID..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">

                        @if(request('search'))
                            <a href="{{ route('admin.users.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 font-bold" title="Xóa tìm kiếm">✕</a>
                        @endif
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Tìm</button>
                </form>

                <a href="{{ route('admin.users.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm tài khoản mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 font-medium">ID</th>
                            <th class="p-4 font-medium">Avatar</th>
                            <th class="p-4 font-medium">Họ và tên</th>
                            <th class="p-4 font-medium">Email</th>
                            <th class="p-4 font-medium">Vai trò</th>
                            <th class="p-4 font-medium">Trạng thái</th>
                            <th class="p-4 font-medium text-center">Hành động trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($users as $user)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4">{{ $user->user_id }}</td>
                            <td class="p-4">
                                <img src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e8634a&color=fff' }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border">
                            </td>
                            <td class="p-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">
                                @if($user->role == 'admin')
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">Quản trị viên</span>
                                @elseif($user->role == 'staff')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Nhân viên</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Khách hàng</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if(($user->status ?? 1) == 1)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Hoạt động</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Bị khóa</span>
                                @endif
                            </td>
                            <td class="p-4 flex justify-center">
    {{-- Form gửi dữ liệu lên hàm destroy của controller để đảo trạng thái --}}
    <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn {{ ($user->status ?? 1) == 1 ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản này?');">
        @csrf
        @method('DELETE')

        @if(($user->status ?? 1) == 1)
            <button type="submit" class="text-amber-600 hover:text-amber-800 font-medium bg-amber-50 hover:bg-amber-100 px-4 py-1.5 rounded-lg border border-amber-200 transition text-xs flex items-center gap-1">
                🔒 Khóa tài khoản
            </button>
        @else
            <button type="submit" class="text-green-600 hover:text-green-800 font-medium bg-green-50 hover:bg-green-100 px-4 py-1.5 rounded-lg border border-green-200 transition text-xs flex items-center gap-1">
                🔓 Mở khóa
            </button>
        @endif
    </form>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400 bg-gray-50/50">
                                Không tìm thấy người dùng nào phù hợp.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4 border-t">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </main>

</body>
</html>
