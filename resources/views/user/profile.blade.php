@extends('layouts.app')

@section('title', 'Tài Khoản Của Tôi - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-10 px-4">
    <div class="w-full max-w-5xl bg-white rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-[80vh] min-h-[550px] max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng (Cố định) --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-8 md:p-10 flex flex-col h-full shrink-0">
            <h2 class="font-serif font-bold text-2xl text-white mb-8">Tài khoản</h2>
            <nav class="space-y-2 flex-1">
                <a href="{{ route('user.profile') }}" class="block px-4 py-3 rounded-xl bg-white/10 text-white font-medium transition-colors">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Đơn hàng của tôi</a>
                <a href="{{ Route::has('user.points') ? route('user.points') : '#' }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors flex items-center justify-between">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
            </nav>
            <a href="{{ route('logout') }}" class="mt-auto px-4 py-3 text-coral hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Form Cập nhật --}}
        <div class="w-full md:w-2/3 p-8 md:p-12 h-full overflow-y-auto custom-scrollbar">
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
                        <label class="block text-sm font-bold text-espresso mb-2">Số điện thoại</label>
                        <input type="text" value="{{ $user->phone ?? 'Chưa cập nhật' }}" disabled class="w-full px-4 py-3 bg-gray-100 border border-transparent rounded-xl text-espresso/50 cursor-not-allowed">
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
</script>
@endsection