@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - Chill Chill Coffee')

@section('content')
<div class="min-h-[calc(100vh-100px)] bg-[#FAF7F2] flex items-center justify-center p-4 sm:p-6">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 sm:p-10 shadow-2xl border border-espresso/5">
        <div class="text-center mb-8">
            <h1 class="font-serif font-bold text-3xl text-espresso mb-2">Đặt Lại Mật Khẩu</h1>
            <p class="text-xs sm:text-sm text-espresso/60">Nhập mật khẩu mới cho tài khoản <span class="font-bold text-coral">{{ $email }}</span></p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-2xl text-xs font-bold mb-6 space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Email tài khoản</label>
                <input type="email" value="{{ $email }}" disabled class="w-full px-4 py-3.5 bg-gray-100 border border-transparent rounded-xl text-espresso/60 font-medium text-sm cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Mật khẩu mới</label>
                <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3.5 bg-[#FAF7F2] border border-gray-200 rounded-xl focus:outline-none focus:border-coral font-medium text-espresso text-sm">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3.5 bg-[#FAF7F2] border border-gray-200 rounded-xl focus:outline-none focus:border-coral font-medium text-espresso text-sm">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-espresso hover:bg-coral text-white rounded-full font-bold text-sm shadow-lg transition-all">
                    Cập Nhật Mật Khẩu Mới
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
