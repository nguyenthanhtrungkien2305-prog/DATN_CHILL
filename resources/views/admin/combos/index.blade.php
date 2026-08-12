@extends('admin.layouts.app')

@section('title', 'Quản lý Combo Sản phẩm - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách Gói Combo Sản Phẩm</h2>
    </header>

    <div class="p-8">
        {{-- Thông báo --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-500">Quản lý các gói Combo tiết kiệm, kết hợp nhiều sản phẩm với giá ưu đãi.</p>
            <a href="{{ route('combos.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                + Thêm gói Combo mới
            </a>
        </div>

        {{-- Thanh tìm kiếm --}}
        <div class="bg-white p-4 rounded-t-2xl border-t border-x border-gray-100 flex items-center justify-between">
            <form action="{{ route('combos.index') }}" method="GET" class="w-full max-w-lg flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên combo..." class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-[#e8634a] text-sm">
                <select name="status" class="px-4 py-2 border rounded-lg text-sm bg-white">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">Lọc</button>
            </form>
        </div>

        {{-- Bảng dữ liệu Combo --}}
        <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 text-gray-600 text-sm">
                            <th class="p-4 font-semibold">Hình ảnh</th>
                            <th class="p-4 font-semibold">Tên Combo</th>
                            <th class="p-4 font-semibold">Sản phẩm thuộc Combo</th>
                            <th class="p-4 font-semibold">Giá gốc</th>
                            <th class="p-4 font-semibold">Giá Combo</th>
                            <th class="p-4 font-semibold text-center">Trạng thái</th>
                            <th class="p-4 font-semibold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($combos as $combo)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-4">
                                    <img src="{{ asset($combo->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=300&auto=format&fit=crop') }}" 
                                         alt="{{ $combo->name }}" 
                                         class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow-sm">
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-800 text-base">{{ $combo->name }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $combo->description }}</div>
                                </td>
                                <td class="p-4">
                                    <ul class="space-y-1">
                                        @foreach($combo->products as $prod)
                                            <li class="inline-flex items-center gap-1.5 bg-orange-50 border border-orange-200 text-orange-800 px-2.5 py-1 rounded-md text-xs font-medium mr-1 mb-1">
                                                <span>{{ $prod->name }}</span>
                                                <span class="bg-orange-200 text-orange-900 px-1.5 py-0.5 rounded font-bold">x{{ $prod->pivot->quantity }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="p-4 font-medium text-gray-400 line-through">
                                    {{ number_format($combo->original_price, 0, ',', '.') }}đ
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-[#e8634a] text-base">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                                    @if($combo->original_price > $combo->price)
                                        <span class="block text-[11px] text-green-600 font-semibold">
                                            Tiết kiệm {{ number_format($combo->original_price - $combo->price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($combo->status)
                                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Đang hiển thị</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">Đã ẩn</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('combos.edit', $combo->combo_id) }}" 
                                           class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition font-medium text-xs">
                                            Sửa
                                        </a>
                                        <form action="{{ route('combos.destroy', $combo->combo_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Combo này?')">
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
                                <td colspan="7" class="p-8 text-center text-gray-400">Chưa có gói Combo sản phẩm nào. Hãy bấm <strong>+ Thêm gói Combo mới</strong> để tạo gói ưu đãi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($combos->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $combos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
