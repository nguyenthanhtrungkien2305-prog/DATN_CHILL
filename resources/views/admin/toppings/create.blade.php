<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Topping - Chill Chill Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    {{-- Copy phần Sidebar <aside>...</aside> từ file index qua đây --}}
    
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center gap-4 px-8">
            <a href="{{ route('toppings.index') }}" class="text-gray-500 hover:text-[#e8634a]">← Quay lại</a>
            <h2 class="text-xl font-semibold text-gray-800">Thêm Topping Mới</h2>
        </header>

        <div class="p-8 max-w-3xl mx-auto w-full">
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-6">
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

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium shadow-md">Lưu Topping</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>