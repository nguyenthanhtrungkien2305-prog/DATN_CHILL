@extends('layouts.app')

@section('title', 'Tài Khoản Của Tôi - Chill Chill')

@section('content')
<style>
    /* CHỈ ẨN FOOTER, MỞ KHÓA LẠI BODY ĐỂ TRÁNH LỖI CẮT LAYOUT */
    footer, #footer, .footer { display: none !important; }

    /* Chỉnh dung nhan cho thanh cuộn */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
</style>

{{-- Tính toán trừ đi chiều cao của Header (khoảng 100px) để không bị đẩy tràn xuống dưới --}}
<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-4 sm:py-10 px-2 sm:px-4">
    
    <div class="w-full max-w-5xl bg-white rounded-[30px] md:rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-auto md:h-[80vh] md:min-h-[550px] md:max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng (Tự động thích ứng Mobile/Desktop) --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-5 md:p-10 flex flex-col h-auto md:h-full shrink-0">
            <div class="flex items-center justify-between md:block mb-3 md:mb-8">
                <h2 class="font-serif font-bold text-xl md:text-2xl text-white">Tài khoản</h2>
                <a href="{{ route('logout') }}" class="md:hidden px-3 py-1 bg-coral/20 text-coral hover:bg-coral hover:text-white rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Thoát
                </a>
            </div>

            <nav class="flex md:flex-col overflow-x-auto gap-2 py-1 md:py-0 custom-scrollbar flex-1 shrink-0">
                <a href="{{ route('user.profile') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors bg-white/20 text-white font-bold">
                    Thông tin cá nhân
                </a>
                <a href="{{ route('user.orders') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors text-cream/70 hover:text-white hover:bg-white/5">
                    Đơn hàng của tôi
                </a>
                <a href="{{ route('user.points') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors flex items-center justify-between gap-2 shrink-0 text-cream/70 hover:text-white hover:bg-white/5">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors text-cream/70 hover:text-white hover:bg-white/5">
                    Đổi mật khẩu
                </a>
            </nav>

            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Form Cập nhật --}}
        <div class="w-full md:w-2/3 p-4 sm:p-8 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar">
            <h3 class="font-serif font-bold text-2xl md:text-3xl text-espresso mb-1">Hồ sơ của bạn</h3>
            <p class="text-espresso/60 mb-8">Quản lý thông tin cá nhân và địa chỉ giao hàng</p>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-bold text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-2xl text-xs font-bold mb-6 space-y-1">
                    @foreach ($errors->all() as $err)
                        <p>• {{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('user.update_profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pr-2 pb-6">
                @csrf

                {{-- Avatar --}}
                <div class="flex items-center gap-6 mb-8 pb-8 border-b border-espresso/10">
                    <div class="w-24 h-24 rounded-full border-4 border-[#FAF7F2] shadow-sm overflow-hidden bg-gray-100 relative group shrink-0">
                        <img id="avatarPreview" src="{{ $user->avatar ? asset($user->avatar) : 'https://i.pravatar.cc/150?u='.$user->user_id }}" class="w-full h-full object-cover">
                        <label class="absolute inset-0 bg-black/40 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <input type="file" name="avatar" class="hidden" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>
                    <div>
                        <h4 class="font-bold text-espresso">Ảnh đại diện</h4>
                        <p class="text-xs text-espresso/50 mt-1">Hỗ trợ JPG, PNG. Tối đa 2MB.</p>
                    </div>
                </div>

                {{-- Thông tin cơ bản --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2">Họ và tên</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2">Số điện thoại</label>
                        <div class="relative flex items-center">
                            <input type="text" id="displayPhoneInput" value="{{ $user->phone ?? 'Chưa cập nhật' }}" disabled class="w-full px-4 py-3 bg-gray-100 border border-transparent rounded-xl text-espresso font-bold cursor-not-allowed pr-32">
                            <button type="button" onclick="openOtpModal()" class="absolute right-2 px-3 py-1.5 bg-coral text-white text-xs font-bold rounded-lg hover:bg-[#d5523b] transition-all shadow-xs">
                                Thay đổi SĐT
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-espresso mb-2">Địa chỉ Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso">
                </div>

                {{-- Địa chỉ mặc định --}}
                <div class="p-5 bg-[#FAF7F2] rounded-2xl border border-espresso/10">
                    <label class="block text-sm font-bold text-espresso mb-4">Địa chỉ giao hàng (TP. Hồ Chí Minh)</label>
                    
                    @if($user->address)
                        <div class="mb-4 text-sm text-espresso/80 bg-white p-3 rounded-lg border border-espresso/10 shadow-sm">
                            <span class="font-bold text-coral">Hiện tại:</span> <span id="current-address-text">{{ is_array(json_decode($user->address, true)) ? json_decode($user->address, true)[0] : $user->address }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <select id="district-select" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-coral transition-all text-espresso font-medium cursor-pointer"><option value="">-- Chọn Quận/Huyện --</option></select>
                        <select id="ward-select" disabled class="w-full px-4 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:border-coral transition-all text-espresso cursor-not-allowed opacity-60"><option value="">-- Chọn Phường/Xã --</option></select>
                    </div>
                    <input type="text" id="street-input" placeholder="Số nhà, Tên đường..." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-coral transition-all text-espresso">
                    
                    <input type="hidden" name="address" id="final-address" value="{{ $user->address }}">
                </div>

                <div class="pt-6 text-right pb-4">
                    <button type="submit" class="px-8 py-3 bg-espresso text-white rounded-full font-bold hover:bg-coral transition-colors shadow-lg">Lưu thay đổi</button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){ document.getElementById('avatarPreview').src = reader.result; };
        reader.readAsDataURL(event.target.files[0]);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const districtSelect = document.getElementById('district-select');
        const wardSelect = document.getElementById('ward-select');
        const streetInput = document.getElementById('street-input');
        const finalAddress = document.getElementById('final-address');
        const currentAddressText = document.getElementById('current-address-text');
        let districtsData = [];

        fetch('https://provinces.open-api.vn/api/p/79?depth=3').then(res => res.json()).then(data => {
            districtsData = data.districts;
            districtsData.forEach(d => {
                let opt = document.createElement('option'); opt.value = d.name; opt.dataset.code = d.code; opt.textContent = d.name;
                districtSelect.appendChild(opt);
            });
        });

        districtSelect.addEventListener('change', function() {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            const code = this.options[this.selectedIndex].dataset.code;
            if (code) {
                const district = districtsData.find(d => d.code == code);
                district.wards.forEach(w => {
                    let opt = document.createElement('option'); opt.value = w.name; opt.textContent = w.name;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false; wardSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60', 'border-transparent');
                wardSelect.classList.add('bg-white', 'cursor-pointer', 'border-gray-200');
            } else {
                wardSelect.disabled = true; wardSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60', 'border-transparent');
            }
            updateFinalAddress();
        });

        wardSelect.addEventListener('change', updateFinalAddress);
        streetInput.addEventListener('input', updateFinalAddress);

        function updateFinalAddress() {
            const district = districtSelect.value; const ward = wardSelect.value; const street = streetInput.value.trim();
            if (district || ward || street) {
                let parts = [];
                if (street) parts.push(street); if (ward) parts.push(ward); if (district) parts.push(district);
                parts.push('TP. Hồ Chí Minh');
                const fullAddress = parts.join(', ');
                
                finalAddress.value = JSON.stringify([fullAddress]); 
                if(currentAddressText) currentAddressText.textContent = fullAddress;
            }
        }
    });

    // ==========================================
    // JS XỬ LÝ MODAL OTP THAY ĐỔI SỐ ĐIỆN THOẠI
    // ==========================================
    function openOtpModal() {
        const modal = document.getElementById('otpModal');
        const box = document.getElementById('otpModalBox');
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
        resetOtpModal();
    }

    function closeOtpModal() {
        const modal = document.getElementById('otpModal');
        const box = document.getElementById('otpModalBox');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function resetOtpModal() {
        document.getElementById('step1Phone').classList.remove('hidden');
        document.getElementById('step2Otp').classList.add('hidden');
        document.getElementById('newPhoneInput').value = '';
        document.getElementById('otpCodeInput').value = '';
        hideOtpAlert();
    }

    function showOtpAlert(msg, isSuccess = false) {
        const alert = document.getElementById('otpAlert');
        alert.className = `p-3 rounded-xl text-xs font-bold mb-4 ${isSuccess ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-red-100 text-red-800 border border-red-300'}`;
        alert.innerHTML = msg;
        alert.classList.remove('hidden');
    }

    function hideOtpAlert() {
        document.getElementById('otpAlert').classList.add('hidden');
    }

    function sendOtpRequest() {
        const phone = document.getElementById('newPhoneInput').value.trim();
        const btn = document.getElementById('btnSendOtp');
        hideOtpAlert();

        if (!phone) {
            showOtpAlert('Vui lòng nhập số điện thoại mới.');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Đang phát sinh & gửi OTP...';

        fetch("{{ route('user.send_phone_otp') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Gửi Mã OTP Xác Thực';

            if (data.success) {
                document.getElementById('step1Phone').classList.add('hidden');
                document.getElementById('step2Otp').classList.remove('hidden');
                document.getElementById('targetPhoneText').innerText = phone;
                showOtpAlert(data.message, true);
            } else {
                showOtpAlert(data.message || 'Gửi OTP thất bại. Vui lòng kiểm tra lại!');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Gửi Mã OTP Xác Thực';
            showOtpAlert('Có lỗi hệ thống xảy ra. Vui lòng thử lại!');
        });
    }

    function verifyOtpRequest() {
        const otp = document.getElementById('otpCodeInput').value.trim();
        const btn = document.getElementById('btnVerifyOtp');
        hideOtpAlert();

        if (!otp || otp.length !== 6) {
            showOtpAlert('Vui lòng nhập chính xác 6 chữ số mã OTP.');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Đang xác nhận OTP...';

        fetch("{{ route('user.verify_phone_otp') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ otp_code: otp })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Xác Nhận & Cập Nhật SĐT';

            if (data.success) {
                showOtpAlert(data.message, true);
                document.getElementById('displayPhoneInput').value = data.phone;
                setTimeout(() => {
                    closeOtpModal();
                }, 2000);
            } else {
                showOtpAlert(data.message || 'Mã OTP không đúng hoặc đã hết hạn.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Xác Nhận & Cập Nhật SĐT';
            showOtpAlert('Có lỗi xác thực xảy ra. Vui lòng thử lại!');
        });
    }
</script>

{{-- MODAL XÁC THỰC OTP THAY ĐỔI SỐ ĐIỆN THOẠI --}}
<div id="otpModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 sm:p-8 shadow-2xl border border-espresso/10 relative transform transition-all scale-95 opacity-0 duration-200" id="otpModalBox">
        <button type="button" onclick="closeOtpModal()" class="absolute right-4 top-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold text-sm">✕</button>

        <h3 class="font-serif font-bold text-xl text-espresso mb-1">Thay Đổi Số Điện Thoại</h3>
        <p class="text-xs text-espresso/60 mb-6">Mã xác thực OTP (6 chữ số) sẽ được phát sinh và gửi xác nhận.</p>

        {{-- ALERT MESSAGES IN MODAL --}}
        <div id="otpAlert" class="hidden p-3 rounded-xl text-xs font-bold mb-4"></div>

        {{-- BƯỚC 1: NHẬP SỐ ĐIỆN THOẠI MỚI --}}
        <div id="step1Phone">
            <div class="mb-4">
                <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Số điện thoại mới</label>
                <input type="text" id="newPhoneInput" placeholder="Ví dụ: 0987654321" class="w-full px-4 py-3 bg-[#FAF7F2] border border-gray-200 rounded-xl focus:outline-none focus:border-coral font-bold text-espresso text-sm">
            </div>
            <button type="button" onclick="sendOtpRequest()" id="btnSendOtp" class="w-full py-3 bg-coral hover:bg-[#d5523b] text-white rounded-xl font-bold text-sm shadow-md transition-all">
                Gửi Mã OTP Xác Thực
            </button>
        </div>

        {{-- BƯỚC 2: NHẬP MÃ OTP --}}
        <div id="step2Otp" class="hidden">
            <div class="mb-4 text-center">
                <p class="text-xs font-bold text-espresso/70 mb-2">Đã gửi mã đến số: <span id="targetPhoneText" class="text-coral font-black"></span></p>
                <input type="text" id="otpCodeInput" maxlength="6" placeholder="000000" class="w-full text-center px-4 py-3 bg-[#FAF7F2] border-2 border-coral/40 rounded-xl focus:outline-none focus:border-coral font-mono font-black text-2xl tracking-[0.4em] text-espresso">
            </div>

            <button type="button" onclick="verifyOtpRequest()" id="btnVerifyOtp" class="w-full py-3 bg-espresso hover:bg-coral text-white rounded-xl font-bold text-sm shadow-md transition-all mb-3">
                Xác Nhận & Cập Nhật SĐT
            </button>

            <div class="text-center">
                <button type="button" onclick="sendOtpRequest()" id="btnResendOtp" class="text-xs text-coral font-bold hover:underline">
                    Chưa nhận được mã? Gửi lại OTP
                </button>
            </div>
        </div>
    </div>
</div>
@endsection