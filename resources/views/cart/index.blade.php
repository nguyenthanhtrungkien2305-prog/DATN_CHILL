@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        {{-- TÍNH TOÁN TỔNG SỐ LƯỢNG VÀ TỔNG TIỀN TỪ SESSION --}}
        @php
            $totalItems = 0;
            $subTotal = 0;
            if(session()->has('cart')) {
                foreach(session('cart') as $item) {
                    $totalItems += $item['quantity'];
                    $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
                }
            }
        @endphp

        <div class="flex items-center gap-3 mb-8">
            <h1 class="font-serif font-bold text-3xl md:text-4xl text-espresso">Giỏ hàng của bạn</h1>
            <span class="bg-coral text-white text-sm font-bold px-3 py-1 rounded-full">{{ $totalItems }} món</span>
        </div>

        {{-- KIỂM TRA NẾU GIỎ HÀNG CÓ SẢN PHẨM --}}
        @if(session('cart') && count(session('cart')) > 0)
        
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            {{-- ========================================= --}}
            {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM TRONG GIỎ --}}
            {{-- ========================================= --}}
            <div class="w-full lg:w-2/3 space-y-4">
                
                @foreach(session('cart') as $cartKey => $item)
                @php
                    // CÁCH MỚI: Kiểm tra trực tiếp xem sản phẩm có thuộc Danh mục Topping không
                    $categoryName = \DB::table('products')
                        ->join('categories', 'products.category_id', '=', 'categories.category_id')
                        ->where('products.product_id', $item['product_id'])
                        ->value('categories.name');

                    // Nếu tên danh mục có chứa chữ "topping" (không phân biệt hoa thường)
                    // (Hoặc nếu bạn biết chính xác ID danh mục topping, ví dụ ID = 5, bạn có thể dùng: $isStandaloneTopping = ($categoryId == 5); )
                    $isStandaloneTopping = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');
                @endphp

                <div class="{{ $isStandaloneTopping ? 'bg-coral/5 border-coral/20 p-4 rounded-[20px]' : 'bg-white border-espresso/5 p-4 md:p-6 rounded-[24px]' }} shadow-sm border flex flex-col sm:flex-row gap-6 relative group transition-all">
                    
                    {{-- Nút Xóa Sản Phẩm --}}
                    <button type="button" onclick="removeCartItem('{{ $cartKey }}')" class="absolute top-4 right-4 text-espresso/40 hover:text-red-500 transition-colors p-2" title="Xóa món này">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>

                    {{-- Ảnh Sản Phẩm (Nhỏ hơn nếu là Topping rời) --}}
                    <div class="{{ $isStandaloneTopping ? 'w-20 h-20 sm:w-24 sm:h-24' : 'w-24 h-24 sm:w-32 sm:h-32' }} rounded-2xl overflow-hidden bg-cream shrink-0">
                        <img src="{{ $item['image'] ?? 'https://via.placeholder.com/200' }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Thông tin --}}
                    <div class="flex-1 flex flex-col justify-center">
                        {{-- Badge phân loại nếu là Topping rời --}}
                        @if($isStandaloneTopping)
                            <span class="text-[10px] font-bold uppercase tracking-wider text-coral mb-1 w-max">Topping Mua Rời</span>
                        @endif

                        <h3 class="font-serif font-bold {{ $isStandaloneTopping ? 'text-lg' : 'text-xl' }} text-espresso mb-1 pr-8">{{ $item['name'] }}</h3>
                        
                        {{-- Kích cỡ --}}
                        <p class="text-sm text-espresso/60 mb-2">Kích cỡ: <span class="font-medium text-espresso">{{ $item['size_name'] }}</span></p>
                        
                        {{-- =============================================== --}}
                        {{-- KHU VỰC TOPPING ĐI KÈM HIỂN THỊ DƯỚI DẠNG TAG --}}
                        {{-- =============================================== --}}
                        @if(!empty($item['toppings']))
                            <div class="bg-[#FAF7F2] p-3 rounded-xl mb-3 border border-espresso/5 w-full md:w-max">
                                <p class="text-xs font-bold text-espresso/50 uppercase tracking-wider mb-2 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    Topping đi kèm (+{{ number_format($item['topping_total'], 0, ',', '.') }}đ)
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($item['toppings'] as $top)
                                        <div class="px-3 py-1 bg-white text-coral border border-coral/20 rounded-lg text-xs font-medium flex items-center gap-1 shadow-sm">
                                            {{ $top['name'] }} 
                                            <span class="bg-coral text-white text-[10px] px-1.5 py-0.5 rounded-md ml-1">x{{ $top['qty'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- NÚT THÊM/SỬA TOPPING --}}
                        @if(!$isStandaloneTopping)
                        <button onclick="openEditToppingModal('{{ $cartKey }}')" class="text-xs font-medium text-coral hover:text-[#d5523b] hover:underline text-left w-max mb-4 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            {{ !empty($item['toppings']) ? 'Thay đổi Topping' : 'Thêm Topping (+)' }}
                        </button>
                        @endif

                        {{-- Khu vực Giá x Số lượng --}}
                        <div class="flex items-end justify-between mt-auto pt-2">
                            <div class="font-black {{ $isStandaloneTopping ? 'text-base' : 'text-lg' }} text-espresso">
                                {{ number_format(($item['price'] + $item['topping_total']) * $item['quantity'], 0, ',', '.') }} đ
                            </div>

                            <div class="flex items-center justify-between border border-espresso/20 rounded-full h-9 w-24 bg-white overflow-hidden">
                                <button onclick="updateCartItemQty('{{ $cartKey }}', -1)" class="w-7 h-full flex items-center justify-center text-espresso hover:text-coral font-bold text-base transition-colors">-</button>
                                <input type="text" value="{{ $item['quantity'] }}" readonly class="w-8 h-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 p-0 m-0 leading-none">
                                <button onclick="updateCartItemQty('{{ $cartKey }}', 1)" class="w-7 h-full flex items-center justify-center text-espresso hover:text-coral font-bold text-base transition-colors">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            {{-- ========================================= --}}
            {{-- CỘT PHẢI: TỔNG QUAN ĐƠN HÀNG & VOUCHER --}}
            {{-- ========================================= --}}
            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 md:p-8 rounded-[32px] shadow-lg border border-espresso/5 sticky top-24">
                    <h2 class="font-serif font-bold text-2xl text-espresso mb-6">Tổng đơn hàng</h2>
                    
                    {{-- Khu vực nhập VOUCHER --}}
                    <div class="mb-6 pb-6 border-b border-espresso/10">
                        <label class="block text-sm font-medium text-espresso/80 mb-2">Mã ưu đãi (Voucher)</label>
                        @if(session()->has('voucher'))
                            <div class="flex items-center justify-between p-3 bg-coral/5 border border-coral/20 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🎟️</span>
                                    <div>
                                        <span class="block font-bold text-espresso uppercase text-sm">{{ session('voucher')['code'] }}</span>
                                        <span class="text-xs text-coral">Đã áp dụng thành công</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeVoucher()" class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline">Hủy áp dụng</button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <input type="text" id="voucher-input" placeholder="Nhập mã giảm giá..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-coral text-sm uppercase">
                                <button type="button" onclick="applyVoucher()" class="bg-espresso text-white px-6 py-3 rounded-xl font-bold hover:bg-coral transition-colors text-sm whitespace-nowrap">Áp dụng</button>
                            </div>
                        @endif
                    </div>

                    {{-- Chi tiết thanh toán --}}
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-espresso/80">
                            <span>Tạm tính ({{ $totalItems }} món)</span>
                            <span class="font-medium">{{ number_format($subTotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between text-espresso/80">
                            <span>Giảm giá (Voucher)</span>
                            <span class="font-medium text-coral">- {{ number_format(session()->has('voucher') ? session('voucher')['discount_amount'] : 0, 0, ',', '.') }} đ</span>
                        </div>
                    </div>

                    {{-- Tổng cộng --}}
                    @php
                        $discount = session()->has('voucher') ? session('voucher')['discount_amount'] : 0;
                        $finalTotal = max(0, $subTotal - $discount);
                    @endphp
                    <div class="flex justify-between items-end mb-8 pt-6 border-t border-espresso/10">
                        <span class="font-bold text-espresso">Tổng cộng</span>
                        <span class="font-black text-3xl text-coral">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block text-center w-full py-4 bg-coral text-white rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all">
                        Tiến hành Thanh toán
                    </a>
                    
                    <a href="{{ route('product.index') }}" class="block text-center mt-4 text-sm text-espresso/60 hover:text-coral hover:underline">
                        ← Tiếp tục chọn món
                    </a>
                </div>
            </div>
        </div>

        @else
        {{-- ========================================= --}}
        {{-- GIAO DIỆN KHI GIỎ HÀNG RỖNG --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-[32px] p-12 text-center shadow-sm border border-espresso/5 max-w-2xl mx-auto mt-8">
            <svg class="w-32 h-32 mx-auto mb-6 text-espresso/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <h2 class="text-2xl font-serif font-bold text-espresso mb-3">Giỏ hàng đang trống!</h2>
            <p class="text-espresso/60 mb-8">Bạn chưa chọn món nước nào. Hãy quay lại thực đơn để chọn cho mình những ly nước thật ngon nhé!</p>
            <a href="{{ route('product.index') }}" class="inline-block bg-coral text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-[#d5523b] shadow-lg shadow-coral/30 transition-all">
                Xem thực đơn ngay
            </a>
        </div>
        @endif

        {{-- ========================================= --}}
        {{-- MODAL CHỈNH SỬA TOPPING TRONG GIỎ HÀNG --}}
        {{-- ========================================= --}}
        <div id="edit-topping-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
            <div class="absolute inset-0 bg-espresso/60 backdrop-blur-sm" onclick="closeEditToppingModal()"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg p-8 transform transition-all scale-95 opacity-0" id="edit-topping-modal-content">
                
                <button onclick="closeEditToppingModal()" class="absolute top-6 right-6 text-espresso/40 hover:text-coral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <h2 class="font-serif font-black text-2xl text-espresso mb-1">Tùy chỉnh Topping</h2>
                <p id="modal-product-name" class="text-espresso/60 text-sm mb-6 font-medium">Đang tải...</p>
                
                {{-- Nơi Javascript sẽ tự động đổ danh sách Topping vào --}}
                <div id="modal-topping-list" class="space-y-3 mb-8 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar"></div>

                {{-- Lưu trữ CartKey ngầm --}}
                <input type="hidden" id="current-cart-key">

                <button onclick="submitToppingChanges()" class="w-full py-4 bg-coral text-white rounded-full font-bold hover:bg-[#d5523b] shadow-lg transition-all">
                    Lưu thay đổi
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ========================================= --}}
{{-- JAVASCRIPT XỬ LÝ AJAX GIỎ HÀNG --}}
{{-- ========================================= --}}
<style>
    /* Làm đẹp thanh cuộn cho danh sách topping trong Modal */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e8634a; border-radius: 10px; }
</style>

<script>
    const csrfToken = '{{ csrf_token() }}';

    // 1. Hàm Xóa sản phẩm khỏi giỏ hàng
    function removeCartItem(cartKey) {
        if(confirm('Bạn có chắc chắn muốn xóa món này khỏi giỏ hàng?')) {
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ _token: csrfToken, cart_key: cartKey })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload(); 
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Lỗi:', error));
        }
    }

    // 2. Hàm Tăng/Giảm số lượng sản phẩm trong giỏ
    function updateCartItemQty(cartKey, change) {
        fetch('{{ route('cart.update') }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ _token: csrfToken, cart_key: cartKey, change: change })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload(); 
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Lỗi:', error));
    }

    // 3. Mở Pop-up và Lấy dữ liệu Topping
    function openEditToppingModal(cartKey) {
        fetch('{{ route('cart.getItem') }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ _token: csrfToken, cart_key: cartKey })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('current-cart-key').value = cartKey;
                document.getElementById('modal-product-name').innerText = data.item_name + ' (' + data.size_name + ')';
                
                let html = '';
                if(data.toppings.length === 0) {
                    html = '<p class="text-center text-gray-500 py-4 italic">Sản phẩm này không có topping nào để thêm.</p>';
                } else {
                    data.toppings.forEach(top => {
                        let formattedPrice = new Intl.NumberFormat('vi-VN').format(top.price);
                        let imgUrl = top.image ? top.image : 'https://via.placeholder.com/200';
                        
                        html += `
                        <div class="flex items-center justify-between p-3 border border-espresso/10 rounded-xl hover:bg-[#FAF7F2] transition-colors group">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <span class="block font-medium text-espresso">${top.name}</span>
                                    <span class="font-bold text-coral">+${formattedPrice}đ</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border border-espresso/20 rounded-full h-10 w-28 bg-white shrink-0 overflow-hidden">
                                <button type="button" onclick="updateModalToppingQty(${top.topping_id}, -1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">-</button>
                                <input type="text" id="modal-topping-qty-${top.topping_id}" value="${top.qty}" readonly class="modal-topping-input w-12 h-full text-center bg-transparent border-none outline-none font-bold text-sm text-espresso focus:ring-0 p-0 m-0 leading-none">
                                <button type="button" onclick="updateModalToppingQty(${top.topping_id}, 1)" class="w-8 h-full flex items-center justify-center text-espresso font-bold hover:text-coral text-lg transition-colors">+</button>
                            </div>
                        </div>
                        `;
                    });
                }
                document.getElementById('modal-topping-list').innerHTML = html;
                
                const modal = document.getElementById('edit-topping-modal');
                const modalContent = document.getElementById('edit-topping-modal-content');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                alert(data.message);
            }
        });
    }

    // 4. Nút Cộng Trừ trong Pop-up
    function updateModalToppingQty(id, change) {
        let input = document.getElementById('modal-topping-qty-' + id);
        let val = parseInt(input.value) + change;
        if(val >= 0) input.value = val;
    }

    // 5. Đóng Pop-up
    function closeEditToppingModal() {
        const modal = document.getElementById('edit-topping-modal');
        const modalContent = document.getElementById('edit-topping-modal-content');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // 6. Lưu Thay Đổi Topping vào Session
    function submitToppingChanges() {
        let cartKey = document.getElementById('current-cart-key').value;
        let toppingsData = {};
        
        document.querySelectorAll('.modal-topping-input').forEach(input => {
            let qty = parseInt(input.value);
            if(qty > 0) {
                let topId = input.id.replace('modal-topping-qty-', '');
                toppingsData[topId] = qty;
            }
        });

        fetch('{{ route('cart.updateToppings') }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ _token: csrfToken, cart_key: cartKey, toppings: toppingsData })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload(); 
            } else {
                alert(data.message);
            }
        });
    }

    // 7. Áp dụng Voucher qua AJAX
    function applyVoucher() {
        const codeInput = document.getElementById('voucher-input');
        const code = codeInput ? codeInput.value.trim() : '';
        if (!code) {
            alert('Vui lòng nhập mã giảm giá!');
            return;
        }

        fetch('{{ route('cart.applyVoucher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ voucher_code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra khi áp dụng mã giảm giá!');
        });
    }

    // 8. Hủy Voucher qua AJAX
    function removeVoucher() {
        fetch('{{ route('cart.removeVoucher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra khi hủy mã giảm giá!');
        });
    }
</script>
@endsection