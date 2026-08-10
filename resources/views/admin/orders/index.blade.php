@extends('admin.layouts.app')

@section('title', 'Quản lý Đơn hàng - Chill Chill Admin')
@section('page_title', 'Danh sách Đơn hàng')

@section('content')
{{-- Thông báo --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative mb-6 shadow-sm flex items-center gap-2">
        <span>✨</span>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl relative mb-6 shadow-sm flex items-center gap-2">
        <span>⚠️</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Thanh lọc, tìm kiếm --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 space-y-4">
    <form action="{{ Route::has('orders.index') ? route('orders.index') : route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Tìm kiếm từ khóa --}}
        <div class="col-span-1 md:col-span-2">
            <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Tìm kiếm đơn hàng</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập mã đơn, tên, số điện thoại..." class="flex-1 px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 focus:bg-white transition-all text-gray-800">
            </div>
        </div>

        {{-- Lọc trạng thái --}}
        <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Trạng thái đơn</label>
            <select name="status" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 text-gray-800">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý (Processing)</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã hoàn thành (Completed)</option>
                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Đã hủy (Canceled)</option>
            </select>
        </div>

        {{-- Lọc loại đơn hàng --}}
        <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Loại đơn hàng</label>
            <select name="order_type" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 text-gray-800">
                <option value="">-- Tất cả loại đơn --</option>
                <option value="pos" {{ request('order_type') === 'pos' ? 'selected' : '' }}>Đơn POS tại quầy</option>
                <option value="delivery" {{ request('order_type') === 'delivery' ? 'selected' : '' }}>Giao hàng tận nơi</option>
                <option value="dine-in" {{ request('order_type') === 'dine-in' ? 'selected' : '' }}>Ăn tại quán</option>
            </select>
        </div>

        <div class="col-span-1 md:col-span-4 flex justify-end gap-2 pt-2 border-t border-gray-50">
            @if(request()->anyFilled(['search', 'status', 'order_type', 'user_id']))
                <a href="{{ Route::has('orders.index') ? route('orders.index') : route('admin.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-xl transition text-sm font-medium">Xóa bộ lọc</a>
            @endif
            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-2 rounded-xl transition text-sm font-medium shadow-sm">Áp dụng lọc</button>
        </div>
    </form>
</div>

{{-- Bảng danh sách đơn hàng --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                    <th class="p-4 font-semibold w-20">Mã đơn</th>
                    <th class="p-4 font-semibold">Khách hàng</th>
                    <th class="p-4 font-semibold">Loại đơn</th>
                    <th class="p-4 font-semibold">Thanh toán</th>
                    <th class="p-4 font-semibold text-right">Tổng tiền</th>
                    <th class="p-4 font-semibold text-center">Trạng thái</th>
                    <th class="p-4 font-semibold">Thời gian đặt</th>
                    <th class="p-4 font-semibold text-center w-28">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="p-4 font-bold text-gray-500">#{{ $order->order_id }}</td>
                    <td class="p-4">
                        <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                        @if($order->customer_phone)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $order->customer_phone }}</div>
                        @elseif($order->user_id)
                            <span class="inline-block mt-1 px-2 py-0.5 rounded bg-orange-50 text-orange-600 text-[10px] font-bold">Thành viên</span>
                        @else
                            <div class="text-xs text-gray-400 mt-0.5 italic">Khách vãng lai</div>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($order->order_type === 'pos')
                            <span class="px-2.5 py-1 rounded-full text-xs bg-purple-50 text-purple-600 font-semibold border border-purple-100">💻 POS tại quầy</span>
                        @elseif($order->order_type === 'dine-in' || $order->order_type === 'at_table')
                            <span class="px-2.5 py-1 rounded-full text-xs bg-blue-50 text-blue-600 font-semibold border border-blue-100">☕ Tại bàn (Bàn {{ $order->table_number ?? 'N/A' }})</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs bg-amber-50 text-amber-600 font-semibold border border-amber-100">🛵 Giao hàng</span>
                        @endif
                    </td>
                    <td class="p-4 uppercase font-semibold text-xs text-gray-500">
                        @if($order->payment_method === 'cash')
                            💵 Tiền mặt
                        @else
                            📱 Chuyển khoản QR
                        @endif
                    </td>
                    <td class="p-4 text-right font-bold text-gray-900">
                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                    </td>
                    <td class="p-4 text-center">
                        @if($order->status === 'pending')
                            <span class="px-3 py-1 rounded-full text-xs bg-red-50 text-red-600 font-bold border border-red-100 animate-pulse">Chờ xác nhận</span>
                        @elseif($order->status === 'processing')
                            <span class="px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-600 font-bold border border-blue-100">Đang làm món</span>
                        @elseif($order->status === 'completed')
                            <span class="px-3 py-1 rounded-full text-xs bg-green-50 text-green-600 font-bold border border-green-100">Hoàn thành</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-400 font-bold">Đã hủy</span>
                        @endif
                    </td>
                    <td class="p-4 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('H:i d/m/Y') }}
                    </td>
                    <td class="p-4 text-center">
                        @if(Route::has('orders.show'))
                            <a href="{{ route('orders.show', $order->order_id) }}" class="inline-block bg-[#e8634a] hover:bg-[#d5523b] text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                Chi tiết
                            </a>
                        @else
                            <span class="text-xs text-gray-400">#{{ $order->order_id }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-400 italic">Không tìm thấy đơn hàng nào khớp với điều kiện lọc.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
