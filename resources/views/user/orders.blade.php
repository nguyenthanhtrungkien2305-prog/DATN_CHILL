@extends('layouts.app')

@section('title', 'Đơn Hàng Của Tôi - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-10 px-4">
    <div class="w-full max-w-5xl bg-white rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-[80vh] min-h-[550px] max-h-[800px]">
        
        <div class="w-full md:w-1/3 bg-espresso text-cream p-8 md:p-10 flex flex-col h-full shrink-0">
            <h2 class="font-serif font-bold text-2xl text-white mb-8">Tài khoản</h2>
            <nav class="space-y-2 flex-1">
                <a href="{{ route('user.profile') }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="block px-4 py-3 rounded-xl bg-white/10 text-white font-medium transition-colors">Đơn hàng của tôi</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="mt-auto px-4 py-3 text-coral hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        <div class="w-full md:w-2/3 p-8 md:p-12 bg-gray-50/50 h-full overflow-y-auto custom-scrollbar">
            <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Lịch sử đặt hàng</h3>
            <p class="text-espresso/60 mb-8">Theo dõi trạng thái các món ngon bạn đã gọi</p>

            <form action="{{ route('user.orders') }}" method="GET" class="mb-8 flex flex-wrap gap-4 items-end bg-white p-5 rounded-[20px] border border-gray-200 shadow-sm">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Ngày đặt</label>
                    <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="px-4 py-2 w-[150px] bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-coral text-sm text-espresso font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Trạng thái</label>
                    <select name="filter_status" class="px-4 py-2 w-[160px] bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-coral text-sm text-espresso font-bold transition-all cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="processing" {{ request('filter_status') == 'processing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                        <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="canceled" {{ request('filter_status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="flex gap-2 ml-auto">
                    <button type="submit" class="px-5 py-2 bg-espresso text-white text-sm font-black rounded-xl hover:bg-coral transition-colors shadow-md">Lọc</button>
                    @if(request()->filled('filter_date') || request()->filled('filter_status'))
                        <a href="{{ route('user.orders') }}" class="px-4 py-2 bg-red-50 text-red-500 border border-red-100 text-sm font-bold rounded-xl hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center">Xóa</a>
                    @endif
                </div>
            </form>

            @if($orders->isEmpty())
                <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-gray-300">
                    <div class="text-6xl mb-4 opacity-50">🥤</div>
                    <p class="text-espresso/60 font-medium mb-4">Bạn chưa có đơn hàng nào!</p>
                    <a href="{{ route('product.index') }}" class="inline-block px-6 py-2.5 bg-coral text-white font-bold rounded-full hover:bg-[#d5523b] transition-colors">Menu Đồ Uống</a>
                </div>
            @else
                <div class="space-y-4 pr-2 pb-4">
                    @foreach($orders as $order)
                        @php $items = json_decode($order->items, true); @endphp
                        
                        {{-- THẺ ĐƠN HÀNG BÊN NGOÀI BÂY GIỜ LÀ 1 ĐƯỜNG LINK --}}
                        <a href="{{ route('user.orders.show', $order->order_id) }}" class="block bg-white rounded-[20px] p-5 shadow-sm border border-espresso/5 hover:border-coral/50 hover:shadow-md transition-all group">
                            
                            <div class="flex flex-wrap justify-between items-center mb-3 gap-4 border-b border-gray-50 pb-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-espresso text-lg">#{{ $order->order_id }}</span>
                                    @if($order->status == 'pending') <span class="px-3 py-1 rounded-md text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase tracking-widest">Chờ xác nhận</span>
                                    @elseif($order->status == 'processing') <span class="px-3 py-1 rounded-md text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-widest">Đang chuẩn bị</span>
                                    @elseif($order->status == 'completed') <span class="px-3 py-1 rounded-md text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-widest">Hoàn thành</span>
                                    @else <span class="px-3 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-widest">Đã hủy</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-400 font-medium">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}</div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="flex -space-x-4 shrink-0">
                                    @foreach(array_slice($items, 0, 3) as $item)
                                        <img src="{{ $item['image'] ?? 'https://via.placeholder.com/100' }}" class="w-12 h-12 rounded-full border-2 border-white bg-gray-100 object-cover relative z-10">
                                    @endforeach
                                    @if(count($items) > 3)
                                        <div class="w-12 h-12 rounded-full border-2 border-white bg-gray-100 text-gray-500 font-bold text-xs flex items-center justify-center relative z-0">+{{ count($items) - 3 }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-espresso line-clamp-1">{{ $items[0]['name'] ?? 'Sản phẩm' }} {{ count($items) > 1 ? 'và '.(count($items)-1).' món khác' : '' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs text-gray-400 mb-0.5">Tổng thanh toán</p>
                                    <span class="font-black text-lg text-coral">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="shrink-0 pl-2 text-coral opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
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