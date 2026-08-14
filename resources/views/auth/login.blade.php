<!doctype html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập / Đăng ký - Chill Chill Coffee & Tea</title>
    
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
          },
        },
      };
    </script>

    <style>
        /* Tùy chỉnh thanh cuộn */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f3f4f6; }
        ::-webkit-scrollbar-thumb { background: hsl(16, 23%, 19%); border-radius: 10px; }

        .auth-input {
            width: 100%;
            height: 56px;
            padding: 0 24px;
            background-color: #ffffff;
            border: 1px solid rgba(43, 38, 35, 0.1);
            border-radius: 100px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            color: #2B2623;
            transition: all 0.3s ease;
        }
        .auth-input:focus {
            outline: none;
            border-color: #e8634a; /* coral */
            box-shadow: 0 0 0 4px rgba(232, 99, 74, 0.1);
        }
    </style>
</head>
<body class="h-full bg-[#FAF7F2] font-sans antialiased overflow-hidden">

    <div class="flex h-full min-h-screen">
        
        {{-- CỘT TRÁI: Hình ảnh --}}
        <div class="hidden md:block md:w-1/2 lg:w-2/3 relative overflow-hidden bg-espresso">
            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=1200&auto=format&fit=crop" 
                 alt="Chill Chill Coffee" 
                 class="absolute inset-0 w-full h-full object-cover opacity-80 mix-blend-luminosity transition-transform duration-10000 hover:scale-110" />
            
            <div class="absolute inset-0 bg-gradient-to-t from-espresso via-espresso/40 to-transparent p-16 flex flex-col justify-between z-10">
                <a href="/" class="flex items-center gap-3 group w-max">
                    <img src="https://i.ibb.co/30XNqj5/chill-chill-logo-no-bg.png" alt="Chill Chill Logo" class="h-12 w-auto filter drop-shadow-lg group-hover:scale-105 transition-transform" />
                    <span class="font-serif font-black text-2xl tracking-widest text-cream uppercase drop-shadow-md">Chill Chill</span>
                </a>

                <div class="max-w-xl">
                    <h2 class="font-serif font-black text-5xl lg:text-6xl text-cream leading-tight mb-6 drop-shadow-lg">
                        Góc nhỏ <span class="text-coral italic font-medium">bình yên</span>,<br>hương vị vẹn nguyên.
                    </h2>
                    <p class="text-xl text-cream/80 font-light leading-relaxed drop-shadow">Đăng nhập để nhận ngay ưu đãi tích điểm và đặt món nhanh chóng hơn.</p>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: Form Đăng nhập / Đăng ký --}}
        <div class="w-full md:w-1/2 lg:w-1/3 flex flex-col justify-center bg-white p-10 md:p-16 relative shadow-2xl z-20 overflow-y-auto">
            
            <a href="/" class="md:hidden absolute top-6 left-6 text-espresso/60 hover:text-coral flex items-center gap-2">
                ← Trang chủ
            </a>

            {{-- FORM ĐĂNG NHẬP --}}
            <div id="login-form" class="transition-all duration-500 transform translate-x-0 opacity-100">
                <div class="mb-12">
                    <h1 class="font-serif font-bold text-4xl text-espresso mb-3">Chào mừng bạn!</h1>
                    <p class="text-espresso/60">Vui lòng đăng nhập để tiếp tục trải nghiệm cùng Chill Chill.</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    @error('login_error')
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-sm flex items-center gap-2 mb-2 animate-pulse">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                    @enderror

                    <div>
                        <input type="text" name="login_identity" placeholder="Số điện thoại hoặc tên đăng nhập" required class="auth-input">
                    </div>
                    
                    <div class="relative">
                        <input type="password" name="password" id="login-password" placeholder="Mật khẩu" required class="auth-input pr-12">
                        <button type="button" onclick="togglePassword('login-password', 'login-eye')" class="absolute right-4 top-1/2 -translate-y-1/2 text-espresso/40 hover:text-coral transition-colors focus:outline-none">
                            <svg id="login-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between text-sm pt-2">
                        <label class="flex items-center gap-2 text-espresso/70 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 accent-coral rounded border-gray-300">
                            Ghi nhớ tôi
                        </label>
                        <a href="{{ route('password.request') }}" class="text-coral hover:underline font-medium">Quên mật khẩu?</a>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full h-14 bg-espresso text-white rounded-full font-bold text-lg hover:bg-coral shadow-lg transition-all duration-300 flex items-center justify-center gap-3 group">
                            Đăng nhập ngay
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </form>

                <div class="mt-12 text-center border-t border-espresso/10 pt-8">
                    <p class="text-espresso/70">Bạn chưa có tài khoản?</p>
                    <button onclick="toggleAuthForms()" class="mt-3 text-coral font-bold text-lg hover:underline decoration-2 underline-offset-4">
                        Đăng ký tài khoản mới →
                    </button>
                </div>
            </div>

            {{-- FORM ĐĂNG KÝ --}}
            <div id="register-form" class="hidden absolute top-0 left-0 w-full h-full p-10 md:p-16 flex flex-col justify-center bg-white transition-all duration-500 transform translate-x-full opacity-0">
                <div class="mb-12">
                    <button onclick="toggleAuthForms()" class="text-coral hover:text-espresso mb-4 flex items-center gap-2 font-medium">
                        ← Quay lại đăng nhập
                    </button>
                    <h1 class="font-serif font-bold text-4xl text-espresso mb-3">Tạo tài khoản</h1>
                    <p class="text-espresso/60">Gia nhập gia đình Chill Chill để nhận ưu đãi tích điểm ngay hôm nay.</p>
                </div>

                <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    @error('register_error')
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-sm flex items-center gap-2 mb-2 animate-pulse">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                    @enderror

                    <div>
                        <input type="text" name="register_identity" placeholder="Nhập Số điện thoại hoặc Tên đăng nhập" required class="auth-input">
                        <p class="text-xs text-espresso/50 mt-2 px-4">* Thông tin này sẽ dùng để đăng nhập</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <input type="password" name="password" id="reg-password" placeholder="Mật khẩu" required class="auth-input pr-10">
                            <button type="button" onclick="togglePassword('reg-password', 'reg-eye')" class="absolute right-4 top-1/2 -translate-y-1/2 text-espresso/40 hover:text-coral transition-colors focus:outline-none">
                                <svg id="reg-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg-password-confirm" placeholder="Xác nhận mật khẩu" required class="auth-input pr-10">
                            <button type="button" onclick="togglePassword('reg-password-confirm', 'reg-confirm-eye')" class="absolute right-4 top-1/2 -translate-y-1/2 text-espresso/40 hover:text-coral transition-colors focus:outline-none">
                                <svg id="reg-confirm-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 text-sm text-espresso/70 pt-2 px-2">
                        <input type="checkbox" required class="w-4 h-4 accent-coral rounded border-gray-300 mt-1">
                        <span>Tôi đồng ý với <a href="#" class="text-coral underline">Điều khoản dịch vụ</a> và <a href="#" class="text-coral underline">Chính sách bảo mật</a>.</span>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full h-14 bg-coral text-white rounded-full font-bold text-lg hover:bg-[#e05b42] shadow-lg shadow-coral/30 transition-all duration-300 flex items-center justify-center gap-3">
                            Hoàn tất đăng ký
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

   {{-- Javascript chuyển đổi form và giữ trạng thái form đăng ký khi có lỗi --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Nếu có lỗi từ form đăng ký do backend gửi về, tự động mở form đăng ký
            const hasRegisterErrors = {{ $errors->has('register_error') ? 'true' : 'false' }};
            if (hasRegisterErrors) {
                document.getElementById('login-form').classList.add('hidden', 'translate-x-full', 'opacity-0');
                document.getElementById('login-form').classList.remove('translate-x-0', 'opacity-100');
                
                const registerForm = document.getElementById('register-form');
                registerForm.classList.remove('hidden', 'translate-x-full', 'opacity-0');
                registerForm.classList.add('translate-x-0', 'opacity-100');
            }
        });

        // Hàm click chuyển đổi (Giữ nguyên của bạn)
        function toggleAuthForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            if (registerForm.classList.contains('hidden')) {
                loginForm.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    loginForm.classList.add('hidden');
                    registerForm.classList.remove('hidden');
                    setTimeout(() => {
                        registerForm.classList.remove('translate-x-full', 'opacity-0');
                        registerForm.classList.add('translate-x-0', 'opacity-100');
                    }, 50);
                }, 400);
            } else {
                registerForm.classList.remove('translate-x-0', 'opacity-100');
                registerForm.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    registerForm.classList.add('hidden');
                    loginForm.classList.remove('hidden');
                    setTimeout(() => {
                        loginForm.classList.remove('translate-x-full', 'opacity-0');
                        loginForm.classList.add('translate-x-0', 'opacity-100');
                    }, 50);
                }, 400);
            }
        }
        
        // Cần thêm hàm togglePassword vào đây nếu trong HTML bạn gọi onclick="togglePassword(...)"
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</body>
</html>