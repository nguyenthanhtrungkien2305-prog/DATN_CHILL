<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Phản Hồi #{{ $feedback->id }} - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('feedbacks.index') }}" class="text-gray-500 hover:text-gray-700 font-medium transition">← Quay lại danh sách</a>
                <span class="text-gray-300">|</span>
                <h2 class="text-xl font-semibold text-gray-800">Chi tiết phản hồi #{{ $feedback->id }}</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
                <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
            </div>
        </header>

        <div class="p-8">
            {{-- Toast/Thông báo --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative mb-6 flex items-center gap-3 shadow-sm">
                    <span>✨</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Cột trái: Thông tin chi tiết phản hồi --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Khung nội dung phản hồi --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6 border-b pb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <span>✉️</span> Nội dung thư phản hồi
                            </h3>
                            <div>
                                @if($feedback->status === 'unread')
                                    <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600 font-bold">Chưa xem</span>
                                @elseif($feedback->status === 'read')
                                    <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-600 font-bold">Đã xem</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600 font-bold">Đã trả lời</span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 p-5 rounded-2xl">
                                <h4 class="text-xs uppercase text-gray-400 font-bold tracking-wide mb-2">Lời nhắn từ khách hàng:</h4>
                                <p class="text-espresso text-base whitespace-pre-line leading-relaxed italic">
                                    "{!! nl2br(e($feedback->message)) !!}"
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Khung câu trả lời (Form soạn thảo hoặc hiển thị câu trả lời đã gửi) --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        @if($feedback->status === 'replied')
                            <div class="flex items-center justify-between mb-6 border-b pb-4">
                                <h3 class="text-lg font-bold text-green-600 flex items-center gap-2">
                                    <span>✅</span> Đã gửi trả lời
                                </h3>
                                <span class="text-xs text-gray-500">
                                    Lúc: {{ $feedback->replied_at->format('H:i d/m/Y') }}
                                </span>
                            </div>
                            <div class="bg-green-50/50 p-5 rounded-2xl border border-green-100">
                                <h4 class="text-xs uppercase text-green-600 font-bold tracking-wide mb-2">
                                    Nội dung đã gửi cho khách (bởi: {{ $feedback->repliedBy->name ?? 'Admin' }}):
                                </h4>
                                <p class="text-espresso text-base whitespace-pre-line leading-relaxed">
                                    {!! nl2br(e($feedback->reply_content)) !!}
                                </p>
                            </div>
                        @else
                            <div class="mb-6 border-b pb-4">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <span>✍️</span> Soạn thư phản hồi
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Hệ thống sẽ tự động gửi email câu trả lời này trực tiếp đến email của khách hàng.</p>
                            </div>

                            <form action="{{ route('feedbacks.reply', $feedback->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <textarea name="reply_content" rows="8" placeholder="Nhập nội dung phản hồi gửi tới khách hàng..." class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#e8634a] text-base focus:bg-white transition-all bg-gray-50 @error('reply_content') border-red-500 @enderror" required>{{ old('reply_content') }}</textarea>
                                    @error('reply_content')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="submit" class="bg-[#e8634a] hover:bg-[#d5523b] text-white px-6 py-3 rounded-full font-bold shadow-lg shadow-[#e8634a]/20 transition-all duration-300 flex items-center gap-2">
                                        <span>🚀</span> Gửi email phản hồi
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Cột phải: Thông tin khách hàng --}}
                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-4 mb-6">Thông tin liên hệ</h3>
                        
                        <div class="space-y-4 text-sm">
                            <div>
                                <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Họ và tên</span>
                                <span class="font-bold text-espresso text-base">{{ $feedback->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Địa chỉ Email</span>
                                <a href="mailto:{{ $feedback->email }}" class="text-[#e8634a] hover:underline font-medium text-base block break-all">{{ $feedback->email }}</a>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Số điện thoại</span>
                                <span class="font-medium text-espresso text-base">{{ $feedback->phone ?? 'Không cung cấp' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-xs font-bold uppercase tracking-wider mb-1">Thời gian gửi</span>
                                <span class="font-medium text-gray-600">{{ $feedback->created_at->format('H:i, d\m\t\h\g m, Y') }}</span>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <form action="{{ route('feedbacks.destroy', $feedback->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-3 border border-red-200 text-red-500 rounded-xl font-bold hover:bg-red-50 transition text-sm text-center">
                                    🗑️ Xóa liên hệ này
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
