@extends('layouts.app')

@section('title', 'Đổi Mật Khẩu - Chill Chill')

@section('content')
<style>
    footer, #footer, .footer { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 20px; }
</style>

<div class="bg-[#FAF7F2] min-h-[calc(100vh-100px)] w-full flex items-center justify-center py-4 sm:py-10 px-2 sm:px-4">
    <div class="w-full max-w-5xl bg-white rounded-[30px] md:rounded-[40px] shadow-2xl border border-espresso/5 overflow-hidden flex flex-col md:flex-row h-auto md:h-[80vh] md:min-h-[550px] md:max-h-[800px]">
        
        {{-- Cột Trái: Menu Điều hướng --}}
        <div class="w-full md:w-1/3 bg-espresso text-cream p-5 md:p-10 flex flex-col h-auto md:h-full shrink-0">
            <div class="flex items-center justify-between md:block mb-3 md:mb-8">
                <h2 class="font-serif font-bold text-xl md:text-2xl text-white">Tài khoản</h2>
                <a href="{{ route('logout') }}" class="md:hidden px-3 py-1 bg-coral/20 text-coral hover:bg-coral hover:text-white rounded-lg text-xs font-bold transition-all">
                    Đăng xuất
                </a>
            </div>

            <nav class="flex md:flex-col overflow-x-auto gap-2 py-1 md:py-0 custom-scrollbar flex-1 shrink-0">
                <a href="{{ route('user.profile') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors text-cream/70 hover:text-white hover:bg-white/5">
                    Thông tin cá nhân
                </a>
                <a href="{{ route('user.orders') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors text-cream/70 hover:text-white hover:bg-white/5">
                    Đơn hàng của tôi
                </a>
                <a href="{{ route('user.points') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors flex items-center justify-between gap-2 shrink-0 text-cream/70 hover:text-white hover:bg-white/5">
                    <span>Tích điểm & Ưu đãi</span>
                    <span class="bg-coral/20 text-coral text-[10px] md:text-xs font-black px-2 py-0.5 rounded-full">{{ auth()->user()->point ?? 0 }}p</span>
                </a>
                <a href="{{ route('user.change_password') }}" class="px-4 py-2.5 rounded-xl font-medium text-xs md:text-sm whitespace-nowrap transition-colors bg-white/20 text-white font-bold">
                    Đổi mật khẩu
                </a>
            </nav>

            <a href="{{ route('logout') }}" class="hidden md:flex mt-auto px-4 py-3 text-coral hover:text-white transition-colors items-center gap-2 text-sm font-bold">
                Đăng xuất
            </a>
        </div>

        {{-- Cột Phải: Form Đổi mật khẩu --}}
        <div class="w-full md:w-2/3 p-4 sm:p-8 md:p-12 h-auto md:h-full overflow-y-auto custom-scrollbar">
            <h3 class="font-serif font-bold text-2xl md:text-3xl text-espresso mb-1">Đổi mật khẩu</h3>
            <p class="text-espresso/60 text-xs sm:text-sm mb-8">Bảo mật tài khoản của bạn bằng mật khẩu an toàn</p>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-bold mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-2xl text-sm font-bold mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-2xl text-xs font-bold mb-6 space-y-1">
                    @foreach ($errors->all() as $err)
                        <p>• {{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('user.update_password') }}" method="POST" class="space-y-6 max-w-md">
                @csrf

                <div>
                    <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Mật khẩu mới</label>
                    <input type="password" name="new_password" required placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Xác nhận mật khẩu mới</label>
                    <input type="password" name="new_password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3 bg-[#FAF7F2] border border-transparent rounded-xl focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso text-sm font-medium">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-espresso text-white rounded-full font-bold hover:bg-coral transition-colors shadow-lg text-sm">
                        Cập Nhật Mật Khẩu
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
