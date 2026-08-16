@extends('admin.layouts.app')

@section('title', 'Quản lý Mã Giảm Giá - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách Mã Giảm Giá</h2>
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
            <p class="text-gray-500">Quản lý các chương trình ưu đãi, mã giảm giá áp dụng cho khách mua hàng.</p>
            <div class="flex items-center gap-3">
                <button type="submit" 
                        form="bulkDeleteVoucherForm" 
                        id="bulkDeleteBtn" 
                        onclick="return confirmBulkDelete(event)" 
                        disabled 
                        class="bg-red-500 text-white px-5 py-2 rounded-lg opacity-50 cursor-not-allowed transition font-medium text-sm flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa mục đã chọn (0)</span>
                </button>

                <a href="{{ route('vouchers.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm mã giảm giá mới
                </a>
            </div>
        </div>

        {{-- Thanh tìm kiếm --}}
        <div class="bg-white p-4 rounded-t-2xl border-t border-x border-gray-100 flex items-center justify-between">
            <form action="{{ route('vouchers.index') }}" method="GET" class="w-full max-w-md flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã giảm giá..." class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-[#e8634a] text-sm">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">Tìm kiếm</button>
                @if(request('search'))
                    <a href="{{ route('vouchers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <form id="bulkDeleteVoucherForm" action="{{ route('vouchers.bulk_delete') }}" method="POST">
            @csrf
            <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 accent-[#e8634a] rounded cursor-pointer" title="Chọn tất cả">
                            </th>
                            <th class="p-4 font-medium w-16">ID</th>
                            <th class="p-4 font-medium">Mã ưu đãi</th>
                            <th class="p-4 font-medium">Loại giảm</th>
                            <th class="p-4 font-medium">Mức giảm</th>
                            <th class="p-4 font-medium">Đơn tối thiểu</th>
                            <th class="p-4 font-medium text-center">Phân loại</th>
                            <th class="p-4 font-medium">Giới hạn sử dụng</th>
                            <th class="p-4 font-medium">Đã dùng</th>
                            <th class="p-4 font-medium">Thời gian hiệu lực</th>
                            <th class="p-4 font-medium text-center w-36">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                        @forelse($vouchers as $v)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $v->voucher_id }}" class="row-checkbox w-4 h-4 accent-[#e8634a] rounded cursor-pointer">
                            </td>
                            <td class="p-4 text-gray-500">{{ $v->voucher_id }}</td>
                            <td class="p-4 font-bold text-espresso text-base"><span class="bg-orange-50 border border-orange-200 text-[#e8634a] px-2.5 py-1 rounded-md">{{ $v->code }}</span></td>
                            <td class="p-4">
                                @if($v->discount_type === 'percent')
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-600 font-bold">Phần trăm (%)</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-600 font-bold">Số tiền cố định</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-gray-900">
                                @if($v->discount_type === 'percent')
                                    {{ number_format($v->discount_value, 0) }}%
                                @else
                                    {{ number_format($v->discount_value, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td class="p-4 font-medium">{{ number_format($v->min_order, 0, ',', '.') }}đ</td>
                            <td class="p-4 text-center">
                                @if($v->assigned_user_id || (isset($v->is_points_exchange) && !$v->is_points_exchange))
                                    <span class="bg-purple-50 text-purple-700 font-bold px-2.5 py-1 rounded-full text-xs border border-purple-200 block whitespace-nowrap">
                                        👤 Cá nhân: {{ $v->assigned_user_name ?? ('ID #'.$v->assigned_user_id) }}
                                    </span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 font-bold px-2.5 py-1 rounded-full text-xs border border-amber-200 block whitespace-nowrap">
                                        🎁 Đổi điểm (🏆 {{ $v->points_required ?? 10 }}p)
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-600 text-xs">
                                <div>Tổng: <strong>{{ $v->usage_limit ? $v->usage_limit . ' lượt' : 'Không giới hạn' }}</strong></div>
                                <div class="text-orange-600 font-semibold mt-0.5">Tối đa {{ $v->usage_per_user ?? 1 }} lần/khách</div>
                            </td>
                            <td class="p-4 font-medium text-[#e8634a]">{{ $v->used_count }}</td>
                            <td class="p-4 text-xs text-gray-500">
                                <div>Bắt đầu: {{ $v->start_date ? \Carbon\Carbon::parse($v->start_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                                <div class="mt-1">Kết thúc: {{ $v->end_date ? \Carbon\Carbon::parse($v->end_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('vouchers.edit', $v->voucher_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-8 text-gray-500">Chưa có mã giảm giá nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="p-4 border-t">
                    {{ $vouchers->links() }}
                </div>
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
                return confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} mã giảm giá đã chọn?`);
            };
        });
    </script>
@endsection