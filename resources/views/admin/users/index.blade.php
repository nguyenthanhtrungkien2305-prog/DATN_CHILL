@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng - Chill Chill Admin')
@section('page_title', 'Danh sách Người dùng')

@section('content')
{{-- Thông báo --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm">{{ session('error') }}</div>
@endif

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <p class="text-gray-500">Quản lý tài khoản khách hàng, nhân viên và quản trị viên của cửa hàng.</p>
    <a href="{{ route('users.create') }}" class="bg-[#e8634a] text-white px-6 py-2.5 rounded-lg hover:bg-[#d5523b] transition font-medium shadow-sm flex items-center gap-2 whitespace-nowrap">
        <span>➕</span> Thêm người dùng mới
    </a>
</div>

{{-- Thanh bộ lọc --}}
<div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm mb-6">
    <form action="{{ route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm theo tên đăng nhập, email, số điện thoại..." 
                   class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#e8634a] transition">
        </div>
        
        <div class="w-full md:w-48">
            <select name="role" onchange="this.form.submit()" 
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#e8634a] transition text-gray-700">
                <option value="">Tất cả Vai trò</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Khách hàng (User)</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">
                Lọc
            </button>
            @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center whitespace-nowrap">
                    Xóa lọc
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-sm border-b whitespace-nowrap">
                    <th class="p-4 font-medium w-16 text-center">ID</th>
                    <th class="p-4 font-medium w-28 text-center">Ảnh đại diện</th>
                    <th class="p-4 font-medium">Tên đăng nhập</th>
                    <th class="p-4 font-medium">Email</th>
                    <th class="p-4 font-medium">Số điện thoại</th>
                    <th class="p-4 font-medium w-36 text-center">Vai trò</th>
                    <th class="p-4 font-medium w-28 text-center">Điểm số</th>
                    <th class="p-4 font-medium text-center w-36">Trạng thái</th>
                    <th class="p-4 font-medium text-center w-40">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($users as $u)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4 text-center text-gray-400 font-mono whitespace-nowrap">{{ $u->user_id }}</td>
                    <td class="p-4 text-center whitespace-nowrap">
                        <div class="inline-flex justify-center">
                            <img src="{{ $u->avatar ? asset($u->avatar) : 'https://api.dicebear.com/7.x/adventurer-neutral/svg?seed=' . urlencode($u->name) }}" 
                                 alt="{{ $u->name }}" 
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                        </div>
                    </td>
                    <td class="p-4 font-medium text-gray-900 text-base whitespace-nowrap">
                        {{ $u->name }}
                        @if(auth()->id() == $u->user_id || auth()->user()->user_id == $u->user_id)
                            <span class="ml-1 text-xs bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 font-medium whitespace-nowrap">Bạn</span>
                        @endif
                    </td>
                    <td class="p-4 text-gray-600 font-mono whitespace-nowrap">{{ $u->email ?: '-' }}</td>
                    <td class="p-4 text-gray-600 font-mono whitespace-nowrap">{{ $u->phone ?: '-' }}</td>
                    <td class="p-4 text-center whitespace-nowrap">
                        @if($u->role === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 shadow-sm whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Quản trị viên
                            </span>
                        @elseif($u->role === 'staff')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Nhân viên
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Khách hàng
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-center font-semibold text-amber-600 whitespace-nowrap">{{ number_format($u->point) }}</td>
                    <td class="p-4 text-center whitespace-nowrap">
                        @if($u->is_locked)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100 shadow-sm whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã khóa
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100 shadow-sm whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Hoạt động
                            </span>
                        @endif
                    </td>
                    <td class="p-4 whitespace-nowrap">
                        <div class="flex justify-center items-center gap-3">
                            @if(auth()->id() != $u->user_id && auth()->user()->user_id != $u->user_id)
                            <form action="{{ route('users.toggle_lock', $u->user_id) }}" method="POST" 
                                  onsubmit="return confirm('Bạn có chắc chắn muốn {{ $u->is_locked ? 'mở khóa' : 'khóa' }} tài khoản này?');">
                                @csrf
                                <button type="submit" class="{{ $u->is_locked ? 'text-emerald-600 hover:text-emerald-800' : 'text-red-600 hover:text-red-800' }} font-medium flex items-center gap-1 transition whitespace-nowrap">
                                    {{ $u->is_locked ? '🔓' : '🔒' }} <span>{{ $u->is_locked ? 'Mở khóa' : 'Khóa' }}</span>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-300 select-none cursor-not-allowed font-medium flex items-center gap-1 whitespace-nowrap" title="Không thể tự khóa bản thân">
                                🔒 <span class="line-through text-gray-300">Khóa</span>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-gray-400 whitespace-nowrap">
                        Không tìm thấy người dùng phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Phân trang --}}
    @if($users->hasPages())
    <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
        <div class="text-xs text-gray-500">
            Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} trong tổng số {{ $users->total() }} người dùng
        </div>
        <div>
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
