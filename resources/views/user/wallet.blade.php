@extends('layouts.app')

@section('title', 'Ví Số Dư Hoàn Tiền - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-6 md:py-10 px-3 md:px-6">
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
                <a href="{{ route('user.orders') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Lịch sử đơn hàng</a>
                <a href="{{ route('user.points') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.wallet') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl bg-white/10 text-white font-medium text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tiền hoàn</span>
                    <span class="bg-coral text-white text-[10px] md:text-xs font-bold px-2 py-0.5 rounded-full">{{ number_format(auth()->user()->wallet_balance ?? 0, 0, ',', '.') }}đ</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors shrink-0">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Ví Số Dư Hoàn Tiền --}}
        <div class="w-full md:w-2/3 p-5 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar">
            <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Tiền hoàn</h3>
            <p class="text-espresso/60 mb-6 text-sm">Quản lý số dư tiền được hoàn từ các đơn hàng đã hủy</p>

            {{-- Card Hiển thị Số dư --}}
            <div class="bg-gradient-to-r from-espresso via-[#3d332e] to-espresso p-8 rounded-3xl text-white shadow-xl mb-8 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-coral/20 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-cream/70 font-semibold uppercase tracking-wider">Số dư khả dụng</span>
                    <span class="text-xs bg-coral/30 text-cream px-3 py-1 rounded-full font-bold">Ví Hoàn Tiền</span>
                </div>
                <div class="text-4xl font-black font-mono tracking-tight text-cream mb-2">
                    {{ number_format($user->wallet_balance ?? 0, 0, ',', '.') }} <span class="text-2xl font-normal text-coral">VNĐ</span>
                </div>
                <p class="text-xs text-cream/60">Số tiền này sẽ tự động trừ vào tổng thanh toán khi bạn mua hàng lần tới!</p>
            </div>

            {{-- Khối hướng dẫn --}}
            <div class="bg-amber-50/80 border border-amber-200 p-5 rounded-2xl mb-8 flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm">💡</div>
                <div class="text-xs text-espresso/80 leading-relaxed">
                    <strong class="text-espresso font-bold block mb-0.5">Cách thức hoạt động:</strong>
                    - Khi bạn hủy đơn hàng đã thanh toán, 100% số tiền sẽ được tự động hoàn vào Ví này.<br>
                    - Khi thanh toán đơn hàng tiếp theo tại <a href="{{ route('product.index') }}" class="text-coral underline font-bold">Thực đơn</a>, tick chọn <strong>"Sử dụng Số dư hoàn tiền"</strong> để khấu trừ số tiền tương ứng!
                </div>
            </div>

            {{-- Lịch sử đơn hàng hoàn tiền --}}
            <h4 class="font-bold text-lg text-espresso mb-4">Lịch sử đơn hàng hoàn tiền & sử dụng ví</h4>
            <div class="space-y-3">
                @forelse($refundedOrders as $ord)
                    <div class="p-4 bg-[#FAF7F2] rounded-2xl border border-gray-100 flex items-center justify-between text-sm">
                        <div>
                            <div class="font-bold text-espresso flex items-center gap-2">
                                <span>Đơn hàng #{{ $ord->order_id }}</span>
                                @if($ord->status === 'cancelled')
                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">Đã hủy (Được hoàn tiền)</span>
                                @else
                                    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">Thanh toán</span>
                                @endif
                            </div>
                            <span class="text-xs text-espresso/50 block mt-1">{{ date('d/m/Y H:i', strtotime($ord->updated_at ?? $ord->created_at)) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold font-mono text-base {{ $ord->status === 'cancelled' ? 'text-green-600' : 'text-espresso' }}">
                                {{ $ord->status === 'cancelled' ? '+' : '' }}{{ number_format($ord->total_amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-espresso/40 text-sm italic">
                        Chưa có lịch sử giao dịch hoàn tiền nào.
                    </div>
                @endforelse
            </div>

        </div>
        
    </div>
</div>
@endsection
