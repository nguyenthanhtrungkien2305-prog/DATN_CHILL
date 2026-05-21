<!doctype html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Chill Chill Coffee & Tea - Đặt cà phê online')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700;900&display=swap" rel="stylesheet" />

    
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
                DEFAULT: "hsl(16, 23%, 19%)",
                light: "hsl(16, 20%, 29%)",
              },
              cream: {
                DEFAULT: "hsl(45, 100%, 94%)",
                light: "hsl(45, 100%, 96%)",
              },
              coral: {
                DEFAULT: "hsl(14, 82%, 65%)",
                hover: "hsl(14, 82%, 72%)",
              },
            },
            borderRadius: {
              card: "20px",
            },
            animation: {
              "bounce-slow": "bounce 3s infinite",
              float: "float 6s ease-in-out infinite",
            },
            keyframes: {
              float: {
                "0%, 100%": { transform: "translateY(0)" },
                "50%": { transform: "translateY(-20px)" },
              },
            },
          },
        },
      };
    </script>

    <style>
      /* Tùy chỉnh thanh cuộn */
      ::-webkit-scrollbar {
        width: 8px;
      }
      ::-webkit-scrollbar-track {
        background: #f3f4f6;
      }
      ::-webkit-scrollbar-thumb {
        background: hsl(16, 23%, 19%);
        border-radius: 10px;
      }

      /* Hover Card Sản phẩm */
      .product-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      }
      .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      }
      .product-image {
        transition: transform 0.6s ease;
      }
      .product-card:hover .product-image {
        transform: scale(1.08);
      }

      /* Lớp Reveal */
      .reveal {
        opacity: 1;
        transform: translateY(40px);
        transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
      }
      .reveal.visible {
        opacity: 1;
        transform: translateY(0);
      }

      /* Header Glassmorphism */
      .header-glass {
        background: rgba(43, 38, 35, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }

      /* Ẩn outline input search */
      input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: none;
      }
    </style>
</head>
<body class="bg-[#FAF7F2] text-espresso font-sans antialiased overflow-x-hidden">

    
    @include('partials.header')

    <main class="pt-24 md:pt-28">
        @yield('content')
    </main>

    @include('partials.footer')
{{-- POP-UP CHÀO MỪNG THÀNH VIÊN MỚI --}}
@if(session('show_welcome_modal'))
<div id="welcome-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    {{-- Lớp nền mờ --}}
    <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeWelcomeModal()"></div>
    
    {{-- Nội dung thẻ Pop-up --}}
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md p-8 md:p-10 text-center transform transition-all scale-100 opacity-100 animate-[float_3s_ease-in-out_infinite]">
        
        {{-- Icon pháo hoa / Cà phê --}}
        <div class="w-24 h-24 mx-auto mb-6 bg-[#FFF0D4] rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        
        <h2 class="font-serif font-black text-3xl text-espresso mb-3">Chào mừng bạn!</h2>
        <p class="text-espresso/70 mb-8 leading-relaxed">
            Tuyệt vời! Bạn đã chính thức trở thành thành viên của gia đình <strong>Chill Chill</strong>. Hãy cập nhật thêm thông tin để chúng tôi phục vụ bạn chu đáo hơn nhé.
        </p>
        
        <div class="flex flex-col gap-3">
            {{-- Nút Cập nhật ngay (Sang trang Tài khoản) --}}
            <a href="{{ route('user.profile') }}" class="w-full py-4 bg-coral text-white font-bold rounded-full hover:bg-[#d5523b] transition-colors shadow-lg shadow-coral/30">
                Cập nhật thông tin ngay
            </a>
            {{-- Nút Để sau (Đóng pop-up ở lại trang chủ) --}}
            <button onclick="closeWelcomeModal()" class="w-full py-4 bg-transparent text-espresso/60 font-medium hover:text-espresso transition-colors">
                Để sau nhé
            </button>
        </div>
    </div>
</div>

<script>
    function closeWelcomeModal() {
        document.getElementById('welcome-modal').style.display = 'none';
    }
</script>
@endif
{{-- THÔNG BÁO CẢNH BÁO TRUY CẬP TRÁI PHÉP --}}
    @if(session('access_denied'))
        <div id="alert-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('alert-modal').style.display='none'"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center animate-[float_3s_ease-in-out_infinite]">
                <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center text-red-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="font-bold text-2xl text-espresso mb-2">Truy cập bị từ chối!</h3>
                <p class="text-espresso/70 mb-6">{{ session('access_denied') }}</p>
                <button onclick="document.getElementById('alert-modal').style.display='none'" class="w-full py-3 bg-red-500 text-white font-bold rounded-full hover:bg-red-600 transition-colors">
                    Đã hiểu
                </button>
            </div>
        </div>
    @endif
</body>
</html>