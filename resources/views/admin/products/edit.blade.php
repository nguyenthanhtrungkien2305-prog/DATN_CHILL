<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
                <h2 class="text-xl font-semibold text-gray-800">Sửa Sản Phẩm: <span class="text-[#e8634a]">{{ $product->name }}</span></h2>
            </div>
        </header>

        <div class="p-8 max-w-4xl mx-auto w-full">
            
            {{-- Hiển thị lỗi nếu nhập sai --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                        </div>

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
                {{-- KẾT THÚC KHU VỰC CHỌN TOPPING --}}
                        {{-- Mô tả --}}
                        <div class="md:col-span-3">
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
    </main>

</body>
</html>