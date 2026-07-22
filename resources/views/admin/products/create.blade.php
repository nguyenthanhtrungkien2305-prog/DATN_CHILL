<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm Mới - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#2B2623] text-white flex flex-col shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold tracking-widest text-[#e8634a]">ADMIN CHILL</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-white/10 transition">📊 Tổng quan</a>
            <a href="{{ route('products.index') }}" class="block px-4 py-3 rounded-lg bg-[#e8634a] text-white font-medium">☕ Quản lý Sản phẩm</a>
            <a href="/" class="block px-4 py-3 rounded-lg hover:bg-white/10 transition mt-auto border-t border-white/10">← Về trang web</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
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

                        <div class="md:col-span-2 bg-gray-50 p-5 border border-gray-200 rounded-lg">
                            <label class="block text-sm font-bold text-gray-800 mb-4">Thiết lập Giá theo Kích cỡ (Size) <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @if(isset($sizes) && count($sizes) > 0)
                                    @foreach($sizes as $size)
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Size {{ $size->name }} (VNĐ)</label>
                                            <input type="number" name="prices[{{ $size->size_id }}]" placeholder="VD: 35000" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e8634a]">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-3 text-sm text-red-500 italic">Chưa có dữ liệu Size trong Database!</div>
                                @endif
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

                        <div class="md:col-span-2 mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Topping áp dụng</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 border border-gray-200 rounded-lg bg-gray-50/50">
                                @foreach($allToppings as $top)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="toppings[]" value="{{ $top->topping_id }}" class="w-5 h-5 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a]">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#e8634a] transition-colors">{{ $top->name }}</span>
                                    </label>
                                @endforeach
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
    </main>
</body>
</html>