<!doctype html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Chill Chill Coffee & Tea - Đặt cà phê online')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Dancing+Script:wght@700&family=Pacifico&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ["Plus Jakarta Sans", "Be Vietnam Pro", "Poppins", "sans-serif"],
              serif: ["Playfair Display", "Georgia", "serif"],
              script: ["Dancing Script", "Pacifico", "cursive"],
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
              "float-slow": "float 8s ease-in-out infinite",
              "spin-slow": "spin 15s linear infinite",
              "pulse-glow": "pulseGlow 2.5s ease-in-out infinite",
              "shimmer": "shimmer 2s infinite",
              "wiggle": "wiggle 1s ease-in-out infinite",
            },
            keyframes: {
              float: {
                "0%, 100%": { transform: "translateY(0)" },
                "50%": { transform: "translateY(-15px)" },
              },
              pulseGlow: {
                "0%, 100%": { opacity: "1", filter: "drop-shadow(0 0 12px rgba(255, 112, 67, 0.4))" },
                "50%": { opacity: "0.8", filter: "drop-shadow(0 0 4px rgba(255, 112, 67, 0.1))" },
              },
              shimmer: {
                "100%": { transform: "translateX(100%)" },
              },
              wiggle: {
                "0%, 100%": { transform: "rotate(-3deg)" },
                "50%": { transform: "rotate(3deg)" },
              }
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

      /* ========================================== */
      /* HỆ THỐNG ANIMATION REVEAL & MICRO-INTERACTION */
      /* ========================================== */
      .reveal, .reveal-up {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-down {
        opacity: 0;
        transform: translateY(-40px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-left {
        opacity: 0;
        transform: translateX(-50px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-right {
        opacity: 0;
        transform: translateX(50px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-zoom {
        opacity: 0;
        transform: scale(0.85);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-flip {
        opacity: 0;
        transform: rotateX(15deg) translateY(30px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }

      .reveal.visible, .reveal-up.visible, .reveal-down.visible, .reveal-left.visible, .reveal-right.visible, .reveal-zoom.visible, .reveal-flip.visible {
        opacity: 1;
        transform: translate(0, 0) scale(1) rotateX(0deg);
      }

      /* Delay Staggering */
      .stagger-1 { transition-delay: 0.1s; }
      .stagger-2 { transition-delay: 0.2s; }
      .stagger-3 { transition-delay: 0.3s; }
      .stagger-4 { transition-delay: 0.4s; }

      /* Interactive Micro-Animations */
      .hover-lift {
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.35s ease;
      }
      .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12);
      }

      .hover-scale {
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      }
      .hover-scale:hover {
        transform: scale(1.05);
      }

      .hover-rotate {
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      }
      .hover-rotate:hover {
        transform: scale(1.06) rotate(3deg);
      }

      .hover-glow {
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
      }
      .hover-glow:hover {
        border-color: rgba(255, 112, 67, 0.5);
        box-shadow: 0 0 25px rgba(255, 112, 67, 0.25);
      }

      .btn-pulse {
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease;
      }
      .btn-pulse:hover {
        transform: scale(1.03);
        box-shadow: 0 8px 25px rgba(255, 112, 67, 0.35);
      }
      .btn-pulse:active {
        transform: scale(0.96);
      }

      .img-zoom {
        transition: transform 0.7s cubic-bezier(0.25, 1, 0.5, 1);
      }
      .group:hover .img-zoom, a:hover .img-zoom {
        transform: scale(1.08);
      }

      .rotate-animation {
        animation: slowSpin 16s linear infinite;
      }
      @keyframes slowSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
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

    {{-- Container chứa thông báo Toast --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    {{-- MODAL XÁC NHẬN CHUNG (CONFIRM) --}}
    <div id="confirm-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeConfirmModal(false)"></div>
        <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-[380px] p-8 text-center transform transition-all scale-95 opacity-0 duration-300" id="confirm-modal-content">
            <div class="w-16 h-16 mx-auto mb-4 bg-coral/10 border border-coral/25 rounded-full flex items-center justify-center text-coral text-2xl">
                ❓
            </div>
            <h3 class="font-serif font-black text-2xl text-espresso mb-2">Xác nhận</h3>
            <p id="confirm-modal-message" class="text-espresso/70 text-sm mb-6 leading-relaxed">Bạn có chắc chắn muốn thực hiện hành động này?</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal(false)" class="flex-1 py-3 border border-espresso/20 rounded-full font-bold text-espresso/60 hover:bg-gray-50 transition-colors text-sm">Hủy</button>
                <button type="button" onclick="closeConfirmModal(true)" class="flex-1 py-3 bg-coral text-white rounded-full font-bold hover:bg-[#d5523b] shadow-lg shadow-coral/25 transition-all text-sm">Đồng ý</button>
            </div>
        </div>
    </div>

    
    @include('partials.header')

    <main class="{{ request()->is('/') ? 'pt-0' : 'pt-24 md:pt-28' }}">
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

    {{-- POP-UP YÊU CẦU ĐĂNG NHẬP ĐỂ ĐẶT HÀNG --}}
    @if(session('login_required'))
        <div id="login-required-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="document.getElementById('login-required-modal').style.display='none'"></div>
            
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md p-8 md:p-10 text-center animate-[float_3s_ease-in-out_infinite]">
                <div class="w-24 h-24 mx-auto mb-6 bg-[#FFF0D4] rounded-full flex items-center justify-center text-coral">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                
                <h2 class="font-serif font-black text-3xl text-espresso mb-3">Yêu cầu đăng nhập</h2>
                <p class="text-espresso/70 mb-8 leading-relaxed">
                    {{ session('login_required') }}
                </p>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full py-4 bg-coral text-white font-bold rounded-full hover:bg-[#d5523b] transition-colors shadow-lg shadow-coral/30">
                        Đăng nhập ngay
                    </a>
                    <button onclick="document.getElementById('login-required-modal').style.display='none'" class="w-full py-4 bg-transparent text-espresso/60 font-medium hover:text-espresso transition-colors">
                        Để sau nhé
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        // === HÀM HỎI XÁC NHẬN CHUNG (CONFIRM) ===
        let confirmCallback = null;

        function showConfirm(message, callback) {
            document.getElementById('confirm-modal-message').innerText = message;
            confirmCallback = callback;

            const modal = document.getElementById('confirm-modal');
            const modalContent = document.getElementById('confirm-modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeConfirmModal(result) {
            const modal = document.getElementById('confirm-modal');
            const modalContent = document.getElementById('confirm-modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                if (result && typeof confirmCallback === 'function') {
                    confirmCallback();
                }
                confirmCallback = null;
            }, 300);
        }

        // === HÀM HIỂN THỊ TOAST THÔNG BÁO SIÊU ĐẸP ===
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            // Cấu hình css glassmorphism kết hợp màu sắc chủ đạo của dự án
            toast.className = 'transform transition-all duration-500 ease-out translate-y-8 opacity-0 pointer-events-auto max-w-sm w-full bg-[#2B2623] border border-white/10 rounded-3xl p-4 shadow-[0_20px_50px_rgba(0,0,0,0.2)] flex items-center gap-4';
            
            let iconHtml = '';
            if (type === 'success') {
                iconHtml = `<div class="w-10 h-10 rounded-full bg-coral/15 border border-coral/30 flex items-center justify-center text-coral shrink-0 text-lg">✨</div>`;
            } else if (type === 'error' || type === 'danger') {
                iconHtml = `<div class="w-10 h-10 rounded-full bg-red-500/15 border border-red-500/30 flex items-center justify-center text-red-400 shrink-0 text-lg">⚠️</div>`;
            } else {
                iconHtml = `<div class="w-10 h-10 rounded-full bg-yellow-500/15 border border-yellow-500/30 flex items-center justify-center text-yellow-400 shrink-0 text-lg">💡</div>`;
            }

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1">
                    <p class="text-white text-sm font-semibold leading-snug">${message}</p>
                </div>
                <button onclick="this.parentElement.style.opacity='0'; setTimeout(() => this.parentElement.remove(), 500)" class="text-white/40 hover:text-white transition-colors duration-200 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            `;

            container.appendChild(toast);

            // Hiệu ứng trượt lên
            setTimeout(() => {
                toast.classList.remove('translate-y-8', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }, 50);

            // Tự động đóng sau 3.5 giây
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-8', 'opacity-0');
                    setTimeout(() => {
                        if (toast && toast.parentElement) toast.remove();
                    }, 500);
                }
            }, 3500);
        }

        // === HÀM CẬP NHẬT BADGE GIỎ HÀNG DỰA TRÊN SỐ LƯỢNG THỰC TẾ ===
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.innerText = count;
                if (parseInt(count) > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        // === TỰ ĐỘNG ĐỒNG BỘ BADGE GIỎ HÀNG KHI LOAD TRANG & KHI DÙNG BFCACHE ===
        function syncCartBadge() {
            fetch('{{ route('cart.count') }}')
                .then(res => res.json())
                .then(data => {
                    updateCartBadge(data.cart_count);
                })
                .catch(err => console.error('Lỗi đồng bộ giỏ hàng:', err));
        }

        document.addEventListener('DOMContentLoaded', syncCartBadge);
        window.addEventListener('pageshow', (event) => {
            syncCartBadge();
        });

        // === GHI ĐÈ WINDOW.ALERT ĐỂ TỰ ĐỘNG DÙNG TOAST ĐẸP ===
        window.alert = function(message) {
            let type = 'success';
            if (message.includes('Lỗi') || message.includes('lỗi') || message.includes('không') || message.includes('hết hạn') || message.includes('chưa') || message.includes('đầy đủ')) {
                type = 'error';
            }
            showToast(message, type);
        };

        // === THÊM NHANH VÀO GIỎ HÀNG KHÔNG RELOAD TRANG ===
        function quickAddToCart(productId) {
            let payload = {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: 1,
                toppings: {}
            };

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast('🎉 ' + data.message, 'success');
                    updateCartBadge(data.cart_count);
                } else {
                    showToast('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Lỗi hệ thống:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại sau!', 'error');
            });
        }

        // === INTERSECTION OBSERVER BẬT ANIMATION REVEAL TỰ ĐỘNG KHI CUỘN TRANG ===
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.08,
                rootMargin: '0px 0px -30px 0px'
            };

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const revealSelector = '.reveal, .reveal-up, .reveal-down, .reveal-left, .reveal-right, .reveal-zoom, .reveal-flip';
            const revealElements = document.querySelectorAll(revealSelector);
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
    
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ session('success') }}", 'success');
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ session('error') }}", 'error');
            });
        </script>
    @endif
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ $errors->first() }}", 'error');
            });
        </script>
    @endif

    {{-- Tích hợp Chat Box nổi --}}
    @include('partials.chatbox')
</body>
</html>