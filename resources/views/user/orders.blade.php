@extends('layouts.app')

@section('title', 'Đơn Hàng Của Tôi - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen py-24">
    <div class="max-w-5xl mx-auto px-6 reveal">
        
        <div class="bg-white rounded-[40px] shadow-sm border border-espresso/5 overflow-hidden flex flex-col md:flex-row">
            
            {{-- CỘT TRÁI: Menu --}}
            <div class="w-full md:w-1/3 bg-espresso text-cream p-8 md:p-10 flex flex-col">
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

            {{-- CỘT PHẢI: Lịch sử đơn hàng --}}
            <div class="w-full md:w-2/3 p-8 md:p-12 bg-gray-50/50">
                <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Lịch sử đặt hàng</h3>
                <p class="text-espresso/60 mb-8">Theo dõi trạng thái các món ngon bạn đã gọi</p>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($orders->isEmpty())
                    <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-gray-300">
                        <div class="text-6xl mb-4 opacity-50">🥤</div>
                        <p class="text-espresso/60 font-medium mb-4">Bạn chưa có đơn hàng nào!</p>
                        <a href="{{ route('product.index') }}" class="inline-block px-6 py-2.5 bg-coral text-white font-bold rounded-full hover:bg-[#d5523b] transition-colors">Menu Đồ Uống</a>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($orders as $order)
                            @php 
                                $items = json_decode($order->items, true); 
                            @endphp
                            
                            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-espresso/5 hover:border-coral/30 transition-all">
                                
                                {{-- Header của Card Đơn Hàng --}}
                                <div class="flex flex-wrap justify-between items-center mb-4 pb-4 border-b border-gray-100 gap-4">
                                    <div>
                                        <p class="font-bold text-espresso text-lg">Mã đơn: #{{ $order->order_id }}</p>
                                        <p class="text-sm text-espresso/60">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($order->status == 'pending')
                                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Chờ xác nhận</span>
                                        @elseif($order->status == 'processing')
                                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Đang chuẩn bị</span>
                                        @elseif($order->status == 'completed')
                                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Hoàn thành</span>
                                        @else
                                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Đã hủy</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Phương thức giao --}}
                                <div class="mb-4 bg-gray-50 p-3 rounded-xl text-sm flex items-start gap-2">
                                    @if($order->order_type == 'delivery')
                                        <span class="text-lg">🛵</span>
                                        <p class="text-espresso/80"><span class="font-bold">Giao đến:</span> {{ $order->shipping_address }}</p>
                                    @else
                                        <span class="text-lg">🍽️</span>
                                        <p class="text-espresso/80"><span class="font-bold">Tại quán:</span> Đang ngồi tại Bàn số {{ $order->table_number }}</p>
                                    @endif
                                </div>

                                {{-- Danh sách món (Chỉ hiển thị tối đa 2 món, còn lại thu gọn) --}}
                                <div class="space-y-3 mb-4">
                                    @foreach(array_slice($items, 0, 2) as $item)
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                                <img src="{{ $item['image'] ?? 'https://via.placeholder.com/100' }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-espresso text-sm line-clamp-1">{{ $item['name'] }}</h4>
                                                <p class="text-xs text-espresso/60">Size {{ $item['size_name'] }} | x{{ $item['quantity'] }}</p>
                                            </div>
                                            <div class="font-bold text-espresso text-sm">
                                                {{ number_format(($item['price'] + $item['topping_total']) * $item['quantity'], 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if(count($items) > 2)
                                        <p class="text-xs text-center text-espresso/50 pt-2">... và {{ count($items) - 2 }} món khác</p>
                                    @endif
                                </div>

                                {{-- Footer: Tổng tiền --}}
                                <div class="flex justify-between items-end pt-4 border-t border-gray-100">
                                    <span class="font-medium text-espresso/80 text-sm">Tổng cộng:</span>
                                    <span class="font-black text-xl text-coral">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</div>
@endsection