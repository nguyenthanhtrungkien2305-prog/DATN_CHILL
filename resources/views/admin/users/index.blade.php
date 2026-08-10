@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng - Chill Chill Admin')
@section('page_title', 'Danh sách Người dùng')

@section('content')
{{-- Thông báo --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm">{{ session('error') }}</div>
@endif

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <p class="text-gray-500">Quản lý tài khoản khách hàng, nhân viên và quản trị viên của cửa hàng.</p>
</div>

{{-- Thanh bộ lọc --}}
<div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
    <form action="{{ Route::has('admin.users.index') ? route('admin.users.index') : route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <div class="flex-1 relative">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm theo tên, email, số điện thoại..." 
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8634a] transition">
        </div>
        
        <div class="w-full md:w-48">
            <select name="role" onchange="this.form.submit()" 
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8634a] transition text-gray-700">
                <option value="">Tất cả Vai trò</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Khách hàng (User)</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition">
                Lọc
            </button>
            @if(request('search') || request('role'))
                <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                    <th class="p-4 font-medium w-16 text-center">ID</th>
                    <th class="p-4 font-medium">Tài khoản / Người dùng</th>
                    <th class="p-4 font-medium">Email</th>
                    <th class="p-4 font-medium w-48 text-center">Vai trò phân quyền</th>
                    <th class="p-4 font-medium text-center w-36">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-center font-bold text-gray-500">#{{ $u->user_id }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#e8634a]/10 text-[#e8634a] font-bold flex items-center justify-center text-sm">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                <div class="text-xs text-gray-400">{{ $u->phone ?? 'Chưa cập nhật SĐT' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-gray-600 font-mono text-xs">{{ $u->email ?: '-' }}</td>
                    <td class="p-4 text-center">
                        @if(Route::has('admin.users.update_role'))
                        <form action="{{ route('admin.users.update_role', $u->user_id) }}" method="POST" class="m-0">
                            @csrf
                            <select name="role" onchange="this.form.submit()" 
                                class="w-full rounded-xl px-3 py-1.5 text-xs font-bold border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#e8634a]/50 transition-colors cursor-pointer text-center
                                {{ $u->role == 'admin' ? 'bg-red-50 text-red-600 border-red-100' : ($u->role == 'staff' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100') }}">
                                <option value="user" {{ $u->role == 'user' ? 'selected' : '' }}>Khách hàng</option>
                                <option value="staff" {{ $u->role == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </form>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $u->role == 'admin' ? 'bg-red-50 text-red-600' : ($u->role == 'staff' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if(Route::has('users.toggle_lock') && auth()->id() != $u->user_id)
                        <form action="{{ route('users.toggle_lock', $u->user_id) }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn {{ $u->is_locked ? 'mở khóa' : 'khóa' }} tài khoản này?');">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-xs font-bold rounded-full transition {{ $u->is_locked ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                {{ $u->is_locked ? '🔒 Đã khóa' : '✅ Hoạt động' }}
                            </button>
                        </form>
                        @else
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $u->is_locked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $u->is_locked ? '🔒 Đã khóa' : '✅ Hoạt động' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400 italic">
                        Không tìm thấy người dùng phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Phân trang --}}
    @if(method_exists($users, 'hasPages') && $users->hasPages())
    <div class="p-4 border-t">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
