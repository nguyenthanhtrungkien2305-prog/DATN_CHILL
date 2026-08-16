@extends('layouts.app')

@section('title', 'Đổi Mật Khẩu - Chill Chill')

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
                <a href="{{ route('user.wallet') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl hover:bg-white/5 text-cream/70 hover:text-white text-xs md:text-sm transition-colors flex items-center gap-2 shrink-0">
                    <span>Tiền hoàn</span>
                    <span class="bg-coral text-white text-[10px] md:text-xs font-bold px-2 py-0.5 rounded-full">{{ number_format(auth()->user()->wallet_balance ?? 0, 0, ',', '.') }}đ</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="whitespace-nowrap px-4 py-2.5 md:py-3 rounded-xl bg-white/10 text-white font-medium text-xs md:text-sm transition-colors shrink-0">Đổi mật khẩu</a>
            </nav>
            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Form Đổi mật khẩu --}}
        <div class="w-full md:w-2/3 p-5 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <h3 class="font-serif font-bold text-3xl text-espresso mb-2">Đổi mật khẩu</h3>
                <p class="text-espresso/60 mb-8 text-sm">Cập nhật mật khẩu mới để bảo vệ tài khoản của bạn</p>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl text-sm mb-6 flex items-center gap-2">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('user.update_password') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required placeholder="Nhập mật khẩu cũ của bạn" class="w-full px-4 py-3 bg-[#FAF7F2] border border-gray-200 rounded-2xl focus:outline-none focus:border-coral text-espresso">
                        @error('current_password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2">Mật khẩu mới</label>
                        <input type="password" name="new_password" required placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 bg-[#FAF7F2] border border-gray-200 rounded-2xl focus:outline-none focus:border-coral text-espresso">
                        @error('new_password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-espresso mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3 bg-[#FAF7F2] border border-gray-200 rounded-2xl focus:outline-none focus:border-coral text-espresso">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-espresso text-white rounded-full font-bold hover:bg-coral transition-colors shadow-lg shadow-espresso/20">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
@endsection
