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
<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-6 md:py-10 px-3 md:px-6">
    
    {{-- Cửa sổ App: Tự co giãn trên Mobile/Tablet, ép 80vh trên Desktop --}}
    <div class="w-full max-w-5xl bg-white rounded-3xl md:rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-auto md:h-[80vh] min-h-0 md:min-h-[550px] md:max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng (Mobile Tabs + Desktop Sidebar) --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-5 md:p-10 flex flex-col shrink-0">
            <div class="flex items-center justify-between md:block mb-4 md:mb-8">
                <h2 class="font-serif font-bold text-xl md:text-2xl text-white">Tài khoản</h2>
                <a href="{{ route('logout') }}" class="md:hidden text-xs text-coral hover:text-white transition-colors flex items-center gap-1 font-bold">
                    Đăng xuất
                </a>
            </div>
            <nav class="flex md:flex-col overflow-x-auto md:overflow-x-visible space-x-2 md:space-x-0 md:space-y-2 flex-1 pb-2 md:pb-0 custom-scrollbar">
                <a href="{{ route('user.profile') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl bg-white/10 text-white font-medium text-xs md:text-sm transition-colors shrink-0">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Lịch sử đơn hàng</a>
                <a href="{{ route('user.points') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.wallet') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tiền hoàn</span>
                    <span class="bg-coral text-white text-[10px] md:text-xs font-bold px-2 py-0.5 rounded-full">{{ number_format(auth()->user()->wallet_balance ?? 0, 0, ',', '.') }}đ</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Form Cập nhật --}}
        <div class="w-full md:w-2/3 p-5 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar">
            <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Hồ sơ của bạn</h3>
            <p class="text-espresso/60 mb-8">Quản lý thông tin cá nhân và địa chỉ giao hàng</p>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    {{ session('success') }}
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
                        <label class="block text-sm font-bold text-espresso mb-2 flex items-center justify-between">
                            <span>Số điện thoại</span>
                            @if($user->phone)
                                <span class="text-[11px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-md border border-green-200">✓ Đã xác thực</span>
                            @else
                                <span class="text-[11px] text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">⚠️ Chưa có SĐT</span>
                            @endif
                        </label>
                        <div class="relative flex items-center">
                            <input type="text" id="profile-phone-display" value="{{ $user->phone ?? 'Chưa cập nhật SĐT' }}" readonly class="w-full px-4 py-3 bg-gray-100 border border-transparent rounded-xl text-espresso font-semibold cursor-not-allowed pr-36">
                            <button type="button" onclick="toggleSmsOtpBox()" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-espresso text-white text-xs rounded-lg font-semibold hover:bg-coral transition-colors">
                                Xác thực SĐT
                            </button>
                        </div>

                        {{-- Khối nhập & Xác minh SĐT qua SMS OTP --}}
                        <div id="profile-sms-block" class="hidden mt-3 bg-gradient-to-r from-amber-50 to-orange-50 p-4 border border-amber-200 rounded-2xl space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-espresso">Xác thực / Thay đổi Số điện thoại qua SMS OTP:</span>
                                <button type="button" onclick="toggleSmsOtpBox()" class="text-xs text-gray-400 hover:text-coral font-bold">✕ Đóng</button>
                            </div>

                            <div class="relative flex items-center">
                                <input type="text" id="profile-phone-input" placeholder="Nhập Số điện thoại mới (Ví dụ: 0912345678)" class="w-full px-3.5 py-2.5 bg-white border border-amber-300 rounded-xl text-sm focus:outline-none focus:border-coral pr-28">
                                <button type="button" id="btn-send-profile-sms" onclick="sendProfileSmsOtp()" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-espresso text-white text-xs rounded-lg font-semibold hover:bg-coral transition-colors">
                                    Gửi mã SMS
                                </button>
                            </div>

                            <div id="profile-otp-input-row" class="hidden flex gap-2">
                                <input type="text" id="profile-sms-code" maxlength="6" placeholder="Mã OTP 6 chữ số" class="w-full h-10 text-center text-sm font-mono tracking-widest border border-amber-300 rounded-xl focus:outline-none focus:border-coral bg-white">
                                <button type="button" onclick="verifyProfileSmsOtp()" class="px-4 h-10 bg-coral text-white text-xs rounded-xl font-bold hover:bg-espresso transition-colors shrink-0 flex items-center gap-1">
                                    <span>Xác minh & Lưu SĐT</span>
                                </button>
                            </div>
                            <div id="profile-sms-msg" class="text-xs font-medium hidden"></div>
                        </div>

                        <div id="profile-sms-verified-badge" class="hidden mt-2 bg-green-50 text-green-700 border border-green-200 px-3 py-2 rounded-xl text-xs font-bold">
                            🎉 Số điện thoại đã được xác thực và cập nhật thành công vào hệ thống!
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
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function toggleSmsOtpBox() {
        const block = document.getElementById('profile-sms-block');
        block.classList.toggle('hidden');
    }

    function sendProfileSmsOtp() {
        const phoneInput = document.getElementById('profile-phone-input');
        const phone = phoneInput ? phoneInput.value.trim() : '';

        if (!phone || !/^(0[3|5|7|8|9])+([0-9]{8})$/.test(phone)) {
            alert('Vui lòng nhập đúng định dạng Số điện thoại Việt Nam (10 chữ số)!');
            return;
        }

        const btn = document.getElementById('btn-send-profile-sms');
        btn.disabled = true;
        btn.innerHTML = 'Đang gửi...';

        fetch('{{ route("auth.send_phone_otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ phone: phone, check_exists: false })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Gửi lại SMS';
            if (data.success) {
                document.getElementById('profile-otp-input-row').classList.remove('hidden');
                const msgEl = document.getElementById('profile-sms-msg');
                msgEl.className = 'text-xs font-medium text-green-600 block mt-1';
                msgEl.innerText = data.message;
            } else {
                alert(data.message || 'Lỗi gửi mã SMS OTP');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Gửi mã SMS';
            alert('Không thể kết nối máy chủ gửi SMS. Vui lòng thử lại!');
        });
    }

    function verifyProfileSmsOtp() {
        const code = document.getElementById('profile-sms-code').value.trim();
        if (code.length !== 6) {
            alert('Vui lòng nhập đủ 6 chữ số mã OTP SMS!');
            return;
        }

        fetch('{{ route("auth.verify_phone_otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ otp_code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profile-sms-block').classList.add('hidden');
                document.getElementById('profile-sms-verified-badge').classList.remove('hidden');
                document.getElementById('profile-phone-display').value = data.phone;
                showToast('🎉 Xác thực & Cập nhật SĐT thành công!', 'success');
            } else {
                alert(data.message || 'Xác thực OTP thất bại!');
            }
        })
        .catch(err => {
            alert('Có lỗi xảy ra khi xác thực OTP!');
        });
    }

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
</script>
@endsection