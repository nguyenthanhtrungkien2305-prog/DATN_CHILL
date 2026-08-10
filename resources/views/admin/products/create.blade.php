@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới - Chill Chill Admin')
@section('page_title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="max-w-4xl mx-auto w-full">
    <div class="mb-4">
        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại danh sách</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6">
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
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm..." required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        <option value="" disabled selected>-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>

                @if(isset($sizes) && count($sizes) > 0)
                <div class="md:col-span-2 bg-gray-50 p-5 border border-gray-200 rounded-xl">
                    <label class="block text-sm font-bold text-gray-800 mb-4">Thiết lập Giá theo Kích cỡ (Size) <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($sizes as $size)
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Size {{ $size->name }} (VNĐ)</label>
                                <input type="number" name="prices[{{ $size->size_id }}]" value="{{ old('prices.'.$size->size_id) }}" placeholder="VD: 35000" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e8634a]">
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá bán (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" placeholder="VD: 35000" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Sản Phẩm <span class="text-red-500">*</span></label>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3">
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Tải ảnh từ máy tính (Ưu tiên)</span>
                            <input type="file" name="image_file" accept="image/*" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#e8634a]/10 file:text-[#e8634a] hover:file:bg-[#e8634a] hover:file:text-white transition cursor-pointer">
                        </div>
                        <div class="flex items-center gap-2">
                            <hr class="flex-1 border-gray-300"><span class="text-xs text-gray-400 font-medium">HOẶC DÙNG LINK URL</span><hr class="flex-1 border-gray-300">
                        </div>
                        <div>
                            <input type="text" name="image_url" value="{{ old('image_url') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]" placeholder="https://...">
                        </div>
                    </div>
                </div>

                @if(isset($allToppings) && count($allToppings) > 0)
                <div class="md:col-span-2 mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Topping áp dụng</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 border border-gray-200 rounded-xl bg-gray-50/50">
                        @foreach($allToppings as $top)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="toppings[]" value="{{ $top->topping_id }}" class="w-5 h-5 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a]">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-[#e8634a] transition-colors">{{ $top->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                    <textarea name="description" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#e8634a]">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t flex gap-4 justify-end">
                <a href="{{ route('products.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-medium">Hủy bỏ</a>
                <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-xl hover:bg-[#d5523b] transition font-medium shadow-sm">Thêm sản phẩm</button>
            </div>
        </form>
    </div>
</div>
@endsection
