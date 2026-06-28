<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Chill Chill</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- MAIN CONTENT (Nội dung chính) --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        {{-- Header của Main Content --}}
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Tổng quan hệ thống</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
                <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
            </div>
        </header>

        {{-- Widget thống kê --}}
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Tổng doanh thu</h3>
                    <p class="text-2xl font-bold text-gray-800">12,500,000 đ</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Đơn hàng mới</h3>
                    <p class="text-2xl font-bold text-gray-800">48</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Tổng sản phẩm</h3>
                    <p class="text-2xl font-bold text-gray-800">156</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Khách hàng</h3>
                    <p class="text-2xl font-bold text-gray-800">1,204</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[400px]">
                <p class="text-gray-500 text-center mt-20">Khu vực hiển thị biểu đồ hoặc danh sách dữ liệu...</p>
            </div>
        </div>
    </main>

</body>
</html>