@extends('layouts.app')

@section('title', 'Quên Mật Khẩu - Chill Chill Coffee')

@section('content')
<div class="min-h-[calc(100vh-100px)] bg-[#FAF7F2] flex items-center justify-center p-4 sm:p-6">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 sm:p-10 shadow-2xl border border-espresso/5">
        <div class="text-center mb-8">
            <h1 class="font-serif font-bold text-3xl text-espresso mb-2">Quên Mật Khẩu?</h1>
            <p class="text-xs sm:text-sm text-espresso/60">Nhập địa chỉ Email liên kết với tài khoản của bạn để nhận liên kết khôi phục.</p>
        </div>

        @if(session('status') || session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold mb-6 break-words leading-relaxed">
                {!! session('status') ?? session('success') !!}
            </div>
        @endif

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

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs uppercase tracking-wider font-bold text-espresso mb-2">Địa chỉ Email đăng ký</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ví dụ: email@gmail.com" class="w-full px-4 py-3.5 bg-[#FAF7F2] border border-gray-200 rounded-xl focus:outline-none focus:border-coral font-medium text-espresso text-sm">
            </div>

            <button type="submit" class="w-full py-4 bg-coral hover:bg-[#d5523b] text-white rounded-full font-bold text-sm shadow-lg shadow-coral/20 transition-all">
                Gửi Liên Kết Khôi Phục
            </button>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-bold text-espresso/60 hover:text-coral transition-colors inline-flex items-center gap-1">
                    ← Quay lại Đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
