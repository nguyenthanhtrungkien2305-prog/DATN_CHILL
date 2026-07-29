@extends('admin.layouts.app')

<<<<<<< HEAD
<<<<<<< HEAD
@section('title', 'Sửa Sản Phẩm - Chill Chill Admin')
@section('page_title')
    Sửa Sản Phẩm: <span class="text-[#e8634a]">{{ $product->name }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto w-full">
    <div class="mb-4">
        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại danh sách</a>
    </div>

    {{-- Hiển thị lỗi nếu nhập sai --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
=======
    <aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">ADMIN CHILL</h1>
>>>>>>> main
        </div>
    @endif

<<<<<<< HEAD
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        {{-- Form Sửa (Chú ý action có truyền $product->product_id và dùng @method('PUT')) --}}
        <form action="{{ route('products.update', $product->product_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Tên sản phẩm --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
=======
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
                <h2 class="text-xl font-semibold text-gray-800">Sửa Sản Phẩm: <span class="text-[#e8634a]">{{ $product->name }}</span></h2>
=======
@section('title', 'Sửa Sản Phẩm - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
            <h2 class="text-xl font-semibold text-gray-800">Sửa Sản Phẩm: <span class="text-[#e8634a]">{{ $product->name }}</span></h2>
        </div>
    </header>

    <div class="p-8 max-w-4xl mx-auto w-full">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
>>>>>>> main
            </div>
        @endif

<<<<<<< HEAD
        <div class="p-8 max-w-4xl mx-auto w-full">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
>>>>>>> main
                </div>

<<<<<<< HEAD
                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ $product->category_id == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Giá tiền sản phẩm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá tiền (đ) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? 0) }}" min="0" placeholder="Nhập giá sản phẩm..." required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>

                {{-- Link Hình ảnh --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link Hình ảnh (URL)</label>
                    <div class="flex gap-4 items-center">
                        <img src="{{ $product->image_url }}" alt="Preview" class="w-16 h-16 rounded object-cover border">
                        <input type="text" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]" placeholder="https://example.com/image.jpg">
                    </div>
                </div>

                {{-- KHU VỰC CHỌN TOPPING NẰM Ở ĐÂY --}}
                <div class="md:col-span-3 mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Topping áp dụng</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 border border-gray-200 rounded-lg bg-gray-50/50">
                        @foreach($allToppings as $top)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="toppings[]" value="{{ $top->topping_id }}" 
                                    class="w-5 h-5 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a]"
                                    {{ isset($selectedToppings) && in_array($top->topping_id, $selectedToppings) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-[#e8634a] transition-colors">
                                    {{ $top->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2 italic">* Khách hàng chỉ có thể thêm những topping được tick chọn ở đây khi mua sản phẩm này.</p>
                </div>
=======
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
=======
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">
                    </div>
>>>>>>> main

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ $product->category_id == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Đang bán</option>
                            <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Ngừng bán</option>
                        </select>
                    </div>

                    {{-- CẬP NHẬT GIÁ VÀ THÊM SIZE ĐỘNG --}}
                    <div class="md:col-span-2 bg-gray-50 p-5 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-3">
                            <label class="block text-sm font-bold text-gray-800">Cập nhật Giá theo Kích cỡ (VNĐ) <span class="text-red-500">*</span></label>
                            
                            {{-- NÚT CỘNG THÊM SIZE MỚI --}}
                            <button type="button" onclick="addSizeRow()" class="flex items-center justify-center w-7 h-7 bg-[#e8634a] text-white rounded-full hover:bg-[#d5523b] shadow-sm transition transform hover:scale-110" title="Thêm Kích cỡ mới">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        
                        <div id="variants-container" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if(isset($variants) && count($variants) > 0)
                                @foreach($variants as $variant)
                                    <div class="relative bg-white p-3 rounded border border-gray-200 shadow-sm">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Size {{ $variant->size_name ?? '?' }}</label>
                                        <input type="number" name="variants[{{ $variant->size_id }}]" value="{{ $variant->price }}" required class="w-full px-3 py-1.5 rounded-lg border border-gray-300 focus:border-[#e8634a] text-sm">
                                    </div>
                                @endforeach
                            @else
                                <div id="empty-variants-msg" class="col-span-3 text-sm text-red-500 italic py-2">
                                    Chưa có Size/Giá cho sản phẩm này! Hãy bấm nút <b>(+)</b> ở góc trên bên phải để thêm mới.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- HÌNH ẢNH CHÍNH --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Chính</label>
                        <div class="flex gap-4 items-start bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <img src="{{ $product->image_url }}" alt="Preview" class="w-20 h-20 rounded-lg object-cover border-2 border-white shadow-sm shrink-0">
                            <div class="w-full space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase w-20">Tải File:</span>
                                    <input type="file" name="image_file" accept="image/*" class="w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-[#e8634a]/10 file:text-[#e8634a] hover:file:bg-[#e8634a] hover:file:text-white transition cursor-pointer">
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase w-20">Hoặc Link:</span>
                                    <input type="text" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] text-sm" placeholder="https://example.com/image.jpg">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HÌNH ẢNH PHỤ (GALLERY) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Phụ (Tối đa 3 ảnh)</label>
                        <div class="space-y-4">
                            @for($i = 0; $i < 3; $i++)
                                @php
                                    $imgUrl = isset($extraImages[$i]) ? $extraImages[$i]->image_url : '';
                                    $imgId = isset($extraImages[$i]) ? $extraImages[$i]->id : '';
                                @endphp
                                <div class="flex flex-col md:flex-row gap-4 items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" class="w-12 h-12 rounded object-cover border-2 border-white shadow-sm shrink-0">
                                        <input type="hidden" name="extra_image_ids[{{ $i }}]" value="{{ $imgId }}">
                                    @else
                                        <div class="w-12 h-12 rounded bg-gray-200 border border-gray-300 border-dashed shrink-0 flex items-center justify-center text-gray-400 text-xs">Trống</div>
                                    @endif
                                    
                                    <div class="w-full space-y-2">
                                        <input type="file" name="extra_image_files[{{ $i }}]" accept="image/*" class="w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 transition cursor-pointer">
                                        <input type="text" name="extra_images[{{ $i }}]" value="{{ $imgUrl }}" class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]" placeholder="Link ảnh phụ {{ $i + 1 }}... (Bỏ trống để xóa ảnh)">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
>>>>>>> main

<<<<<<< HEAD
                {{-- Mô tả --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
=======
                    <!-- {{-- Topping --}}
                    <div class="md:col-span-2 mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Topping áp dụng</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 border border-gray-200 rounded-lg bg-gray-50/50">
                            @foreach($allToppings as $top)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="toppings[]" value="{{ $top->topping_id }}" 
                                        class="w-5 h-5 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a]"
                                        {{ isset($selectedToppings) && in_array($top->topping_id, $selectedToppings) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-[#e8634a] transition-colors">{{ $top->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div> -->

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] focus:ring-1 focus:ring-[#e8634a]">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <div class="pt-6 border-t flex gap-4 justify-end">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">Hủy bỏ</a>
                    <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] transition font-medium">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
>>>>>>> main

<<<<<<< HEAD
            <div class="pt-6 border-t flex gap-4 justify-end">
                <a href="{{ route('products.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">Hủy bỏ</a>
                <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] transition font-medium">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection
=======
    {{-- SCRIPTS XỬ LÝ THÊM SIZE ĐỘNG --}}
    <script>
        const allSizes = @json(isset($sizes) ? $sizes : []);

        function addSizeRow() {
            const container = document.getElementById('variants-container');
            const emptyMsg = document.getElementById('empty-variants-msg');
            if (emptyMsg) emptyMsg.style.display = 'none';

            let options = '<option value="">-- Chọn Size --</option>';
            allSizes.forEach(s => {
                options += `<option value="${s.size_id}">${s.name}</option>`;
            });

            const div = document.createElement('div');
            div.className = 'relative bg-white p-3 rounded border border-[#e8634a] shadow-md flex flex-col gap-2 animate-pulse';
            setTimeout(() => div.classList.remove('animate-pulse'), 1000);

            div.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold hover:bg-red-600 shadow z-10" title="Xóa ô này">✕</button>
                <select class="w-full text-xs border border-gray-300 rounded px-2 py-1.5 focus:border-[#e8634a] focus:outline-none font-bold text-gray-700" onchange="updateVariantInput(this)">
                    ${options}
                </select>
                <input type="number" placeholder="Nhập giá bán (VNĐ)..." disabled class="variant-new-price w-full px-3 py-1.5 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50">
            `;
            container.appendChild(div);
        }

        function updateVariantInput(selectEl) {
            const inputEl = selectEl.nextElementSibling;
            if(selectEl.value) {
                inputEl.name = `variants[${selectEl.value}]`; 
                inputEl.disabled = false;
                inputEl.required = true;
                inputEl.classList.remove('bg-gray-50');
            } else {
                inputEl.name = '';
                inputEl.disabled = true;
                inputEl.required = false;
                inputEl.classList.add('bg-gray-50');
            }
        }
    </script>
<<<<<<< HEAD
</body>
</html>
>>>>>>> main
=======
@endsection
>>>>>>> main
