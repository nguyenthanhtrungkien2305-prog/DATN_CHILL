@extends('admin.layouts.app')

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