@extends('layouts.app')

@section('title', 'Thanh toán - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        <h1 class="font-serif font-bold text-3xl md:text-4xl text-espresso mb-8">Thanh toán đơn hàng</h1>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" class="flex flex-col lg:flex-row gap-8 items-start">
            @csrf
            <input type="hidden" name="order_type" id="order_type" value="delivery">
            <input type="hidden" name="table_number" id="table_number" value="">

            {{-- ========================================= --}}
            {{-- CỘT TRÁI --}}
            {{-- ========================================= --}}
            <div class="w-full lg:w-2/3 space-y-6">
                
                {{-- BỘ TAB GIAO HÀNG / TẠI QUÁN --}}
                <div class="bg-white p-2 rounded-[20px] shadow-sm border border-espresso/5 flex gap-2">
                    <button type="button" id="btn-tab-delivery" onclick="switchOrderType('delivery')" class="flex-1 py-4 rounded-xl font-bold text-lg transition-all bg-coral text-white shadow-md">
                        🛵 Giao hàng tận nơi
                    </button>
                    <button type="button" id="btn-tab-dinein" onclick="switchOrderType('dine_in')" class="flex-1 py-4 rounded-xl font-bold text-lg transition-all text-espresso/60 hover:bg-gray-50">
                        🍽️ Thưởng thức tại quán
                    </button>
                </div>

                {{-- KHOẢNG 1: GIAO HÀNG TẬN NƠI --}}
                <div id="section-delivery" class="space-y-6 block">
                    <div class="bg-white p-6 md:p-8 rounded-[24px] shadow-sm border border-espresso/5">
                        <h2 class="font-bold text-xl text-espresso mb-4 flex items-center gap-2">
                            <span class="bg-coral text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">1</span> 
                            Thông tin người nhận
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-espresso/80 mb-2">Họ và tên</label>
                                <input type="text" name="customer_name" id="req_name" value="{{ $user->name ?? '' }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-coral bg-[#FAF7F2]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-espresso/80 mb-2">Số điện thoại</label>
                                <input type="text" name="customer_phone" id="req_phone" value="{{ $user->phone ?? '' }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-coral bg-[#FAF7F2]">
                            </div>
                        </div>
                    </div>

                    {{-- BLOCK ĐỊA CHỈ (ĐÃ CẢI TIẾN THEO YÊU CẦU) --}}
                    <div class="bg-white p-6 md:p-8 rounded-[24px] shadow-sm border border-espresso/5">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                            <h2 class="font-bold text-xl text-espresso flex items-center gap-2">
                                <span class="bg-coral text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">2</span> 
                                Địa chỉ giao hàng <span class="text-xs font-normal text-gray-400 ml-1">({{ count($addresses) }}/4)</span>
                            </h2>
                            
                            {{-- Nút Thêm Mới Dấu Cộng Ở Góc Phải --}}
                            @if(count($addresses) < 4)
                                <button type="button" onclick="openAddressModal()" class="text-coral font-bold hover:text-[#d5523b] flex items-center gap-1.5 text-sm bg-coral/10 px-3 py-1.5 rounded-full transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                    Thêm mới
                                </button>
                            @endif
                        </div>

                        {{-- Danh sách địa chỉ --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="address-grid">
                            @if(count($addresses) == 0)
                                <div class="col-span-2 text-center py-6 text-gray-400 italic">Chưa có địa chỉ nào được lưu.</div>
                            @endif

                            @foreach($addresses as $index => $addr)
                                {{-- Chỉ hiện 2 địa chỉ đầu, từ địa chỉ thứ 3 trở đi sẽ bị ẩn (gắn class extra-address) --}}
                                <label class="cursor-pointer relative address-item {{ $index >= 2 ? 'hidden extra-address' : '' }}">
                                    <input type="radio" name="shipping_address" value="{{ $addr }}" class="peer sr-only req-address-radio" {{ $index === 0 ? 'checked' : '' }} required>
                                    <div class="p-5 rounded-xl border-2 border-gray-100 peer-checked:border-coral peer-checked:bg-coral/5 hover:border-coral/30 transition-all h-full relative group shadow-sm">
                                        
                                        {{-- Nút Xóa (Dấu X - Chỉ hiện khi di chuột vào) --}}
                                        <button type="button" onclick="deleteAddress({{ $index }}, event)" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity bg-white rounded-full p-1 shadow-sm" title="Xóa địa chỉ này">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>

                                        <div class="flex items-center gap-2 mb-2 pr-6">
                                            <svg class="w-5 h-5 text-coral hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span class="font-bold text-espresso text-sm {{ $index === 0 ? 'text-coral' : '' }}">
                                                {{ $index === 0 ? 'Địa chỉ Mặc định' : 'Địa chỉ ' . ($index + 1) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-espresso/70 leading-relaxed line-clamp-2 pr-2" title="{{ $addr }}">{{ $addr }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- Nút Xem thêm (Chỉ hiện nếu có nhiều hơn 2 địa chỉ) --}}
                        @if(count($addresses) > 2)
                            <div class="mt-4 text-center border-t border-dashed border-gray-200 pt-4">
                                <button type="button" id="btn-toggle-address" onclick="toggleExtraAddresses()" class="text-sm font-medium text-espresso/60 hover:text-coral transition-colors flex items-center justify-center gap-1 mx-auto">
                                    <span>Xem thêm địa chỉ</span>
                                    <svg class="w-4 h-4 transition-transform" id="icon-toggle-address" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KHOẢNG 2: TẠI QUÁN --}}
                <div id="section-dinein" class="bg-white p-6 md:p-8 rounded-[24px] shadow-sm border border-espresso/5 hidden">
                    <h2 class="font-bold text-xl text-espresso mb-2 flex items-center gap-2">
                        <span class="bg-coral text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">1</span> Chọn bàn
                    </h2>
                    <p class="text-espresso/60 text-sm mb-6">Chọn số bàn bạn đang ngồi để nhân viên phục vụ nhé!</p>
                    <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-10 gap-3">
                        @for($i = 1; $i <= 30; $i++)
                            <button type="button" onclick="selectTable({{ $i }})" id="btn-table-{{ $i }}" class="table-btn aspect-square rounded-xl border-2 border-gray-100 flex items-center justify-center font-bold text-espresso/60 hover:border-coral/50 hover:text-coral transition-all relative overflow-hidden">
                                {{ $i }}
                                <div class="absolute bottom-0 right-0 bg-coral text-white rounded-tl-lg p-0.5 hidden check-icon">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </button>
                        @endfor
                    </div>
                </div>

                <div class="bg-white p-6 md:p-8 rounded-[24px] shadow-sm border border-espresso/5">
                    <h2 class="font-bold text-xl text-espresso mb-4 flex items-center gap-2">
                        <span class="bg-coral text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">💳</span> Phương thức thanh toán
                    </h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border border-coral bg-coral/5 rounded-xl cursor-pointer payment-method-label" id="label-cash">
                            <input type="radio" name="payment_method" value="cash" checked onchange="switchPaymentMethod('cash')" class="text-coral focus:ring-coral">
                            <span class="font-medium text-espresso" id="payment-text-cash">Thanh toán tiền mặt khi nhận hàng</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:border-coral/30 payment-method-label" id="label-qr">
                            <input type="radio" name="payment_method" value="qr" onchange="switchPaymentMethod('qr')" class="text-coral focus:ring-coral">
                            <span class="font-medium text-espresso">Chuyển khoản qua mã VietQR</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- CỘT PHẢI: TỔNG QUAN --}}
            {{-- ========================================= --}}
            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 md:p-8 rounded-[32px] shadow-lg border border-espresso/5 sticky top-24">
                    <h2 class="font-serif font-bold text-2xl text-espresso mb-6">Đơn hàng của bạn</h2>
                    <div class="space-y-4 mb-6 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        @php $subTotal = 0; @endphp
                        @foreach($cart as $item)
                            @php 
                                $itemTotal = ($item['price'] + $item['topping_total']) * $item['quantity'];
                                $subTotal += $itemTotal; 
                            @endphp
                            <div class="flex gap-4 items-start">
                                <div class="w-16 h-16 rounded-xl bg-cream overflow-hidden shrink-0 border border-gray-100">
                                    <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200' }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200';" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-espresso text-sm line-clamp-1">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-espresso/60 mb-1">{{ $item['size_name'] }} | x{{ $item['quantity'] }}</p>
                                    <span class="font-bold text-coral text-sm">{{ number_format($itemTotal, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-3 mb-6 pt-6 border-t border-espresso/10">
                        <div class="flex justify-between text-espresso/80 text-sm"><span>Tạm tính</span><span class="font-medium">{{ number_format($subTotal, 0, ',', '.') }}đ</span></div>
                        <div class="flex justify-between text-espresso/80 text-sm">
                            <span>Giảm giá (Voucher)</span>
                            <span class="font-medium text-coral">- {{ number_format(session()->has('voucher') ? session('voucher')['discount_amount'] : 0, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between text-espresso/80 text-sm" id="shipping-fee-row"><span>Phí vận chuyển</span><span class="font-medium text-green-500">Miễn phí</span></div>
                    </div>
                    @php
                        $discount = session()->has('voucher') ? session('voucher')['discount_amount'] : 0;
                        $finalTotal = max(0, $subTotal - $discount);
                    @endphp
                    <div class="flex justify-between items-end mb-8 pt-4 border-t border-espresso/10">
                        <span class="font-bold text-espresso">Tổng thanh toán</span>
                        <span class="font-black text-3xl text-coral">{{ number_format($finalTotal, 0, ',', '.') }}đ</span>
                    </div>
                    <button type="submit" id="btn-submit-order" onclick="return validateOrder()" class="w-full py-4 bg-coral text-white rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all">
                        Chốt Đơn Ngay
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL THÊM ĐỊA CHỈ --}}
<div id="new-address-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeAddressModal()"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg p-8 transform transition-all scale-95 opacity-0" id="address-modal-content">
        <h2 class="font-serif font-black text-2xl text-espresso mb-6">Thêm địa chỉ giao hàng</h2>
        <div class="space-y-4 mb-8">
            <div class="grid grid-cols-2 gap-4">
                <select id="district-select" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral text-sm font-medium"><option value="">-- Chọn Quận/Huyện --</option></select>
                <select id="ward-select" disabled class="w-full px-4 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:border-coral text-sm cursor-not-allowed opacity-60"><option value="">-- Chọn Phường/Xã --</option></select>
            </div>
            <input type="text" id="street-input" placeholder="Số nhà, Tên đường..." class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral text-sm">
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeAddressModal()" class="flex-1 py-3 rounded-full font-bold text-espresso/60 hover:bg-gray-100 transition-colors">Hủy</button>
            <button type="button" onclick="submitNewAddress()" class="flex-1 py-3 bg-coral text-white rounded-full font-bold hover:bg-[#d5523b] shadow-lg transition-all">Lưu địa chỉ</button>
        </div>
    </div>
</div>

<script>
    // === XÓA ĐỊA CHỈ ===
    function deleteAddress(index, event) {
        event.preventDefault(); // Ngăn việc click làm chọn luôn radio button
        event.stopPropagation();

        showConfirm('Bạn có chắc chắn muốn xóa địa chỉ này?', function() {
            fetch('{{ route("checkout.deleteAddress") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ index: index })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) { window.location.reload(); } 
                else { alert(data.message); }
            });
        });
    }

    // === XEM THÊM ĐỊA CHỈ ===
    let isAddressExpanded = false;
    function toggleExtraAddresses() {
        isAddressExpanded = !isAddressExpanded;
        
        document.querySelectorAll('.extra-address').forEach(el => {
            if (isAddressExpanded) el.classList.remove('hidden');
            else el.classList.add('hidden');
        });

        const btnText = document.querySelector('#btn-toggle-address span');
        const icon = document.getElementById('icon-toggle-address');
        
        if (isAddressExpanded) {
            btnText.innerText = 'Thu gọn';
            icon.classList.add('rotate-180');
        } else {
            btnText.innerText = 'Xem thêm địa chỉ';
            icon.classList.remove('rotate-180');
        }
    }

    // === PAYMENT METHOD LOGIC ===
    function switchPaymentMethod(method) {
        const labels = document.querySelectorAll('.payment-method-label');
        labels.forEach(l => {
            l.classList.remove('border-coral', 'bg-coral/5');
            l.classList.add('border-gray-100');
        });
        
        const selectedLabel = document.getElementById('label-' + method);
        if (selectedLabel) {
            selectedLabel.classList.remove('border-gray-100');
            selectedLabel.classList.add('border-coral', 'bg-coral/5');
        }
        
        const btnSubmit = document.getElementById('btn-submit-order');
        if (method === 'qr') {
            btnSubmit.innerText = 'Thanh toán qua mã QR';
        } else {
            btnSubmit.innerText = 'Chốt Đơn Ngay';
        }
    }

    // === TABS LOGIC ===
    function switchOrderType(type) {
        document.getElementById('order_type').value = type;
        const btnDelivery = document.getElementById('btn-tab-delivery');
        const btnDineIn = document.getElementById('btn-tab-dinein');
        const secDelivery = document.getElementById('section-delivery');
        const secDineIn = document.getElementById('section-dinein');
        const reqPhone = document.getElementById('req_phone');
        const addressRadios = document.querySelectorAll('.req-address-radio');

        if (type === 'delivery') {
            btnDelivery.className = 'flex-1 py-4 rounded-xl font-bold text-lg transition-all bg-coral text-white shadow-md';
            btnDineIn.className = 'flex-1 py-4 rounded-xl font-bold text-lg transition-all text-espresso/60 hover:bg-gray-50';
            secDelivery.classList.remove('hidden'); secDineIn.classList.add('hidden');
            document.getElementById('payment-text-cash').innerText = 'Thanh toán tiền mặt khi nhận hàng';
            document.getElementById('shipping-fee-row').style.display = 'flex';
            if(reqPhone) reqPhone.setAttribute('required', 'required');
            addressRadios.forEach(r => r.setAttribute('required', 'required'));
        } else {
            btnDineIn.className = 'flex-1 py-4 rounded-xl font-bold text-lg transition-all bg-coral text-white shadow-md';
            btnDelivery.className = 'flex-1 py-4 rounded-xl font-bold text-lg transition-all text-espresso/60 hover:bg-gray-50';
            secDineIn.classList.remove('hidden'); secDelivery.classList.add('hidden');
            document.getElementById('payment-text-cash').innerText = 'Thanh toán tiền mặt tại quầy';
            document.getElementById('shipping-fee-row').style.display = 'none';
            if(reqPhone) reqPhone.removeAttribute('required');
            addressRadios.forEach(r => r.removeAttribute('required'));
        }
    }

    // === CHỌN BÀN ===
    function selectTable(tableNumber) {
        document.getElementById('table_number').value = tableNumber;
        document.querySelectorAll('.table-btn').forEach(btn => {
            btn.classList.remove('border-coral', 'text-coral', 'bg-coral/5');
            btn.classList.add('border-gray-100', 'text-espresso/60');
            btn.querySelector('.check-icon').classList.add('hidden');
        });
        const activeBtn = document.getElementById('btn-table-' + tableNumber);
        activeBtn.classList.remove('border-gray-100', 'text-espresso/60');
        activeBtn.classList.add('border-coral', 'text-coral', 'bg-coral/5');
        activeBtn.querySelector('.check-icon').classList.remove('hidden');
    }

    // === VALIDATE SUBMIT ===
    function validateOrder() {
        if (document.getElementById('order_type').value === 'dine_in' && !document.getElementById('table_number').value) {
            alert('Vui lòng chọn số bàn bạn đang ngồi nhé!'); return false; 
        }
        return true; 
    }

    // === API ĐỊA CHỈ & MODAL ===
    function openAddressModal() {
        document.getElementById('new-address-modal').classList.remove('hidden');
        setTimeout(() => { document.getElementById('address-modal-content').classList.remove('scale-95', 'opacity-0'); }, 10);
    }
    function closeAddressModal() {
        document.getElementById('address-modal-content').classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('new-address-modal').classList.add('hidden'); }, 300);
    }

    let districtsData = [];
    fetch('https://provinces.open-api.vn/api/p/79?depth=3').then(res => res.json()).then(data => {
        districtsData = data.districts;
        districtsData.forEach(d => {
            let opt = document.createElement('option'); opt.value = d.name; opt.dataset.code = d.code; opt.textContent = d.name;
            document.getElementById('district-select').appendChild(opt);
        });
    });

    document.getElementById('district-select').addEventListener('change', function() {
        const wardSelect = document.getElementById('ward-select');
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        const code = this.options[this.selectedIndex].dataset.code;
        if (code) {
            const district = districtsData.find(d => d.code == code);
            district.wards.forEach(w => {
                let opt = document.createElement('option'); opt.value = w.name; opt.textContent = w.name;
                wardSelect.appendChild(opt);
            });
            wardSelect.disabled = false; wardSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            wardSelect.classList.add('bg-[#FAF7F2]');
        } else {
            wardSelect.disabled = true; wardSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
        }
    });

    function submitNewAddress() {
        const district = document.getElementById('district-select').value;
        const ward = document.getElementById('ward-select').value;
        const street = document.getElementById('street-input').value.trim();
        if(!district || !ward || !street) { alert('Vui lòng nhập đầy đủ thông tin địa chỉ!'); return; }
        
        fetch('{{ route('checkout.addAddress') }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({ new_address: `${street}, ${ward}, ${district}, TP. Hồ Chí Minh` })
        }).then(res => res.json()).then(data => {
            if(data.success) { window.location.reload(); } else { alert(data.message); }
        });
    }
</script>
@endsection