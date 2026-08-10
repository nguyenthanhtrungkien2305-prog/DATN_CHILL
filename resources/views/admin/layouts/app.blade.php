<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Chill Chill')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ["Poppins", "sans-serif"],
              serif: ["Playfair Display", "serif"],
            },
            colors: {
              espresso: {
                DEFAULT: "#2B2623",
                light: "#423B37",
              },
              cream: {
                DEFAULT: "#FAF7F2",
                light: "#FFF8E7",
              },
              coral: {
                DEFAULT: "#E8634A",
                hover: "#D5523B",
              },
            },
          }
        }
      }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.4); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col h-screen @yield('main_class', 'overflow-y-auto') w-full relative">
        @hasSection('page_title')
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">@yield('page_title')</h2>
            <div class="flex items-center gap-4">
                @yield('header_actions')
                <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
                <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
            </div>
        </header>
        @endif

        <div class="@yield('content_class', 'p-6 md:p-8 flex-1')">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>