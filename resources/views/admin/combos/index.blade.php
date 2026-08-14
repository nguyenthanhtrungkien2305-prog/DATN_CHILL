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
            <div class="flex items-center gap-3">
                <button type="submit" 
                        form="bulkDeleteComboForm" 
                        id="bulkDeleteBtn" 
                        onclick="return confirmBulkDelete(event)" 
                        disabled 
                        class="bg-red-500 text-white px-5 py-2 rounded-lg opacity-50 cursor-not-allowed transition font-medium text-sm flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa mục đã chọn (0)</span>
                </button>

                <a href="{{ route('combos.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm gói Combo mới
                </a>
            </div>
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
        <form id="bulkDeleteComboForm" action="{{ route('combos.bulk_delete') }}" method="POST">
            @csrf
            <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 text-gray-600 text-sm">
                                <th class="p-4 w-10 text-center">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 accent-[#e8634a] rounded cursor-pointer" title="Chọn tất cả">
                                </th>
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
                                    <td class="p-4 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $combo->combo_id }}" class="row-checkbox w-4 h-4 accent-[#e8634a] rounded cursor-pointer">
                                    </td>
                                    <td class="p-4">
                                        <img src="{{ format_image_url($combo->image_url, '/images/logo1.png') }}" 
                                             alt="{{ $combo->name }}" 
                                             class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow-sm"
                                             onerror="this.onerror=null; this.src='/images/logo1.png';">
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-gray-400">Chưa có gói Combo sản phẩm nào. Hãy bấm <strong>+ Thêm gói Combo mới</strong> để tạo gói ưu đãi.</td>
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
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            function updateBulkState() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (bulkDeleteBtn) {
                    bulkDeleteBtn.disabled = checkedCount === 0;
                    if (checkedCount > 0) {
                        bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-red-500');
                        bulkDeleteBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'cursor-pointer');
                        bulkDeleteBtn.querySelector('span').textContent = `Xóa ${checkedCount} mục đã chọn`;
                    } else {
                        bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-red-500');
                        bulkDeleteBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'cursor-pointer');
                        bulkDeleteBtn.querySelector('span').textContent = `Xóa mục đã chọn (0)`;
                    }
                }
                if (selectAll) {
                    selectAll.checked = rowCheckboxes.length > 0 && checkedCount === rowCheckboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkState();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkState);
            });

            window.confirmBulkDelete = function (e) {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một mục để xóa!');
                    return false;
                }
                return confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} gói Combo đã chọn?`);
            };
        });
    </script>
@endsection
