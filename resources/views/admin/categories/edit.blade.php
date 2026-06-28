<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Danh Mục - Chill Chill Admin</title>
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
                <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
                <h2 class="text-xl font-semibold text-gray-800">Sửa Danh Mục: <span class="text-[#e8634a]">{{ $category->name }}</span></h2>
            </div>
        </header>

        <div class="p-8 max-w-3xl mx-auto w-full">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form action="{{ route('categories.update', $category->category_id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Hình ảnh (URL)</label>
                        <div class="flex gap-4 items-center">
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="Preview" class="w-16 h-16 rounded object-cover border">
                            @endif
                            <input type="text" name="image" value="{{ old('image', $category->image) }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                        </div>
                    </div>

                    <div class="pt-6 flex gap-4 justify-end">
                        <a href="{{ route('categories.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Hủy</a>
                        <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>