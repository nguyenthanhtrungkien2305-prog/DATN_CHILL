@extends('layouts.app')

@section('title', 'Tích Điểm & Đổi Voucher - Điểm Cộng Coffee')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-10 px-4">
    
    <div class="w-full max-w-5xl bg-white rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-[80vh] min-h-[550px] max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-8 md:p-10 flex flex-col h-full shrink-0">
            <h2 class="font-serif font-bold text-2xl text-white mb-8">Tài khoản</h2>
            <nav class="space-y-2 flex-1">
                <a href="{{ route('user.profile') }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Thông tin cá nhân</a>
                <a href="{{ route('user.orders') }}" class="block px-4 py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white transition-colors">Đơn hàng của tôi</a>
                <a href="{{ route('user.points') }}" class="block px-4 py-3 rounded-xl bg-white/10 text-white font-medium transition-colors flex items-center justify-between">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral text-white text-xs font-black px-2.5 py-0.5 rounded-full shadow-sm">🏆 {{ number_format($user->point ?? 0) }}p</span>
                </a>
            </nav>
            <a href="{{ route('logout') }}" class="mt-auto px-4 py-3 text-coral hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Nội dung Tích điểm & Đổi Voucher --}}
        <div class="w-full md:w-2/3 p-6 md:p-10 bg-gray-50/50 h-full overflow-y-auto custom-scrollbar space-y-8">
            
            <div>
                <h3 class="font-serif font-bold text-3xl text-espresso mb-1">Tích điểm & Ưu đãi</h3>
                <p class="text-xs text-espresso/60">Tích lũy điểm từ mỗi đơn hàng hoàn tất để đổi mã giảm giá hấp dẫn</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 shadow-xs">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 shadow-xs">
                    <span>⚠️ {{ session('error') }}</span>
                </div>
            @endif

            {{-- 1. THẺ ĐIỂM TÍCH LŨY THÀNH VIÊN --}}
            <div class="bg-gradient-to-br from-[#3e2723] via-[#4a2e29] to-[#1b100e] text-white p-6 rounded-[28px] shadow-xl relative overflow-hidden border border-amber-500/20">
                <div class="absolute -right-6 -bottom-6 w-36 h-36 bg-coral/10 rounded-full blur-2xl"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <span class="text-amber-400/80 uppercase tracking-widest text-[10px] font-black">Ví Điểm Điểm Cộng</span>
                        <h4 class="text-4xl font-black text-amber-400 mt-1 tracking-tight">{{ number_format($user->point ?? 0) }} <span class="text-lg font-bold text-white/80">điểm</span></h4>
                        <p class="text-xs text-white/70 mt-2 font-medium">💡 <strong>Quy tắc:</strong> 10.000đ thanh toán đơn hàng = <strong>+1 điểm</strong></p>
                    </div>
                    <div class="text-right">
                        @php
                            $pts = $user->point ?? 0;
                            if ($pts >= 150) { $rank = '🥇 Thẻ Vàng'; $badgeBg = 'bg-amber-400 text-espresso'; }
                            elseif ($pts >= 50) { $rank = '🥈 Thẻ Bạc'; $badgeBg = 'bg-slate-200 text-espresso'; }
                            else { $rank = '🥉 Thẻ Đồng'; $badgeBg = 'bg-amber-700/40 text-amber-200 border border-amber-500/30'; }
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $badgeBg }} shadow-sm">
                            {{ $rank }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 2. ĐỔI VOUCHER BẰNG ĐIỂM --}}
            <div>
                <h4 class="font-bold text-base text-espresso uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>🎁 Đổi Voucher Ưu Đãi</span>
                </h4>

                @if($availableVouchers->isEmpty())
                    <div class="bg-white p-6 rounded-2xl border border-dashed border-gray-300 text-center text-espresso/60 text-xs font-medium">
                        Hiện chưa có voucher nào mở đổi điểm. Vui lòng quay lại sau!
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($availableVouchers as $voucher)
                            @php
                                $ptsNeeded = $voucher->points_required;
                                $canRedeem = ($user->point ?? 0) >= $ptsNeeded;
                            @endphp
                            <div class="bg-white rounded-2xl p-4 border border-espresso/10 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-mono font-black text-espresso text-base block group-hover:text-coral transition-colors">{{ $voucher->code }}</span>
                                        <span class="text-xs font-bold text-coral block mt-0.5">
                                            @if($voucher->discount_type === 'percent')
                                                Giảm {{ (float)$voucher->discount_value }}%
                                            @else
                                                Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                            @endif
                                        </span>
                                    </div>
                                    <span class="bg-amber-100 text-amber-800 text-[11px] font-black px-2 py-1 rounded-lg">
                                        {{ $ptsNeeded }} điểm
                                    </span>
                                </div>
                                
                                <p class="text-[11px] text-espresso/60 mb-3 font-medium">
                                    Đơn tối thiểu: <strong>{{ number_format($voucher->min_order, 0, ',', '.') }}đ</strong>
                                </p>

                                <form action="{{ route('user.points.redeem') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="voucher_id" value="{{ $voucher->voucher_id }}">
                                    @if($canRedeem)
                                        <button type="submit" class="w-full py-2 bg-coral hover:bg-[#d5523b] text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-xs flex items-center justify-center gap-1">
                                            Đổi Ngay (-{{ $ptsNeeded }}p)
                                        </button>
                                    @else
                                        <button type="button" disabled class="w-full py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-bold uppercase tracking-wider cursor-not-allowed border border-gray-200">
                                            Thiếu {{ $ptsNeeded - ($user->point ?? 0) }} điểm
                                        </button>
                                    @endif
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 3. KHO VOUCHER CỦA TÔI --}}
            <div>
                <h4 class="font-bold text-base text-espresso uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>🎟️ Kho Voucher Của Tôi</span>
                </h4>

                @if($myVouchers->isEmpty())
                    <div class="bg-white p-6 rounded-2xl border border-dashed border-gray-300 text-center text-espresso/60 text-xs font-medium">
                        Kho voucher đang trống. Hãy tích điểm và đổi mã ưu đãi ngay nhé!
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($myVouchers as $mv)
                            <div class="bg-white rounded-2xl p-4 border border-espresso/10 shadow-xs flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 {{ $mv->assigned_user_id ? 'bg-purple-50 text-purple-600 border-purple-200' : 'bg-amber-50 text-amber-600 border-amber-200' }} rounded-xl flex items-center justify-center font-bold text-lg shrink-0 border">
                                        {{ $mv->assigned_user_id ? '🎁' : '🎫' }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono font-black text-espresso text-sm">{{ $mv->code }}</span>
                                            <span class="text-xs font-bold text-coral">
                                                ({{ $mv->discount_type === 'percent' ? 'Giảm '.$mv->discount_value.'%' : 'Giảm '.number_format($mv->discount_value, 0, ',', '.').'đ' }})
                                            </span>
                                            @if($mv->assigned_user_id)
                                                <span class="text-[10px] font-extrabold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-md border border-purple-200">🎁 Ưu đãi tặng riêng</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-espresso/50 mt-0.5">Ngày nhận: {{ \Carbon\Carbon::parse($mv->save_at)->format('H:i - d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div>
                                    @if($mv->is_used)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-xs font-bold border border-gray-200">Đã sử dụng</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200 shadow-2xs">Sẵn sàng dùng</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 4. LỊCH SỬ TÍCH ĐIỂM TỪ ĐƠN HÀNG --}}
            <div>
                <h4 class="font-bold text-base text-espresso uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>📊 Lịch Sử Tích Điểm Đơn Hàng</span>
                </h4>

                @if($completedOrders->isEmpty())
                    <div class="bg-white p-6 rounded-2xl border border-dashed border-gray-300 text-center text-espresso/60 text-xs font-medium">
                        Chưa có lịch sử tích điểm từ đơn hàng nào.
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-espresso/10 overflow-hidden shadow-xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="bg-espresso text-white uppercase text-[10px] font-black tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Mã Đơn</th>
                                        <th class="py-3 px-4">Ngày Hoàn Thành</th>
                                        <th class="py-3 px-4 text-right">Tổng Tiền</th>
                                        <th class="py-3 px-4 text-center">Điểm Tích Lũy</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($completedOrders as $co)
                                        @php $earned = (int)floor($co->total_amount / 10000); @endphp
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-3 px-4 font-black text-espresso">#{{ $co->order_id }}</td>
                                            <td class="py-3 px-4 text-espresso/70 font-medium">{{ \Carbon\Carbon::parse($co->updated_at ?? $co->created_at)->format('H:i - d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-right font-black text-coral">{{ number_format($co->total_amount, 0, ',', '.') }}đ</td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full text-xs">
                                                    +{{ $earned }} điểm
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        </div>
        
    </div>
</div>
@endsection
