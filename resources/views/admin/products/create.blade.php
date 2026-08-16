@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
            <h2 class="text-xl font-semibold text-gray-800">Thêm Sản Phẩm Mới</h2>
        </div>
    </header>

    <div class="p-8 max-w-4xl mx-auto w-full">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm..." required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            <option value="" disabled selected>-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            <option value="1">Đang bán</option>
                            <option value="0">Ngừng bán</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 p-5 border border-gray-200 rounded-2xl">
                        <div class="mb-4 border-b border-gray-200 pb-3">
                            <label class="block text-sm font-bold text-gray-800">Thiết lập Giá theo Kích cỡ (Size) <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-500 mt-0.5">Nhập giá bán tương ứng cho kích cỡ bạn muốn bán (Để trống nếu không áp dụng kích cỡ đó cho sản phẩm này).</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($sizes as $size)
                                @php
                                    $name = trim($size->name);
                                    $displayName = (str_starts_with(strtolower($name), 'size') || $name === 'Mặc định') ? $name : 'Size ' . $name;
                                @endphp
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm hover:border-[#e8634a] transition-all">
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ $displayName }} (VNĐ)</label>
                                    <input type="number" name="prices[{{ $size->size_id }}]" placeholder="VD: 35000" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#e8634a] focus:outline-none text-sm font-medium">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Chính <span class="text-red-500">*</span></label>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Cách 1: Tải ảnh từ máy tính (Ưu tiên)</span>
                                <input type="file" name="image_file" accept="image/*" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-[#e8634a]/10 file:text-[#e8634a] hover:file:bg-[#e8634a] hover:file:text-white transition cursor-pointer">
                            </div>
                            <div class="flex items-center gap-2">
                                <hr class="flex-1 border-gray-300"><span class="text-xs text-gray-400 font-medium">HOẶC</span><hr class="flex-1 border-gray-300">
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Cách 2: Dùng Link ảnh (URL)</span>
                                <input type="text" name="image_url" class="mt-1 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Phụ (Tối đa 3 ảnh)</label>
                        <div class="space-y-4">
                            @for($i = 0; $i < 3; $i++)
                            <div class="flex flex-col md:flex-row gap-2 items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <input type="file" name="extra_image_files[{{ $i }}]" accept="image/*" class="w-full md:w-1/2 text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 transition cursor-pointer">
                                <span class="text-xs text-gray-400 font-medium whitespace-nowrap">HOẶC LINK:</span>
                                <input type="text" name="extra_images[{{ $i }}]" class="w-full md:w-1/2 px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]" placeholder="Nhập URL ảnh phụ...">
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]"></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t flex gap-4 justify-end">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">Hủy bỏ</a>
                    <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] transition font-medium">Thêm sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
@endsection