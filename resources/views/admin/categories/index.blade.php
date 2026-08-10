@extends('admin.layouts.app')

@section('title', 'Quản lý Danh mục - Chill Chill Admin')
@section('page_title', 'Danh sách Danh mục')

@section('content')
{{-- Thông báo --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4">{{ session('error') }}</div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-gray-500">Quản lý các nhóm đồ uống, thức ăn của cửa hàng.</p>
    <a href="{{ route('categories.create') }}" class="bg-[#e8634a] text-white px-6 py-2.5 rounded-xl hover:bg-[#d5523b] transition font-medium shadow-sm flex items-center justify-center gap-2">
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
                <td class="p-4 font-medium">{{ $cat->category_id }}</td>
                <td class="p-4">
                    <img src="{{ $cat->image ?? 'https://via.placeholder.com/150' }}" alt="{{ $cat->name }}" class="w-16 h-12 rounded-lg object-cover border">
                </td>
                <td class="p-4 font-medium text-gray-900 text-base">{{ $cat->name }}</td>
                <td class="p-4 flex justify-center gap-3">
                    <a href="{{ route('categories.edit', $cat->category_id) }}" class="text-blue-600 hover:text-blue-800 font-medium px-3 py-1 bg-blue-50 rounded-lg">Sửa</a>
                    
                    <form action="{{ route('categories.destroy', $cat->category_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium px-3 py-1 bg-red-50 rounded-lg">Xóa</button>
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
@endsection