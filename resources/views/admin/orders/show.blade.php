@extends('admin.layouts.app')

@section('title')
    Chi tiết Đơn hàng #{{ $order->order_id }} - Chill Chill Admin
@endsection
@section('page_title')
    Chi tiết Đơn hàng #{{ $order->order_id }}
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 font-medium transition flex items-center gap-1.5 text-sm">
        ← Quay lại danh sách đơn hàng
    </a>
</div>

{{-- Thông báo thành công --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative mb-6 shadow-sm flex items-center gap-2">
        <span>✨</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Cột Trái: Chi tiết giỏ hàng và Trạng thái --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Hộp cập nhật trạng thái đơn hàng --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>⚙️</span> Xử lý đơn hàng
            </h3>
            <form action="{{ route('orders.update_status', $order->order_id) }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end sm:items-center">
                @csrf
                @method('PUT')
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Trạng thái đơn hàng</label>
                    <select name="status" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 text-espresso font-semibold">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý (Processing)</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Đã hoàn thành (Completed)</option>
                        <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Đã hủy (Canceled)</option>
                    </select>
                </div>
                <button type="submit" class="w-full sm:w-auto bg-[#e8634a] hover:bg-[#d5523b] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-md shadow-[#e8634a]/20">
                    Cập nhật
                </button>
            </form>
        </div>

        {{-- Danh sách các món --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b pb-3">
                📋 Chi tiết giỏ hàng
            </h3>
            
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <div class="py-4 flex gap-4 items-start">
                    <div class="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden shrink-0 border border-gray-100">
                        <img src="{{ $item['image'] ?? 'https://via.placeholder.com/100' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="font-bold text-espresso text-base">{{ $item['name'] }}</h4>
                                <p class="text-xs text-gray-400 mt-1">
                                    Size: <span class="font-bold text-gray-600">{{ $item['size_name'] ?? 'Mặc định' }}</span> | Số lượng: <span class="font-bold text-[#e8634a]">x{{ $item['quantity'] }}</span>
                                </p>
                            </div>
                            <span class="font-bold text-gray-900 text-sm">
                                {{ number_format(($item['price'] + ($item['topping_total'] ?? 0)) * $item['quantity'], 0, ',', '.') }}đ
                            </span>
                        </div>

                        {{-- Hiển thị Toppings --}}
                        @if(isset($item['toppings']) && is_array($item['toppings']) && count(array_filter($item['toppings'])) > 0)
                            <div class="mt-2 bg-gray-50 p-2 rounded-lg border border-gray-100 flex flex-wrap gap-1.5">
                                @foreach($item['toppings'] as $t_id => $t_data)
                                    @if(is_array($t_data))
                                        {{-- Định dạng từ giỏ hàng online: ['name' => ..., 'price' => ..., 'qty' => ...] --}}
                                        @if(isset($t_data['qty']) && $t_data['qty'] > 0)
                                            <span class="text-[11px] text-espresso/70 bg-white px-2 py-0.5 rounded border border-gray-200">
                                                + {{ $t_data['name'] }} (x{{ $t_data['qty'] }}) (+{{ number_format($t_data['price'] * $t_data['qty'], 0) }}đ)
                                            </span>
                                        @endif
                                    @else
                                        {{-- Định dạng từ POS: [topping_id => qty] --}}
                                        @php
                                            $qty = $t_data;
                                            $toppingName = DB::table('toppings')->where('topping_id', $t_id)->value('name');
                                            $toppingPrice = DB::table('toppings')->where('topping_id', $t_id)->value('price') ?? 0;
                                        @endphp
                                        @if($qty > 0 && $toppingName)
                                            <span class="text-[11px] text-espresso/70 bg-white px-2 py-0.5 rounded border border-gray-200">
                                                + {{ $toppingName }} (x{{ $qty }}) (+{{ number_format($toppingPrice * $qty, 0) }}đ)
                                            </span>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tính toán tổng tiền --}}
            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Tạm tính</span>
                    <span class="font-medium text-gray-900">{{ number_format($order->total_amount + ($order->discount_amount ?? 0), 0, ',', '.') }}đ</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-red-500">
                        <span>Giảm giá (Voucher: {{ $voucher->code ?? 'N/A' }})</span>
                        <span class="font-medium">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span>Phí vận chuyển</span>
                    <span class="font-semibold text-green-600">Miễn phí</span>
                </div>
                <div class="flex justify-between items-end pt-3 border-t border-dashed border-gray-100">
                    <span class="font-bold text-espresso text-base">Tổng thanh toán</span>
                    <span class="font-black text-2xl text-[#e8634a]">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cột Phải: Thông tin Khách hàng & Thành viên --}}
    <div class="space-y-6">
        
        {{-- Thông tin giao nhận/đơn hàng --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 border-b pb-3 mb-4">
                📦 Thông tin giao nhận
            </h3>
            
            <div class="space-y-3.5 text-sm">
                <div>
                    <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Loại hình đặt hàng</span>
                    @if($order->order_type === 'pos')
                        <span class="font-bold text-purple-600">💻 POS tại quầy</span>
                    @elseif($order->order_type === 'dine-in')
                        <span class="font-bold text-blue-600">☕ Phục vụ tại bàn (Bàn {{ $order->table_number ?? 'N/A' }})</span>
                    @else
                        <span class="font-bold text-amber-600">🛵 Giao hàng tận nơi</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Họ và tên nhận hàng</span>
                    <span class="font-bold text-espresso">{{ $order->customer_name }}</span>
                </div>
                @if($order->customer_phone)
                <div>
                    <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Số điện thoại liên hệ</span>
                    <span class="font-medium text-espresso">{{ $order->customer_phone }}</span>
                </div>
                @endif
                @if($order->shipping_address)
                <div>
                    <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Địa chỉ giao hàng / Ghi chú bàn</span>
                    <span class="font-medium text-gray-600 block bg-gray-50 p-2.5 rounded-xl border border-gray-100 leading-relaxed">{{ $order->shipping_address }}</span>
                </div>
                @endif
                <div>
                    <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Phương thức thanh toán</span>
                    <span class="font-semibold text-espresso">
                        @if($order->payment_method === 'cash')
                            💵 Tiền mặt khi nhận hàng
                        @else
                            📱 Chuyển khoản VietQR
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Lấy thông tin tài khoản thành viên liên kết (Đóng vai trò "Lấy thông tin từ User về") --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 border-b pb-3 mb-4">
                👤 Thành viên đặt đơn
            </h3>

            @if($member)
                {{-- Thẻ Thành viên / VIP --}}
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-[#2B2623] to-[#423a35] text-white shadow-md relative overflow-hidden">
                        <div class="absolute right-2 bottom-2 text-6xl opacity-10">☕</div>
                        <div class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-1">THÀNH VIÊN VIP</div>
                        <h4 class="font-bold text-lg mb-4">{{ $member->name }}</h4>
                        
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-white/50 block mb-0.5">Điểm tích lũy</span>
                                <span class="text-xl font-bold text-amber-300">{{ $member->point }} Điểm</span>
                            </div>
                            <div>
                                <span class="text-white/50 block mb-0.5">Tổng chi tiêu</span>
                                <span class="text-base font-bold">{{ number_format($memberTotalSpent, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm border-t pt-4">
                        <div>
                            <span class="text-gray-400 block text-xs font-semibold mb-0.5">Email tài khoản</span>
                            <span class="font-medium text-espresso">{{ $member->email ?? 'Chưa đăng ký email' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs font-semibold mb-0.5">Số điện thoại đăng ký</span>
                            <span class="font-medium text-espresso">{{ $member->phone ?? 'Chưa liên kết SĐT' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs font-semibold mb-0.5">Số lượng đơn đã đặt</span>
                            <span class="font-bold text-[#e8634a]">{{ $memberOrderCount }} đơn hàng</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('orders.index', ['user_id' => $member->user_id]) }}" class="block text-center bg-gray-100 hover:bg-gray-200 text-espresso font-bold py-2 px-4 rounded-xl text-xs transition">
                            📂 Xem lịch sử đặt đơn của thành viên này
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-dashed border-gray-200 p-6 rounded-2xl text-center text-gray-500">
                    <span class="text-4xl block mb-2">👤</span>
                    <h4 class="font-bold text-espresso text-sm mb-1">Khách vãng lai</h4>
                    <p class="text-xs leading-normal">Đơn hàng này được đặt bởi khách vãng lai trực tiếp tại quầy hoặc không đăng nhập thành viên online.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
