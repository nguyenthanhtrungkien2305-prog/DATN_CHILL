@extends('admin.layouts.app')

@section('title', 'Quản lý Người Dùng - Chill Chill Admin')

@section('content')
    {{-- Header của trang --}}
    <header class="hidden lg:flex h-16 bg-white shadow-sm items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Người Dùng</h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
        </div>
    </header>

    <div class="p-4 md:p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 text-sm font-medium">{{ session('error') }}</div>
        @endif

        <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Danh sách tài khoản</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Quản lý người dùng, phân quyền vai trò và khóa/mở khóa tài khoản.</p>
                </div>

                {{-- Ô và Nút Tìm Kiếm Người Dùng --}}
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:w-72">
                        <input type="text" name="keyword" id="user-search-input" value="{{ request('keyword') }}" 
                               placeholder="Tìm tên, SĐỐ, email..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#e8634a]">
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-700 transition shrink-0">
                        Tìm kiếm
                    </button>
                    @if(request('keyword'))
                        <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-red-500 underline shrink-0">Bỏ lọc</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                        <tr>
                            <th class="p-4 pl-6 w-16">ID</th>
                            <th class="p-4">Tài khoản / Người dùng</th>
                            <th class="p-4 text-center w-36">Trạng thái</th>
                            <th class="p-4 text-center w-48">Vai trò phân quyền</th>
                            <th class="p-4 pr-6 text-center w-40">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="users-table-body">
                        @forelse($users as $user)
                        @php
                            $isMainAdmin = ($user->user_id == 1);
                            $isLocked = !empty($user->is_locked);
                        @endphp
                        <tr class="user-row hover:bg-gray-50/80 transition-colors {{ $isLocked ? 'bg-red-50/20' : '' }}" 
                            data-name="{{ mb_strtolower($user->name) }}" 
                            data-phone="{{ $user->phone }}" 
                            data-email="{{ mb_strtolower($user->email ?? '') }}">
                            
                            <td class="p-4 pl-6 font-bold text-gray-900">{{ $user->user_id }}</td>
                            
                            <td class="p-4">
                                <div class="font-bold text-gray-800 text-base">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5">
                                    {{ $user->phone ?? $user->email ?? 'Tham gia: ' . ($user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') : 'Mới') }}
                                </div>
                            </td>

                            {{-- Trạng thái Tài khoản --}}
                            <td class="p-4 text-center">
                                @if($isLocked)
                                    <span class="bg-red-100 text-red-700 font-medium px-3 py-1 rounded-full text-xs inline-block">
                                        Đã bị khóa
                                    </span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 font-medium px-3 py-1 rounded-full text-xs inline-block">
                                        Hoạt động
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Phân quyền Vai trò --}}
                            <td class="p-4 text-center">
                                @if($isMainAdmin)
                                    <span class="inline-block w-full py-2 px-3 rounded-lg text-sm font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        Admin
                                    </span>
                                @else
                                    <form action="{{ route('admin.users.update_role', $user->user_id) }}" method="POST" class="m-0">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()" 
                                            class="w-full rounded-lg px-3 py-2 text-sm font-bold border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#e8634a]/50 transition-colors cursor-pointer text-center
                                            {{ $user->role == 'admin' ? 'bg-red-50 text-red-600 border-red-100' : ($user->role == 'staff' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-600') }}">
                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Khách hàng</option>
                                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            
                            {{-- Hành động khóa / mở khóa --}}
                            <td class="p-4 pr-6 text-center">
                                @if($isMainAdmin)
                                    <span class="text-xs font-medium text-gray-400 px-3 py-1.5 rounded-lg inline-block">
                                        Cố định
                                    </span>
                                @else
                                    <form action="{{ route('admin.users.toggle_lock', $user->user_id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc muốn {{ $isLocked ? 'mở khóa' : 'khóa' }} tài khoản {{ $user->name }}?');">
                                        @csrf
                                        <button type="submit" 
                                                class="font-medium text-xs px-3 py-1.5 rounded-lg transition-colors {{ $isLocked ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                            {{ $isLocked ? 'Mở khóa' : 'Khóa tài khoản' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-gray-400 text-sm">Không tìm thấy người dùng nào phù hợp.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Lọc nhanh trực tiếp khi gõ chữ
        document.getElementById('user-search-input')?.addEventListener('input', function() {
            const kw = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const phone = row.getAttribute('data-phone') || '';
                const email = row.getAttribute('data-email') || '';
                if (name.includes(kw) || phone.includes(kw) || email.includes(kw)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection