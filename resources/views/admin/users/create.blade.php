@extends('admin.layouts.app')

@section('title', 'Thêm Người Dùng - Chill Chill Admin')
@section('page_title', 'Thêm Người Dùng Mới')

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại danh sách</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: nguyenvanan" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Ví dụ: 0987654321" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Ví dụ: an.nguyen@example.com" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] text-gray-700 bg-white">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Khách hàng (User)</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Điểm số tích lũy <span class="text-red-500">*</span></label>
                    <input type="number" name="point" value="{{ old('point', 0) }}" min="0" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                <textarea name="address" rows="3" placeholder="Ví dụ: 123 Đường Nguyễn Huệ, Quận 1, TP. HCM" 
                          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">{{ old('address') }}</textarea>
            </div>

            <div class="pt-6 flex gap-4 justify-end">
                <a href="{{ route('users.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Hủy</a>
                <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium transition shadow-sm">Lưu người dùng</button>
            </div>
        </form>
    </div>
</div>
@endsection
