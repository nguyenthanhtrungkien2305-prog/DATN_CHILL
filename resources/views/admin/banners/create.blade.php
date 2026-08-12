@extends('admin.layouts.app')

@section('title', 'Thêm Banner Mới - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0 border-b border-gray-100">
        <div class="max-w-5xl w-full mx-auto flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Thêm Banner Mới Cho Trang Chủ / Combo</h2>
            <a href="{{ route('banners.index') }}" class="text-gray-600 hover:text-[#e8634a] text-sm font-medium transition">← Quay lại danh sách</a>
        </div>
    </header>

    <div class="p-8 max-w-5xl mx-auto w-full">
        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-md">
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Vị trí hiển thị --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Vị Trí Đặt Banner <span class="text-red-500">*</span></label>
                        <select name="position" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-semibold bg-gray-50">
                            <option value="home_hero" selected>🚩 Hero Banner Đầu Trang Chủ (`home_hero`)</option>
                            <option value="home_promo">🎁 Promo Voucher Banner Cuối Trang Chủ (`home_promo`)</option>
                            <option value="combo_banner">🛍️ Banner Khuyến Mãi Combo (`combo_banner`)</option>
                        </select>
                    </div>

                    {{-- Thẻ Tag / Badge --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Thẻ Nhãn (Badge Tag)</label>
                        <input type="text" name="badge" value="{{ old('badge', 'Thưởng thức hương vị chuẩn Gu') }}" placeholder="Ví dụ: Thưởng thức hương vị chuẩn Gu" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                        <span class="text-xs text-gray-400 mt-1 block">Dòng nhãn nhỏ nổi bật phía trên tiêu đề</span>
                    </div>
                </div>

                {{-- Tiêu đề chính --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tiêu Đề Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', 'Thư giãn từng nét - Giao hòa cảm xúc') }}" required placeholder="Ví dụ: Thư giãn từng nét - Giao hòa cảm xúc" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-bold">
                </div>

                {{-- Mô tả banner --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mô Tả / Nội Dung Banner</label>
                    <textarea name="description" rows="3" placeholder="Nhập nội dung mô tả giới thiệu hoặc hướng dẫn nhập mã..." class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm leading-relaxed">{{ old('description', 'Nơi dừng chân lý tưởng cho những tách cà phê nguyên chất đậm đà và ly trà sữa ngọt ngào. Gọi món ngay để nhận ưu đãi giao tận nơi!') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-orange-50/50 rounded-2xl border border-orange-100">
                    {{-- Nút bấm chính --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Nút Bấm 1 (Nút Chính)</label>
                        <input type="text" name="button_text" value="{{ old('button_text', 'Khám phá Menu ngay') }}" placeholder="Chữ trên nút 1" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-semibold mb-2 bg-white">
                        <input type="text" name="button_link" value="{{ old('button_link', '/san-pham') }}" placeholder="Link nút 1 (vd: /san-pham)" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-mono bg-white">
                    </div>

                    {{-- Nút bấm phụ --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Nút Bấm 2 (Nút Phụ - Dành cho Hero Banner)</label>
                        <input type="text" name="button_secondary_text" value="{{ old('button_secondary_text', 'Món bán chạy') }}" placeholder="Chữ trên nút 2" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-semibold mb-2 bg-white">
                        <input type="text" name="button_secondary_link" value="{{ old('button_secondary_link', '#best-sellers') }}" placeholder="Link nút 2 (vd: #best-sellers)" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-mono bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Chọn Sản Phẩm hiển thị trên Hero Banner --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sản Phẩm Hiển Thị Trên Hero Banner</label>
                        <select name="product_id" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                            <option value="">-- Mặc định (Món được ghim / Mới nhất) --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->product_id }}">
                                    ☕ {{ $prod->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray-400 mt-1 block">Chọn món sẽ hiển thị nổi bật ở thẻ bên phải Hero Banner Trang Chủ</span>
                    </div>

                    {{-- Kiểu Màu Nền Gradient --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Màu Nền Banner (Dành cho Combo / Text Banner)</label>
                        <select name="bg_gradient" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                            <option value="from-espresso via-coral to-amber-600" selected>Nâu Trầm & Cam Sang Trọng (Mặc định)</option>
                            <option value="from-amber-700 via-orange-600 to-amber-500">Cam Đất Vàng Hổ Phách</option>
                            <option value="from-red-700 via-coral to-rose-500">Đỏ Coral Khuyến Mãi</option>
                            <option value="from-emerald-800 via-teal-600 to-green-500">Xanh Tươi Mát Trà Trái Cây</option>
                        </select>
                    </div>
                </div>

                {{-- Upload Ảnh minh họa --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh Nền / Minh họa (Dành cho Promo Banner)</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                    <span class="text-xs text-gray-400 mt-1 block">Chấp nhận JPG, PNG, WEBP tối đa 2MB</span>
                </div>

                {{-- Trạng thái hiển thị --}}
                <div class="pt-2">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="status" value="1" checked class="w-5 h-5 rounded text-[#e8634a] focus:ring-[#e8634a]">
                        <span class="text-sm font-semibold text-gray-800">Hiển thị Banner này ngay trên trang web</span>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-4 border-t border-gray-100">
                    <a href="{{ route('banners.index') }}" class="px-6 py-2.5 border rounded-xl text-gray-600 hover:bg-gray-50 text-sm font-medium">Hủy bỏ</a>
                    <button type="submit" class="px-6 py-2.5 bg-[#e8634a] text-white rounded-xl hover:bg-[#d5523b] transition font-medium text-sm shadow-md">
                        Lưu & Tạo Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
