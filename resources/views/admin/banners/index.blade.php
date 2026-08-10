@extends('admin.layouts.app')

@section('title', 'Quản lý Banner Trang Chủ & Khuyến Mãi - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Banner Trang Chủ & Khuyến Mãi</h2>
    </header>

    <div class="p-8">
        {{-- Thông báo --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 flex items-center justify-between shadow-sm">
                <span>🎉 {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-green-700 font-bold">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 flex items-center justify-between shadow-sm">
                <span>⚠️ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-700 font-bold">&times;</button>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-gray-500 text-sm">Quản lý tập trung các Banner hiển thị trên **Trang Chủ** (Hero Section, Promo Voucher) và trang sản phẩm.</p>
                <p class="text-xs text-gray-400 mt-0.5">Dễ dàng sửa tiêu đề, mô tả, nút điều hướng và hình ảnh bất cứ khi nào có sản phẩm mới hoặc chương trình ưu đãi.</p>
            </div>
            <a href="{{ route('banners.create') }}" class="bg-[#e8634a] text-white px-6 py-2.5 rounded-xl hover:bg-[#d5523b] transition font-medium text-sm shadow-md flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm Banner mới
            </a>
        </div>

        {{-- Thanh lọc & Tìm kiếm --}}
        <div class="bg-white p-4 rounded-t-2xl border-t border-x border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="{{ route('banners.index') }}" method="GET" class="w-full md:w-auto flex flex-wrap gap-2 flex-1">
                <select name="position" onchange="this.form.submit()" class="px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-gray-50 font-medium">
                    <option value="">-- Tất cả vị trí Banner --</option>
                    <option value="home_hero" {{ request('position') == 'home_hero' ? 'selected' : '' }}>🚩 Hero Banner (Đầu Trang Chủ)</option>
                    <option value="home_promo" {{ request('position') == 'home_promo' ? 'selected' : '' }}>🎁 Promo Voucher (Cuối Trang Chủ)</option>
                    <option value="combo_banner" {{ request('position') == 'combo_banner' ? 'selected' : '' }}>🛍️ Combo Banner Khuyến Mãi</option>
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề, thẻ tag..." class="flex-1 min-w-[200px] px-4 py-2 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition text-sm font-medium">Tìm kiếm</button>
                @if(request('search') || request('position'))
                    <a href="{{ route('banners.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-300 transition text-sm flex items-center font-medium">Reset Filter</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b">
                        <th class="p-4 font-semibold w-16">ID</th>
                        <th class="p-4 font-semibold">Vị Trí Banner</th>
                        <th class="p-4 font-semibold">Thẻ Tag (Badge)</th>
                        <th class="p-4 font-semibold">Tiêu đề Banner</th>
                        <th class="p-4 font-semibold">Mô tả</th>
                        <th class="p-4 font-semibold">Nút Điều Hướng</th>
                        <th class="p-4 font-semibold text-center">Trạng thái</th>
                        <th class="p-4 font-semibold text-right w-44">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4 font-bold text-gray-700">#{{ $banner->banner_id }}</td>
                            <td class="p-4">
                                @if($banner->position == 'home_hero')
                                    <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 border border-purple-200 px-2.5 py-1 rounded-lg text-xs font-bold">
                                        🚩 Hero Trang Chủ
                                    </span>
                                @elseif($banner->position == 'home_promo')
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-bold">
                                        🎁 Promo Trang Chủ
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-bold">
                                        🛍️ Combo Banner
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-800 border border-gray-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                    {{ $banner->badge ?? 'Không có' }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-gray-800 max-w-xs">
                                {{ $banner->title }}
                            </td>
                            <td class="p-4 text-xs text-gray-600 max-w-xs">
                                <p class="line-clamp-2">{{ $banner->description ?? 'Chưa có mô tả' }}</p>
                            </td>
                            <td class="p-4 space-y-1">
                                <div class="text-xs">
                                    <span class="font-bold text-gray-800">{{ $banner->button_text ?: 'Nút 1' }}:</span>
                                    <a href="{{ $banner->button_link }}" target="_blank" class="text-xs text-[#e8634a] hover:underline font-mono ml-1">
                                        {{ $banner->button_link ?: '#' }}
                                    </a>
                                </div>
                                @if($banner->button_secondary_text)
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-600">{{ $banner->button_secondary_text }}:</span>
                                        <span class="text-xs text-gray-500 font-mono ml-1">{{ $banner->button_secondary_link ?: '#' }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('banners.toggle_status', $banner->banner_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-full text-xs font-bold transition flex items-center gap-1.5 mx-auto {{ $banner->status ? 'bg-green-100 text-green-700 border border-green-300 hover:bg-green-200' : 'bg-gray-100 text-gray-500 border border-gray-300 hover:bg-gray-200' }}">
                                        <span class="w-2 h-2 rounded-full {{ $banner->status ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        {{ $banner->status ? 'Đang Hiển Thị' : 'Đã Ẩn' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('banners.edit', $banner->banner_id) }}" 
                                       class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition font-medium text-xs">
                                        Sửa
                                    </a>
                                    <form action="{{ route('banners.destroy', $banner->banner_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Banner này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition font-medium text-xs">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
                                Chưa có banner nào. Hãy bấm <strong>"Thêm Banner mới"</strong> để tạo banner cho trang chủ.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="mt-6">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
@endsection
