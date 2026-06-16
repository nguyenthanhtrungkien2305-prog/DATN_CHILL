@extends('admin.layouts.app')

@section('title', 'Quản lý Người Dùng')

@section('content')
<div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
        <h3 class="text-lg font-black text-gray-800 uppercase tracking-wider">Danh sách tài khoản</h3>
        <span class="bg-gray-100 text-gray-600 px-4 py-1.5 rounded-lg text-sm font-bold">Tổng: {{ count($users) }} người</span>
    </div>

    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="p-4 pl-6 w-20">ID</th>
                    <th class="p-4">Tài khoản / Người dùng</th>
                    <th class="p-4 text-center w-48">Vai trò phân quyền</th>
                    <th class="p-4 pr-6 text-right w-32">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4 pl-6 font-black text-gray-900">{{ $user->user_id }}</td>
                    
                    <td class="p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-coral/10 flex items-center justify-center text-coral font-black text-lg shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-base">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5">Tham gia: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới' }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="p-4 text-center">
                        <form action="{{ route('admin.users.update_role', $user->user_id) }}" method="POST" class="m-0">
                            @csrf
                            <select name="role" onchange="this.form.submit()" 
                                class="w-full rounded-lg px-3 py-2 text-sm font-bold border border-gray-200 focus:outline-none focus:ring-2 focus:ring-coral/50 transition-colors cursor-pointer text-center
                                {{ $user->role == 'admin' ? 'bg-red-50 text-red-600 border-red-100' : ($user->role == 'staff' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-600') }}">
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Khách hàng</option>
                                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </form>
                    </td>
                    
                    <td class="p-4 pr-6 text-right">
                        <button class="text-red-400 hover:text-red-600 font-bold text-sm px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                            Xóa
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection