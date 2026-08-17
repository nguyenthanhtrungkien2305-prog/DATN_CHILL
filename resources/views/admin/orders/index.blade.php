@extends('admin.layouts.app')

@section('title', 'Quản lý Đơn Hàng - Chill Chill Admin')

@section('content')
    <header class="hidden lg:flex h-16 bg-white shadow-sm items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            Quản lý Đơn hàng & Tiến độ
        </h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
        </div>
    </header>

    <div class="p-4 md:p-8">
        {{-- THÔNG BÁO THÀNH CÔNG VÀ LỖI CHẶN LUỒNG --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl font-bold relative mb-4 shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl font-bold relative mb-4 shadow-sm flex items-center gap-2">
                <span>⚠️</span> {{ session('error') }}
            </div>
        @endif

        {{-- WIDGET THỐNG KÊ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-red-500">
                <div>
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Chờ xác nhận</h3>
                    <p class="text-3xl font-black text-red-500">{{ $countPending }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-xl">⏳</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-blue-500">
                <div>
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Đang pha chế / Xử lý</h3>
                    <p class="text-3xl font-black text-blue-500">{{ $countProcessing }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-xl">👨‍🍳</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-emerald-500">
                <div>
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Hoàn thành (Hôm nay)</h3>
                    <p class="text-3xl font-black text-emerald-500">{{ $countCompletedToday }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-xl">🎉</div>
            </div>
        </div>

        {{-- BỘ LỌC VÀ TÌM KIẾM --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between gap-4">
            <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 md:pb-0">
                <a href="{{ route('admin.orders.index', ['status' => 'incomplete']) }}" class="px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors {{ $statusFilter == 'incomplete' ? 'bg-[#e8634a] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                     Đơn chưa xong 
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors {{ $statusFilter == 'all' ? 'bg-gray-800 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tất cả
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors {{ $statusFilter == 'completed' ? 'bg-emerald-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Đã hoàn thành
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors {{ $statusFilter == 'cancelled' ? 'bg-gray-400 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Đã hủy
                </a>
            </div>

            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm ID hoặc Tên khách..." class="w-full md:w-64 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 focus:bg-white transition-all">
                <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-xl hover:bg-gray-700 transition font-bold text-sm">Tìm</button>
            </form>
        </div>

        {{-- BẢNG DANH SÁCH ĐƠN HÀNG --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                            <th class="p-4 pl-6 w-24">ID</th>
                            <th class="p-4">Khách hàng / Thời gian</th>
                            <th class="p-4 w-48 text-right">Tổng thanh toán</th>
                            <th class="p-4 w-64 text-center">Tiến độ đơn hàng</th>
                            <th class="p-4 pr-6 text-center w-32">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="p-4 pl-6 font-black text-gray-900 text-base">#{{ $order->order_id }}</td>
                            <td class="p-4">
                                <div class="font-bold text-espresso text-base">{{ $order->customer_name ?? 'Khách Vãng Lai' }}</div>
                                <div class="text-xs font-medium text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }} 
                                    @if($statusFilter == 'incomplete')
                                        <span class="text-coral ml-2 bg-coral/10 px-1.5 py-0.5 rounded text-[10px]">Chờ {{ \Carbon\Carbon::parse($order->created_at)->diffInMinutes(now()) }} phút</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-right font-black text-[#e8634a] text-base">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </td>
                            <td class="p-4 text-center">
                                {{-- BỘ THAY ĐỔI TRẠNG THÁI --}}
                                <form action="{{ route('admin.orders.update_status', $order->order_id) }}" method="POST" class="m-0">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                        class="w-full text-xs font-bold rounded-xl px-3 py-2 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-coral/50 transition-all text-center cursor-pointer shadow-2xs
                                        {{ $order->status == 'pending' ? 'bg-red-50 text-red-600 border-red-200' : '' }}
                                        {{ $order->status == 'processing' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}
                                        {{ $order->status == 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                        {{ $order->status == 'cancelled' ? 'bg-gray-100 text-gray-500 border-gray-200' : '' }}">
                                        
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                            🟡 Chờ xác nhận
                                        </option>
                                        
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                            🔵 Đang pha chế
                                        </option>
                                        
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                            🟢 Đã hoàn thành
                                        </option>
                                        
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                            🔴 Đã hủy
                                        </option>

                                    </select>
                                </form>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <button onclick="alert('Chức năng Xem chi tiết Popup đang phát triển!')" class="text-gray-400 hover:text-[#e8634a] transition-colors p-2 bg-gray-50 rounded-lg border border-gray-200 hover:bg-[#e8634a]/10 hover:border-[#e8634a]/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-400 italic text-sm">
                                🍵 Tuyệt vời! Hiện không có đơn hàng nào cần xử lý.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection