@extends('layouts.app')

@section('title', 'Thanh toán qua mã QR - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-16 min-h-screen">
    <div class="max-w-3xl mx-auto px-6">
        
        <div class="bg-white rounded-[40px] shadow-lg border border-espresso/5 p-8 md:p-12 text-center">
            
            {{-- Tiêu đề --}}
            <h1 class="font-serif font-black text-3xl text-espresso mb-2">Thanh toán chuyển khoản</h1>
            <p class="text-espresso/60 mb-8">Vui lòng quét mã QR dưới đây hoặc chuyển khoản theo thông tin chi tiết</p>

            {{-- Đếm ngược thời gian --}}
            <div class="bg-coral/5 border border-coral/10 rounded-2xl p-4 inline-flex items-center gap-2 mb-8">
                <span class="text-lg">⏳</span>
                <span class="text-espresso font-medium text-sm">Đơn hàng sẽ hết hạn sau:</span>
                <span class="font-bold text-coral text-lg" id="countdown-timer">10:00</span>
            </div>

            {{-- Nội dung chính --}}
            <div class="flex flex-col md:flex-row gap-8 items-center justify-center text-left">
                
                {{-- Mã QR --}}
                <div class="bg-cream/30 p-4 rounded-3xl border border-espresso/5 shadow-inner shrink-0">
                    @php
                        $qrUrl = "https://img.vietqr.io/image/mbbank-0385792442-compact2.png?amount=" . (int)$order->total_amount . "&addInfo=CHILLCHILL%20" . $order->order_id . "&accountName=NGUYEN%20THANH%20TRUNG%20KIEN";
                    @endphp
                    <img src="{{ $qrUrl }}" alt="VietQR" class="w-64 h-64 md:w-72 md:h-72 object-contain rounded-xl">
                    <p class="text-center text-[10px] text-espresso/40 mt-2 font-bold tracking-wider uppercase">Quét mã bằng App Ngân hàng</p>
                </div>

                {{-- Chi tiết thông tin --}}
                <div class="flex-1 w-full space-y-4">
                    <h3 class="font-bold text-espresso text-lg pb-2 border-b border-gray-100">Thông tin chuyển khoản</h3>
                    
                    {{-- Ngân hàng --}}
                    <div>
                        <span class="text-xs text-espresso/50 block font-medium">Ngân hàng</span>
                        <span class="text-espresso font-bold text-sm">Ngân hàng Quân Đội (MB Bank)</span>
                    </div>

                    {{-- Số tài khoản --}}
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div>
                            <span class="text-xs text-espresso/50 block font-medium">Số tài khoản</span>
                            <span class="text-espresso font-extrabold text-base" id="copy-acc">0385792442</span>
                        </div>
                        <button onclick="copyToClipboard('0385792442', this)" class="bg-espresso text-white hover:bg-coral px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                            Sao chép
                        </button>
                    </div>

                    {{-- Tên người nhận --}}
                    <div>
                        <span class="text-xs text-espresso/50 block font-medium">Chủ tài khoản</span>
                        <span class="text-espresso font-bold text-sm">NGUYEN THANH TRUNG KIEN</span>
                    </div>

                    {{-- Số tiền --}}
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div>
                            <span class="text-xs text-espresso/50 block font-medium">Số tiền chuyển khoản</span>
                            <span class="text-coral font-black text-lg">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <button onclick="copyToClipboard('{{ (int)$order->total_amount }}', this)" class="bg-espresso text-white hover:bg-coral px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                            Sao chép
                        </button>
                    </div>

                    {{-- Nội dung --}}
                    <div class="flex items-center justify-between bg-coral/5 p-3 rounded-xl border border-coral/10">
                        <div>
                            <span class="text-xs text-coral block font-bold">Nội dung chuyển khoản (Bắt buộc)</span>
                            <span class="text-espresso font-black text-base" id="copy-memo">CHILLCHILL {{ $order->order_id }}</span>
                        </div>
                        <button onclick="copyToClipboard('CHILLCHILL {{ $order->order_id }}', this)" class="bg-coral text-white hover:bg-espresso px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                            Sao chép
                        </button>
                    </div>
                </div>

            </div>

            {{-- Lưu ý --}}
            <div class="mt-10 p-5 bg-yellow-50/50 border border-yellow-200/50 rounded-2xl text-left text-sm text-espresso/80 leading-relaxed">
                <span class="font-bold text-yellow-700">Lưu ý quan trọng:</span> Bạn cần ghi chính xác nội dung chuyển khoản là <strong class="text-coral">CHILLCHILL {{ $order->order_id }}</strong> để hệ thống tự động xác nhận đơn hàng nhanh nhất. Sau khi chuyển tiền, vui lòng bấm nút xác nhận bên dưới.
            </div>

            {{-- Nút xác nhận --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('cart.index') }}" class="py-4 px-8 border border-gray-300 rounded-full font-bold text-espresso/70 hover:bg-gray-50 transition-all text-center">
                    Quay lại Giỏ hàng
                </a>
                <a href="{{ route('user.orders') }}" class="py-4 px-8 bg-coral text-white hover:bg-[#d5523b] rounded-full font-bold text-lg shadow-lg shadow-coral/20 transition-all text-center">
                    Tôi đã chuyển khoản thành công
                </a>
            </div>

        </div>

    </div>
</div>

<script>
    // === BỘ ĐẾM NGƯỢC 10 PHÚT ===
    let timeLimit = 600; // 10 phút tính bằng giây
    const timerDisplay = document.getElementById('countdown-timer');

    const interval = setInterval(() => {
        let minutes = Math.floor(timeLimit / 60);
        let seconds = timeLimit % 60;

        seconds = seconds < 10 ? '0' + seconds : seconds;
        minutes = minutes < 10 ? '0' + minutes : minutes;

        timerDisplay.innerText = `${minutes}:${seconds}`;

        if (timeLimit <= 0) {
            clearInterval(interval);
            alert('Thời gian thanh toán đã hết hạn! Vui lòng thực hiện đặt hàng lại.');
            window.location.href = '{{ route('cart.index') }}';
        }
        timeLimit--;
    }, 1000);

    // === HÀM SAO CHÉP CLIPBOARD ===
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.innerText;
            button.innerText = 'Đã chép! ✓';
            button.classList.add('bg-green-500', 'text-white');
            button.classList.remove('bg-espresso', 'bg-coral');

            setTimeout(() => {
                button.innerText = originalText;
                button.classList.remove('bg-green-500');
                if (button.classList.contains('hover:bg-espresso')) {
                    button.classList.add('bg-coral');
                } else {
                    button.classList.add('bg-espresso');
                }
            }, 1500);
        }).catch(err => {
            console.error('Không thể sao chép: ', err);
        });
    }

    // === POLLING STATUS CHECK ===
    const checkInterval = setInterval(() => {
        fetch('{{ route('checkout.check_status', $order->order_id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'processing' || data.status === 'completed') {
                    clearInterval(checkInterval);
                    alert('🎉 Thanh toán thành công! Hệ thống đang chuẩn bị đơn hàng cho bạn.');
                    window.location.href = '{{ route('user.orders') }}';
                }
            })
            .catch(err => console.error('Lỗi kiểm tra trạng thái:', err));
    }, 3000);

</script>
@endsection
