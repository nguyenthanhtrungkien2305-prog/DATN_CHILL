@extends('admin.layouts.app')

@section('title', 'Thêm Topping - Chill Chill Admin')
@section('page_title', 'Thêm Topping Mới')

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="mb-4">
        <a href="{{ route('toppings.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại danh sách</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('toppings.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên Topping <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Trân châu trắng" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Giá tiền (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Link Hình ảnh (URL)</label>
            <input type="text" name="image" value="{{ old('image') }}" placeholder="Ví dụ: /images/toppings/pearl.jpg" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
        </div>

        <div class="pt-6 flex justify-end gap-4">
            <a href="{{ route('toppings.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">Hủy</a>
            <button type="submit" class="px-8 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium shadow-md">Lưu Topping</button>
        </div>
    </form>
</div>
@endsection