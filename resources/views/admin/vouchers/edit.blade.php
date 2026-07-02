@extends('admin.layouts.app')

@section('title', 'Sửa Mã Giảm Giá - Chill Chill Admin')
@section('page_title', 'Sửa Mã Giảm Giá')

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="mb-4">
        <a href="{{ route('vouchers.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại danh sách</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('vouchers.update', $voucher->voucher_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã giảm giá <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $voucher->code) }}" placeholder="Ví dụ: CHILL50, GIAM20K" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại giảm giá <span class="text-red-500">*</span></label>
                    <select name="discount_type" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        <option value="percent" {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type', $voucher->discount_type) == 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định (đ)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá trị giảm <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $voucher->discount_value) }}" placeholder="Ví dụ: 10 (cho 10%) hoặc 20000 (cho 20k)" required min="0" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn hàng tối thiểu (đ) <span class="text-red-500">*</span></label>
                    <input type="number" name="min_order" value="{{ old('min_order', $voucher->min_order) }}" placeholder="Ví dụ: 50000" required min="0" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date', $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Giới hạn số lượt dùng (Bỏ trống nếu không giới hạn)</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" placeholder="Ví dụ: 100" min="1" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
            </div>

            <div class="pt-6 flex gap-4 justify-end">
                <a href="{{ route('vouchers.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Hủy</a>
                <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium">Cập nhật mã giảm giá</button>
            </div>
        </form>
    </div>
</div>
@endsection

