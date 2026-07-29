@extends('admin.layouts.app')

<<<<<<< HEAD
@section('title')
    Chi tiết Phản Hồi #{{ $feedback->id }} - Chill Chill Admin
@endsection
@section('page_title')
    Chi tiết phản hồi #{{ $feedback->id }}
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('feedbacks.index') }}" class="text-gray-500 hover:text-gray-700 font-medium transition">← Quay lại danh sách</a>
</div>

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
@endsection

=======
@section('title', 'Quản lý Phản Hồi - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Phản Hồi Khách Hàng</h2>
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

        {{-- Thẻ Thống kê Nhỏ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-gray-500 text-sm font-medium mb-1">Chưa xử lý</h3>
                <p class="text-3xl font-bold text-red-500">{{ $countUnread }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-gray-500 text-sm font-medium mb-1">Tổng phản hồi</h3>
                <p class="text-3xl font-bold text-[#2B2623]">{{ $countTotal }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-gray-500 text-sm font-medium mb-1">Đã trả lời</h3>
                <p class="text-3xl font-bold text-green-500">{{ \App\Models\Feedback::where('status', 'replied')->count() }}</p>
            </div>
        </div>

        {{-- Thanh bộ lọc & Tìm kiếm --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Các nút lọc trạng thái --}}
                <div class="flex gap-2">
                    <a href="{{ route('feedbacks.index') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Tất cả
                    </a>
                    <a href="{{ route('feedbacks.index', ['status' => 'unread']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request('status') == 'unread' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                        Chưa xem ({{ $countUnread }})
                    </a>
                    <a href="{{ route('feedbacks.index', ['status' => 'read']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request('status') == 'read' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                        Đã xem
                    </a>
                    <a href="{{ route('feedbacks.index', ['status' => 'replied']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request('status') == 'replied' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        Đã trả lời
                    </a>
                </div>

                {{-- Form tìm kiếm --}}
                <form action="{{ route('feedbacks.index') }}" method="GET" class="flex gap-2 w-full md:max-w-md">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, email, nội dung..." class="flex-1 px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 focus:bg-white transition-all">
                    <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-xl hover:bg-gray-700 transition text-sm font-medium">Tìm</button>
                    @if(request('search'))
                        <a href="{{ route('feedbacks.index', request('status') ? ['status' => request('status')] : []) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-300 transition text-sm flex items-center">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Bảng hiển thị --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                        <th class="p-4 font-medium w-16">ID</th>
                        <th class="p-4 font-medium">Khách hàng</th>
                        <th class="p-4 font-medium">Liên hệ</th>
                        <th class="p-4 font-medium">Nội dung tóm tắt</th>
                        <th class="p-4 font-medium">Trạng thái</th>
                        <th class="p-4 font-medium">Thời gian nhận</th>
                        <th class="p-4 font-medium text-center w-40">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($feedbacks as $fb)
                    <tr class="border-b hover:bg-gray-50/50 transition">
                        <td class="p-4 text-gray-500">{{ $fb->id }}</td>
                        <td class="p-4 font-semibold text-espresso">{{ $fb->name }}</td>
                        <td class="p-4 text-xs">
                            <div>Email: <span class="font-medium">{{ $fb->email }}</span></div>
                            <div class="mt-1">SĐT: <span class="font-medium text-gray-500">{{ $fb->phone ?? 'N/A' }}</span></div>
                        </td>
                        <td class="p-4 text-gray-600 max-w-xs truncate">{{ $fb->message }}</td>
                        <td class="p-4">
                            @if($fb->status === 'unread')
                                <span class="px-2.5 py-1 rounded-full text-xs bg-red-100 text-red-600 font-bold">Chưa xem</span>
                            @elseif($fb->status === 'read')
                                <span class="px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-600 font-bold">Đã xem</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-600 font-bold">Đã trả lời</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-gray-500">{{ $fb->created_at->format('H:i d/m/Y') }}</td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center items-center gap-3">
                                <a href="{{ route('feedbacks.show', $fb->id) }}" class="text-blue-500 hover:text-blue-700 font-bold transition">Xem</a>
                                <form action="{{ route('feedbacks.destroy', $fb->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400 italic">Không tìm thấy phản hồi nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Phân trang --}}
            @if($feedbacks->hasPages())
            <div class="p-4 border-t">
                {{ $feedbacks->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection
>>>>>>> main
