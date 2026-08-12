@extends('admin.layouts.app')

@section('title', 'Quản lý Sản Phẩm - Chill Chill Admin')
@section('page_title', 'Danh sách Sản phẩm')

@section('content')
{{-- Thông báo thành công khi Thêm/Sửa/Xóa --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 font-medium">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 font-medium">
        {{ session('error') }}
    </div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    {{-- Form Tìm Kiếm --}}
    <form action="{{ route('products.index') }}" method="GET" class="w-full sm:w-80 flex gap-2">
        <div class="relative w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc ID sản phẩm..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#e8634a] text-sm text-gray-800">
            @if(request('search'))
                <a href="{{ route('products.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 font-bold" title="Xóa tìm kiếm">✕</a>
            @endif
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition text-sm font-medium">Tìm</button>
    </form>
    
    <a href="{{ route('products.create') }}" class="bg-[#e8634a] text-white px-6 py-2.5 rounded-xl hover:bg-[#d5523b] transition font-medium shadow-sm flex items-center justify-center gap-2">
        + Thêm sản phẩm mới
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                <th class="p-4 font-medium w-16">ID</th>
                <th class="p-4 font-medium w-24">Hình ảnh</th>
                <th class="p-4 font-medium">Tên sản phẩm</th>
                <th class="p-4 font-medium">Danh mục</th>
                <th class="p-4 font-medium">Giá bán</th>
                <th class="p-4 font-medium">Trạng thái</th>
                <th class="p-4 font-medium text-center">Hành động</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm">
            @foreach($products as $product)
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="p-4 font-medium text-gray-500">#{{ $product->product_id }}</td>
                <td class="p-4">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover border">
                </td>
                <td class="p-4 font-medium text-gray-900">{{ $product->name }}</td>
                <td class="p-4">{{ $product->category_name ?? 'Khác' }}</td>
                <td class="p-4 font-bold text-[#e8634a]">{{ number_format($product->price ?? 0, 0, ',', '.') }}đ</td>
                <td class="p-4">
                    @if($product->status == 1)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Đang bán</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Ngừng bán</span>
                    @endif
                </td>
                <td class="p-4">
                    <div class="flex items-center justify-center gap-2">
                        @if(Route::has('products.toggle_featured'))
                        <form action="{{ route('products.toggle_featured', $product->product_id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" 
                                    class="text-xs font-bold px-2.5 py-1 rounded-lg transition {{ !empty($product->is_featured) ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-gray-100 text-gray-600 hover:bg-amber-50 hover:text-amber-700' }}"
                                    title="Ghim món này hiển thị trên Banner Hero Trang Chủ">
                                {{ !empty($product->is_featured) ? '⭐ Đang ghim Banner' : 'Ghim Banner' }}
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('products.edit', $product->product_id) }}" class="text-blue-600 hover:text-blue-800 font-medium px-3 py-1 bg-blue-50 rounded-lg">Sửa</a>
                        
                        <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium px-3 py-1 bg-red-50 rounded-lg">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- Phân trang --}}
    <div class="p-4 border-t">
        {{ $products->links() }}
    </div>
</div>
@endsection