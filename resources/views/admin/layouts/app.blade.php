<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Chill Chill')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- MAIN CONTENT (Nội dung chính) --}}
    <main class="flex-1 flex flex-col h-screen @yield('main_class', 'overflow-y-auto')">
        {{-- Header của Main Content --}}
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
            <h2 class="text-xl font-semibold text-gray-800">@yield('page_title', 'Tổng quan hệ thống')</h2>
            <div class="flex items-center gap-4">
                @yield('header_actions')
                <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
                <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
            </div>
        </header>

        {{-- Dynamic Content Area --}}
        <div class="@yield('content_class', 'p-8')">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>