@extends('layouts.app')

@section('title', 'Sổ Địa Chỉ Giao Hàng - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-6 md:py-10 px-3 md:px-6">
    <div class="w-full max-w-5xl bg-white rounded-3xl md:rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-auto md:h-[80vh] min-h-0 md:min-h-[550px] md:max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-5 md:p-10 flex flex-col shrink-0">
            <div class="flex items-center justify-between md:block mb-4 md:mb-8">
                <h2 class="font-serif font-bold text-xl md:text-2xl text-white">Tài khoản</h2>
                <a href="{{ route('logout') }}" class="md:hidden text-xs text-coral hover:text-white transition-colors flex items-center gap-1 font-bold">
                    Đăng xuất
                </a>
            </div>
            <nav class="flex md:flex-col overflow-x-auto md:overflow-x-visible space-x-2 md:space-x-0 md:space-y-2 flex-1 pb-2 md:pb-0 custom-scrollbar">
                <a href="{{ route('user.profile') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Lịch sử đơn hàng</a>
                <a href="{{ route('user.points') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.address') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl bg-white/10 text-white font-medium text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Địa chỉ nhận hàng</span>
                    <span class="bg-coral text-white text-[10px] md:text-xs font-bold px-2 py-0.5 rounded-full">{{ count($addresses) }}</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm">
                Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Quản lý địa chỉ giao hàng --}}
        <div class="w-full md:w-2/3 p-5 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-serif font-bold text-2xl md:text-3xl text-espresso">Địa chỉ nhận hàng</h3>
                @if(count($addresses) < 5)
                    <button type="button" onclick="openAddModal()" class="bg-coral hover:bg-[#d5523b] text-white font-bold text-xs md:text-sm px-4 py-2.5 rounded-xl transition-all shadow-md shrink-0">
                        Thêm địa chỉ mới
                    </button>
                @endif
            </div>
            <p class="text-espresso/60 text-sm mb-6">Tùy chỉnh tên người nhận, SĐT và chọn các Quận/Huyện tại TP. Hồ Chí Minh</p>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl mb-6 text-sm flex items-center gap-2 shadow-sm">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-2xl mb-6 text-sm flex items-center gap-2 shadow-sm">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Danh sách địa chỉ --}}
            <div class="space-y-4 flex-1">
                @forelse($addresses as $index => $addr)
                    @php
                        $name = is_array($addr) ? ($addr['name'] ?? $user->name) : $user->name;
                        $phone = is_array($addr) ? ($addr['phone'] ?? $user->phone) : $user->phone;
                        $district = is_array($addr) ? ($addr['district'] ?? '') : '';
                        $ward = is_array($addr) ? ($addr['ward'] ?? '') : '';
                        $street = is_array($addr) ? ($addr['street'] ?? '') : '';
                        $fullAddress = is_array($addr) ? ($addr['full_address'] ?? '') : $addr;
                    @endphp
                    <div class="p-5 bg-[#FAF7F2] rounded-2xl border {{ $index === 0 ? 'border-coral/40 bg-coral/5 shadow-sm' : 'border-gray-100' }} transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1 pr-2">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-bold text-sm {{ $index === 0 ? 'text-coral' : 'text-espresso' }}">
                                    {{ $index === 0 ? 'Địa chỉ Mặc định' : 'Địa chỉ ' . ($index + 1) }}
                                </span>
                                @if($index === 0)
                                    <span class="bg-coral text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Default</span>
                                @endif
                            </div>
                            <div class="text-sm text-espresso mb-1 flex items-center gap-2">
                                <strong class="font-bold text-espresso">{{ $name }}</strong>
                                <span class="text-gray-300">|</span>
                                <span class="text-espresso/80 font-medium">{{ $phone }}</span>
                            </div>
                            <p class="text-xs text-espresso/70 leading-relaxed font-medium">{{ $fullAddress }}</p>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                            @if($index !== 0)
                                <form action="{{ route('user.address.setDefault') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <button type="submit" class="text-xs font-semibold text-espresso/70 hover:text-coral hover:bg-white px-3 py-1.5 rounded-lg border border-espresso/10 transition-colors">
                                        Mặc định
                                    </button>
                                </form>
                            @endif

                            <button type="button" onclick="openEditModal({{ $index }}, '{{ addslashes($name) }}', '{{ addslashes($phone) }}', '{{ addslashes($district) }}', '{{ addslashes($ward) }}', '{{ addslashes($street) }}')" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:bg-white px-3 py-1.5 rounded-lg border border-blue-200 transition-colors">
                                Sửa
                            </button>

                            <form action="{{ route('user.address.delete') }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                                @csrf
                                <input type="hidden" name="index" value="{{ $index }}">
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 hover:bg-white px-3 py-1.5 rounded-lg border border-red-200 transition-colors">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-[#FAF7F2] rounded-3xl border border-dashed border-espresso/20 flex flex-col items-center justify-center">
                        <h4 class="font-bold text-espresso text-base mb-1">Chưa có địa chỉ nào</h4>
                        <p class="text-xs text-espresso/60 mb-4 max-w-xs text-center">Thêm địa chỉ ngay để trải nghiệm giao hàng tận nơi nhanh chóng từ Chill Chill nhé!</p>
                        <button type="button" onclick="openAddModal()" class="bg-coral text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-[#d5523b] transition-all">
                            Thêm địa chỉ nhận hàng
                        </button>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- MODAL THÊM ĐỊA CHỈ NÓNG --}}
<div id="add-address-modal" class="fixed inset-0 bg-espresso/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl p-6 md:p-8 w-full max-w-lg shadow-2xl border border-espresso/10 transform transition-all max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
            <h3 class="font-serif font-bold text-xl text-espresso">
                Thêm địa chỉ mới
            </h3>
            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-espresso p-1 rounded-full hover:bg-gray-100 transition-colors">
                ✕
            </button>
        </div>

        <form action="{{ route('user.address.store') }}" method="POST" id="add-address-form" class="space-y-4">
            @csrf
            {{-- 1. Tên người nhận --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Tên người nhận hàng</label>
                <input type="text" name="name" required value="{{ auth()->user()->name }}" placeholder="Nhập tên người nhận" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">
            </div>

            {{-- 2. SĐT nhận hàng + OTP verification block --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Số điện thoại nhận hàng</label>
                <div class="relative">
                    <input type="text" name="phone" id="add-phone-input" required value="{{ auth()->user()->phone }}" oninput="checkPhoneChange('add')" placeholder="Nhập 10 chữ số SĐT" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">
                </div>

                {{-- Status & OTP Actions Container --}}
                <div id="add-otp-container" class="mt-2 text-xs">
                    <div id="add-phone-badge" class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-lg font-medium">
                        <span>SĐT chính chủ tài khoản</span>
                    </div>
                </div>

                {{-- Khối nhập OTP (Ẩn) --}}
                <div id="add-otp-input-block" class="hidden mt-2 p-3 bg-amber-50/80 border border-amber-200 rounded-xl space-y-2">
                    <p class="text-xs text-amber-800 font-medium">Nhập mã OTP 6 chữ số đã gửi qua SMS:</p>
                    <div class="flex gap-2">
                        <input type="text" id="add-otp-code-input" maxlength="6" placeholder="______" class="w-32 px-3 py-1.5 border border-amber-300 rounded-lg text-center font-mono font-bold tracking-widest text-sm focus:outline-none focus:border-coral">
                        <button type="button" onclick="verifyOtp('add')" class="px-4 py-1.5 bg-coral text-white font-bold rounded-lg text-xs hover:bg-[#d5523b] transition-all">Xác thực OTP</button>
                    </div>
                </div>
            </div>

            {{-- 3. Quận / Huyện TP.HCM & Phường / Xã --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Quận / Huyện (TP.HCM)</label>
                    <select name="district" id="add-district-select" required onchange="onDistrictChange('add')" class="w-full px-3.5 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium bg-white">
                        <option value="">-- Chọn Quận/Huyện --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Phường / Xã</label>
                    <select name="ward" id="add-ward-select" class="w-full px-3.5 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium bg-white">
                        <option value="">-- Chọn Phường/Xã --</option>
                    </select>
                </div>
            </div>

            {{-- 4. Địa chỉ chi tiết --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Số nhà, Tên đường chi tiết</label>
                <input type="text" name="street" required placeholder="Ví dụ: Số 123 Nguyễn Văn Cừ" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-espresso/70 font-semibold text-xs hover:bg-gray-50 transition-colors">Hủy</button>
                <button type="submit" id="add-submit-btn" class="px-6 py-2.5 rounded-xl bg-coral text-white font-bold text-xs hover:bg-[#d5523b] shadow-md transition-all">Lưu địa chỉ</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SỬA ĐỊA CHỈ --}}
<div id="edit-address-modal" class="fixed inset-0 bg-espresso/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl p-6 md:p-8 w-full max-w-lg shadow-2xl border border-espresso/10 transform transition-all max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
            <h3 class="font-serif font-bold text-xl text-espresso">
                Chỉnh sửa địa chỉ
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-espresso p-1 rounded-full hover:bg-gray-100 transition-colors">
                ✕
            </button>
        </div>

        <form action="{{ route('user.address.update') }}" method="POST" id="edit-address-form" class="space-y-4">
            @csrf
            <input type="hidden" name="index" id="edit-address-index" value="">
            
            {{-- 1. Tên người nhận --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Tên người nhận hàng</label>
                <input type="text" name="name" id="edit-name-input" required placeholder="Nhập tên người nhận" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">
            </div>

            {{-- 2. SĐT nhận hàng + OTP verification block --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Số điện thoại nhận hàng</label>
                <input type="text" name="phone" id="edit-phone-input" required oninput="checkPhoneChange('edit')" placeholder="Nhập 10 chữ số SĐT" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">

                {{-- Status & OTP Actions Container --}}
                <div id="edit-otp-container" class="mt-2 text-xs">
                    <div id="edit-phone-badge" class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-lg font-medium">
                        <span>SĐT chính chủ tài khoản</span>
                    </div>
                </div>

                {{-- Khối nhập OTP (Ẩn) --}}
                <div id="edit-otp-input-block" class="hidden mt-2 p-3 bg-amber-50/80 border border-amber-200 rounded-xl space-y-2">
                    <p class="text-xs text-amber-800 font-medium">Nhập mã OTP 6 chữ số đã gửi qua SMS:</p>
                    <div class="flex gap-2">
                        <input type="text" id="edit-otp-code-input" maxlength="6" placeholder="______" class="w-32 px-3 py-1.5 border border-amber-300 rounded-lg text-center font-mono font-bold tracking-widest text-sm focus:outline-none focus:border-coral">
                        <button type="button" onclick="verifyOtp('edit')" class="px-4 py-1.5 bg-coral text-white font-bold rounded-lg text-xs hover:bg-[#d5523b] transition-all">Xác thực OTP</button>
                    </div>
                </div>
            </div>

            {{-- 3. Quận / Huyện TP.HCM & Phường / Xã --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Quận / Huyện (TP.HCM)</label>
                    <select name="district" id="edit-district-select" required onchange="onDistrictChange('edit')" class="w-full px-3.5 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium bg-white">
                        <option value="">-- Chọn Quận/Huyện --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Phường / Xã</label>
                    <select name="ward" id="edit-ward-select" class="w-full px-3.5 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium bg-white">
                        <option value="">-- Chọn Phường/Xã --</option>
                    </select>
                </div>
            </div>

            {{-- 4. Địa chỉ chi tiết --}}
            <div>
                <label class="block text-xs font-bold text-espresso uppercase tracking-wider mb-1.5">Số nhà, Tên đường chi tiết</label>
                <input type="text" name="street" id="edit-street-input" required placeholder="Ví dụ: Số 123 Nguyễn Văn Cừ" class="w-full px-4 py-2.5 border border-espresso/20 rounded-xl focus:ring-2 focus:ring-coral focus:border-coral outline-none text-sm font-medium">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-espresso/70 font-semibold text-xs hover:bg-gray-50 transition-colors">Hủy</button>
                <button type="submit" id="edit-submit-btn" class="px-6 py-2.5 rounded-xl bg-coral text-white font-bold text-xs hover:bg-[#d5523b] shadow-md transition-all">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
const USER_ACCOUNT_PHONE = "{{ auth()->user()->phone ?? '' }}";
let districtsData = [];
let verifiedPhonesMap = {};

const FALLBACK_DISTRICTS = [
    "Quận 1", "Quận 3", "Quận 4", "Quận 5", "Quận 6", "Quận 7", "Quận 8", "Quận 10", "Quận 11", "Quận 12",
    "Quận Bình Thạnh", "Quận Gò Vấp", "Quận Phú Nhuận", "Quận Tân Bình", "Quận Tân Phú", "Quận Bình Tân",
    "Thành phố Thủ Đức", "Huyện Củ Chi", "Huyện Hóc Môn", "Huyện Bình Chánh", "Huyện Nhà Bè", "Huyện Cần Giờ"
];

document.addEventListener("DOMContentLoaded", function() {
    fetch('https://provinces.open-api.vn/api/p/79?depth=3')
        .then(res => res.json())
        .then(data => {
            if (data && data.districts) {
                districtsData = data.districts;
                populateDistrictDropdowns();
            } else {
                populateFallbackDistricts();
            }
        })
        .catch(() => {
            populateFallbackDistricts();
        });
});

function populateDistrictDropdowns() {
    const addSelect = document.getElementById('add-district-select');
    const editSelect = document.getElementById('edit-district-select');

    addSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    editSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

    districtsData.forEach(d => {
        let opt1 = document.createElement('option');
        opt1.value = d.name;
        opt1.dataset.code = d.code;
        opt1.textContent = d.name;
        addSelect.appendChild(opt1);

        let opt2 = document.createElement('option');
        opt2.value = d.name;
        opt2.dataset.code = d.code;
        opt2.textContent = d.name;
        editSelect.appendChild(opt2);
    });
}

function populateFallbackDistricts() {
    const addSelect = document.getElementById('add-district-select');
    const editSelect = document.getElementById('edit-district-select');

    addSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    editSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

    FALLBACK_DISTRICTS.forEach(d => {
        let opt1 = document.createElement('option'); opt1.value = d; opt1.textContent = d; addSelect.appendChild(opt1);
        let opt2 = document.createElement('option'); opt2.value = d; opt2.textContent = d; editSelect.appendChild(opt2);
    });
}

function onDistrictChange(prefix, targetWardName = '') {
    const distSelect = document.getElementById(prefix + '-district-select');
    const wardSelect = document.getElementById(prefix + '-ward-select');
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

    const selectedDist = distSelect.value;
    const foundDist = districtsData.find(d => d.name === selectedDist);

    if (foundDist && foundDist.wards) {
        foundDist.wards.forEach(w => {
            let opt = document.createElement('option');
            opt.value = w.name;
            opt.textContent = w.name;
            if (targetWardName && w.name === targetWardName) {
                opt.selected = true;
            }
            wardSelect.appendChild(opt);
        });
    }
}

// ==========================================
// KIỂM TRA SĐT VÀ XÁC THỰC OTP SMS
// ==========================================
let currentOriginalPhoneEdit = '';

function checkPhoneChange(prefix) {
    const phoneInput = document.getElementById(prefix + '-phone-input');
    const container = document.getElementById(prefix + '-otp-container');
    const otpBlock = document.getElementById(prefix + '-otp-input-block');
    const submitBtn = document.getElementById(prefix + '-submit-btn');

    const phoneVal = phoneInput.value.trim();

    otpBlock.classList.add('hidden');

    if (phoneVal === USER_ACCOUNT_PHONE || (prefix === 'edit' && phoneVal === currentOriginalPhoneEdit) || verifiedPhonesMap[phoneVal]) {
        container.innerHTML = `
            <div class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-lg font-medium">
                <span>Số điện thoại hợp lệ / Đã xác thực</span>
            </div>
        `;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return;
    }

    const isValidPattern = /^(0[3|5|7|8|9])+([0-9]{8})$/.test(phoneVal);
    if (!isValidPattern) {
        container.innerHTML = `
            <div class="inline-flex items-center gap-1.5 text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-lg font-medium">
                <span>Số điện thoại phải gồm 10 chữ số hợp lệ</span>
            </div>
        `;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    container.innerHTML = `
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 bg-red-50 border border-red-200 p-2.5 rounded-xl">
            <span class="text-red-700 font-bold">SĐT mới (${phoneVal}) cần xác thực OTP</span>
            <button type="button" onclick="sendOtp('${prefix}')" class="px-3 py-1 bg-espresso hover:bg-coral text-white font-bold rounded-lg text-xs transition-colors shadow-sm shrink-0">
                Gửi mã OTP SMS
            </button>
        </div>
    `;
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
}

function sendOtp(prefix) {
    const phoneInput = document.getElementById(prefix + '-phone-input');
    const phoneVal = phoneInput.value.trim();

    if (!/^(0[3|5|7|8|9])+([0-9]{8})$/.test(phoneVal)) {
        alert('Vui lòng nhập đúng số điện thoại (10 chữ số bắt đầu bằng 03, 05, 07, 08, 09)!');
        return;
    }

    fetch('{{ route("auth.send_phone_otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ phone: phoneVal, check_exists: false })
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(errData => { throw new Error(errData.message || 'Lỗi gửi mã OTP'); });
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            const otpInput = document.getElementById(prefix + '-otp-code-input');
            otpInput.value = data.demo_otp ? data.demo_otp : '';
            document.getElementById(prefix + '-otp-input-block').classList.remove('hidden');
            otpInput.focus();
        } else {
            alert(data.message || 'Lỗi gửi mã OTP!');
        }
    })
    .catch(err => {
        alert(err.message || 'Không thể gửi mã OTP!');
    });
}

function verifyOtp(prefix) {
    const phoneInput = document.getElementById(prefix + '-phone-input');
    const otpCodeInput = document.getElementById(prefix + '-otp-code-input');
    const phoneVal = phoneInput.value.trim();
    const otpCode = otpCodeInput.value.trim();

    if (otpCode.length !== 6) {
        alert('Vui lòng nhập đủ 6 chữ số mã OTP!');
        return;
    }

    fetch('{{ route("auth.verify_phone_otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ otp_code: otpCode, update_user_phone: false })
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(errData => { throw new Error(errData.message || 'Xác thực OTP thất bại!'); });
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            verifiedPhonesMap[phoneVal] = true;
            alert('Xác thực OTP thành công!');
            document.getElementById(prefix + '-otp-input-block').classList.add('hidden');
            checkPhoneChange(prefix);
        } else {
            alert(data.message || 'Xác thực OTP thất bại!');
        }
    })
    .catch(err => {
        alert(err.message || 'Lỗi xác thực OTP!');
    });
}

function openAddModal() {
    document.getElementById('add-phone-input').value = USER_ACCOUNT_PHONE;
    checkPhoneChange('add');
    document.getElementById('add-address-modal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('add-address-modal').classList.add('hidden');
}

function openEditModal(index, name, phone, district, ward, street) {
    document.getElementById('edit-address-index').value = index;
    document.getElementById('edit-name-input').value = name || USER_ACCOUNT_PHONE;
    document.getElementById('edit-phone-input').value = phone || USER_ACCOUNT_PHONE;
    document.getElementById('edit-street-input').value = street || '';

    currentOriginalPhoneEdit = phone;

    const editDistSelect = document.getElementById('edit-district-select');
    editDistSelect.value = district || '';
    onDistrictChange('edit', ward);

    checkPhoneChange('edit');
    document.getElementById('edit-address-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-address-modal').classList.add('hidden');
}
</script>
@endsection
