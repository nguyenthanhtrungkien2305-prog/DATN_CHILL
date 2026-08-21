@extends('layouts.app')

@section('title', 'Lịch Sử Đặt Hàng - Chill Chill')

@section('content')
<style>
    /* ẨN FOOTER ĐỂ ĐỒNG BỘ VỚI TRANG HỒ SƠ */
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-6 md:py-10 px-3 md:px-6">
    
    {{-- Cửa sổ App --}}
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
                <a href="{{ route('user.profile') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl bg-white/10 text-white font-medium text-xs md:text-sm transition-colors shrink-0">Lịch sử đơn hàng</a>
                <a href="{{ route('user.points') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.address') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Địa chỉ nhận hàng</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Nội dung Lịch sử & Bảng thống kê (Tỷ lệ 2/3 cho phép cuộn) --}}
        <div class="w-full md:w-2/3 p-6 md:p-10 bg-white h-full overflow-y-auto custom-scrollbar flex flex-col">
            
            {{-- Header Trang & Nút Đặt món --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-espresso/10">
                <div>
                    <h3 class="font-serif font-bold text-3xl text-espresso mb-1">Lịch sử đặt hàng</h3>
                    <p class="text-xs sm:text-sm text-espresso/60">Theo dõi trạng thái các món ngon bạn đã gọi</p>
                </div>
                <a href="{{ route('product.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-coral/10 border border-coral/20 text-coral hover:bg-coral hover:text-white transition-all text-xs font-bold shrink-0">
                    <span>+ Đặt món mới</span>
                </a>
            </div>

            {{-- BẢNG THỐNG KÊ 4 THẺ CHỈ SỐ (CÂN ĐỐI VỪA VẶN) --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                {{-- 1. Tổng số đơn --}}
                <div class="bg-[#FAF7F2] border border-espresso/10 rounded-2xl p-3.5">
                    <span class="text-[11px] font-bold text-espresso/60 uppercase tracking-wider block mb-1">Tổng số đơn</span>
                    <span class="text-lg font-black text-espresso">{{ $totalOrdersCount ?? $orders->count() }} đơn</span>
                </div>

                {{-- 2. Đang xử lý --}}
                <div class="bg-[#FFFDF0] border border-amber-200/80 rounded-2xl p-3.5">
                    <span class="text-[11px] font-bold text-amber-800/70 uppercase tracking-wider block mb-1">Đang xử lý</span>
                    <span class="text-lg font-black text-amber-700">{{ $processingOrdersCount ?? 0 }} đơn</span>
                </div>

                {{-- 3. Đã hoàn thành --}}
                <div class="bg-[#F2FBF5] border border-emerald-200/80 rounded-2xl p-3.5">
                    <span class="text-[11px] font-bold text-emerald-800/70 uppercase tracking-wider block mb-1">Đã hoàn thành</span>
                    <span class="text-lg font-black text-emerald-700">{{ $completedOrdersCount ?? 0 }} đơn</span>
                </div>

                {{-- 4. Điểm Chill Club --}}
                <div class="bg-[#FFF5F7] border border-pink-200/80 rounded-2xl p-3.5">
                    <span class="text-[11px] font-bold text-coral uppercase tracking-wider block mb-1">Chill Club</span>
                    <span class="text-lg font-black text-coral">{{ number_format($userPoints ?? auth()->user()->point ?? 0) }} pts</span>
                </div>
            </div>

            {{-- BỘ LỌC ĐƠN HÀNG --}}
            <form action="{{ route('user.orders') }}" method="GET" class="mb-6 flex flex-wrap gap-3 items-end bg-[#FAF7F2] p-4 rounded-2xl border border-espresso/10">
                <div>
                    <label class="block text-[10px] font-bold text-espresso/60 mb-1 uppercase tracking-wider">NGÀY ĐẶT</label>
                    <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-coral text-espresso shadow-2xs">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-espresso/60 mb-1 uppercase tracking-wider">TRẠNG THÁI</label>
                    <select name="filter_status" class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-coral text-espresso shadow-2xs cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="processing" {{ request('filter_status') == 'processing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                        <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="canceled" {{ request('filter_status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 bg-espresso text-white text-xs font-bold rounded-xl hover:bg-coral transition-colors shadow-2xs">Lọc</button>
                    @if(request()->filled('filter_date') || request()->filled('filter_status'))
                        <a href="{{ route('user.orders') }}" class="px-3 py-1.5 bg-red-50 text-red-500 border border-red-100 text-xs font-bold rounded-xl hover:bg-red-500 hover:text-white transition-colors">Xóa</a>
                    @endif
                </div>
            </form>

            {{-- DANH SÁCH ĐƠN HÀNG --}}
            @if($orders->isEmpty())
                <div class="text-center py-12 bg-[#FAF7F2]/50 rounded-2xl border border-dashed border-espresso/10">
                    <p class="text-espresso/60 text-xs font-medium mb-3">Bạn chưa có đơn hàng nào thuộc trạng thái này.</p>
                    <a href="{{ route('product.index') }}" class="inline-block px-5 py-2 bg-coral text-white font-bold text-xs rounded-full hover:bg-espresso transition-colors">Khám phá Thực đơn</a>
                </div>
            @else
                <div class="space-y-3 pb-4">
                    @foreach($orders as $order)
                        @php $items = json_decode($order->items, true); @endphp
                        
                        <a href="{{ route('user.orders.show', $order->order_id) }}" class="block bg-white rounded-2xl p-4 shadow-2xs border border-espresso/10 hover:border-coral/40 hover:shadow-md transition-all group">
                            <div class="flex flex-wrap justify-between items-center mb-2.5 gap-2 border-b border-gray-100 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-espresso text-sm">#{{ $order->order_id }}</span>
                                    @if($order->status == 'pending') <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 uppercase tracking-wider">Chờ xác nhận</span>
                                    @elseif($order->status == 'processing') <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Đang chuẩn bị</span>
                                    @elseif($order->status == 'completed') <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Hoàn thành</span>
                                    @else <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 uppercase tracking-wider">Đã hủy</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}</div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="flex -space-x-3 shrink-0">
                                    @if(is_array($items))
                                        @foreach(array_slice($items, 0, 3) as $item)
                                            <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=200&auto=format&fit=crop' }}" class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 object-cover relative z-10">
                                        @endforeach
                                        @if(count($items) > 3)
                                            <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 text-gray-500 font-bold text-[10px] flex items-center justify-center relative z-0">+{{ count($items) - 3 }}</div>
                                        @endif
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-espresso line-clamp-1">
                                        {{ is_array($items) && isset($items[0]['name']) ? $items[0]['name'] : 'Đơn hàng Chill' }} 
                                        {{ is_array($items) && count($items) > 1 ? 'và '.(count($items)-1).' món khác' : '' }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-[10px] text-gray-400">Tổng thanh toán</p>
                                    <span class="font-black text-base text-coral block {{ in_array($order->status, ['pending', 'processing']) ? 'mb-1' : '' }}">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                    @if(in_array($order->status, ['pending', 'processing']))
                                        <form action="{{ route('user.orders.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->order_id }} không?');" class="inline-block" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 text-[10px] rounded-lg font-bold hover:bg-red-600 hover:text-white transition-colors">
                                                ✕ Hủy đơn
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </a>

                    @endforeach
                </div>
            @endif
        </div>
        
    </div>
</div>
@endsection