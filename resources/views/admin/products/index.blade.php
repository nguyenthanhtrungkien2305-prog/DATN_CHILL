@extends('admin.layouts.app')

@section('title', 'Quản lý Sản Phẩm - Chill Chill Admin')

@section('content')
    <header class="hidden lg:flex h-16 bg-white shadow-sm items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách Sản phẩm</h2>
    </header>

    <div class="p-4 md:p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <form action="{{ route('products.index') }}" method="GET" class="w-full sm:w-1/2 md:w-1/3 flex gap-2">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc ID..." class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-xl focus:outline-none focus:border-[#e8634a]">
                    @if(request('search'))
                        <a href="{{ route('products.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 font-bold" title="Xóa tìm kiếm">✕</a>
                    @endif
                </div>
                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-xl text-xs sm:text-sm hover:bg-gray-700 transition font-bold shrink-0">Tìm</button>
            </form>
            
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <button type="submit" 
                        form="bulkDeleteForm" 
                        id="bulkDeleteBtn" 
                        onclick="return confirmBulkDelete(event)" 
                        disabled 
                        class="bg-red-500 text-white px-3 py-2 rounded-xl opacity-50 cursor-not-allowed transition font-bold text-xs flex items-center gap-1 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa mục đã chọn (0)</span>
                </button>

                <a href="{{ route('products.create') }}" class="bg-[#e8634a] text-white px-4 py-2 rounded-xl hover:bg-[#d5523b] transition font-bold text-xs whitespace-nowrap">
                    + Thêm sản phẩm mới
                </a>
            </div>
        </div>

        <form id="bulkDeleteForm" action="{{ route('products.bulk_delete') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 accent-[#e8634a] rounded cursor-pointer" title="Chọn tất cả">
                            </th>
                            <th class="p-4 font-medium">ID</th>
                            <th class="p-4 font-medium">Hình ảnh</th>
                            <th class="p-4 font-medium">Tên sản phẩm</th>
                            <th class="p-4 font-medium">Danh mục</th>
                            <th class="p-4 font-medium">Giá bán (Từ)</th>
                            <th class="p-4 font-medium">Trạng thái</th>
                            <th class="p-4 font-medium text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $product->product_id }}" class="row-checkbox w-4 h-4 accent-[#e8634a] rounded cursor-pointer">
                            </td>
                            <td class="p-4 font-bold text-gray-900">{{ $product->product_id }}</td>
                            <td class="p-4">
                                <img src="{{ format_image_url($product->image_url, '/images/logo1.jpg', $product->name) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover border" onerror="this.onerror=null; this.src='/images/logo1.jpg';">
                            </td>
                            <td class="p-4 font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="p-4">{{ $product->category_name ?? 'Không có' }}</td>
                            
                            <td class="p-4 font-bold text-[#e8634a]">
                                {{ number_format($product->price ?? 0, 0, ',', '.') }}đ
                            </td>

                            <td class="p-4">
                                @if($product->status == 1)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Đang bán</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Ngừng bán</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('products.edit', $product->product_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">Chưa có sản phẩm nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                
                <div class="p-4 border-t">
                    {{ $products->links() }}
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
                return confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} sản phẩm đã chọn?`);
            };
        });
    </script>
@endsection