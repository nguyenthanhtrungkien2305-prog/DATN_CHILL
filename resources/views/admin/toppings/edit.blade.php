<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Topping - Chill Chill Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    {{-- Copy phần Sidebar <aside>...</aside> từ file index qua đây --}}
    
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center gap-4 px-8">
            <a href="{{ route('toppings.index') }}" class="text-gray-500 hover:text-[#e8634a]">← Quay lại</a>
            <h2 class="text-xl font-semibold text-gray-800">Sửa Topping: <span class="text-[#e8634a]">{{ $topping->name }}</span></h2>
        </header>

        <div class="p-8 max-w-3xl mx-auto w-full">
            <form action="{{ route('toppings.update', $topping->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                @csrf @method('PUT')
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên Topping <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $topping->name) }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Giá tiền (VNĐ) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $topping->price) }}" min="0" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                    </div>
                </div>
<div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link Hình ảnh (URL)</label>
                    <div class="flex gap-4 items-center">
                        {{-- Ảnh xem trước --}}
                        <img src="{{ $topping->image ?? 'https://via.placeholder.com/150' }}" alt="Preview" class="w-16 h-16 rounded-full object-cover border border-gray-300">
                        
                        <input type="text" name="image" value="{{ old('image', $topping->image) }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium shadow-md">Cập nhật thay đổi</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>