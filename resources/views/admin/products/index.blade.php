@extends('admin.layouts.app')

@section('title', 'Quản lý Sản Phẩm - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách Sản phẩm</h2>
    </header>

    <div class="p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <form action="{{ route('products.index') }}" method="GET" class="w-1/3 flex gap-2">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc ID sản phẩm..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
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
                        <th class="p-4 font-medium">Giá bán (Từ)</th>
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
                        <td class="p-4">{{ $product->category_name ?? 'Không có' }}</td>
                        
                        <td class="p-4 font-bold text-[#e8634a]">
                            {{ number_format($product->price ?? 0, 0, ',', '.') }}đ
                        </td>

                        <td class="p-4">
                            @if($product->status == 1)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Đang bán</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Ngừng bán</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('products.edit', $product->product_id) }}" class="text-blue-500 hover:text-blue-700">Sửa</a>
                                
                                <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-4 border-t">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection