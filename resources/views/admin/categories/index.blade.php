@extends('admin.layouts.app')

@section('title', 'Quản lý Danh mục - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách Danh mục</h2>
    </header>

    <div class="p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 text-sm font-medium">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-500 text-sm">Quản lý trạng thái hiển thị của các danh mục đồ uống & thức ăn.</p>
            
            <div class="flex items-center gap-3">
                <button type="submit" 
                        form="bulkDeleteCategoryForm" 
                        id="bulkDeleteBtn" 
                        onclick="return confirmBulkDelete(event)" 
                        disabled 
                        class="bg-red-500 text-white px-5 py-2.5 rounded-xl opacity-50 cursor-not-allowed transition font-bold text-sm shadow-md flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa mục đã chọn (0)</span>
                </button>

                <a href="{{ route('categories.create') }}" class="bg-[#e8634a] text-white px-6 py-2.5 rounded-xl hover:bg-[#d5523b] transition font-bold text-sm shadow-md">
                    + Thêm danh mục mới
                </a>
            </div>
        </div>

        <form id="bulkDeleteCategoryForm" action="{{ route('categories.bulk_delete') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b">
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 accent-[#e8634a] rounded cursor-pointer" title="Chọn tất cả">
                            </th>
                            <th class="p-4 font-bold w-20">ID</th>
                            <th class="p-4 font-bold w-28">Hình ảnh</th>
                            <th class="p-4 font-bold">Tên danh mục</th>
                            <th class="p-4 font-bold text-center w-36">Trạng thái</th>
                            <th class="p-4 font-bold text-center w-48">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                        @forelse($categories as $cat)
                        @php
                            $st = $cat->status ?? 1;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $cat->category_id }}" class="row-checkbox w-4 h-4 accent-[#e8634a] rounded cursor-pointer">
                            </td>
                            <td class="p-4 font-bold text-gray-900">{{ $cat->category_id }}</td>
                            <td class="p-4">
                                <img src="{{ format_image_url($cat->image, '/images/logo1.png') }}" alt="{{ $cat->name }}" class="w-16 h-12 rounded-xl object-cover border" onerror="this.onerror=null; this.src='/images/logo1.png';">
                            </td>
                            <td class="p-4 font-bold text-gray-900 text-base">{{ $cat->name }}</td>
                            
                            <td class="p-4 text-center">
                                @if($st == 1)
                                    <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full text-xs inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hiển thị
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 font-bold px-3 py-1 rounded-full text-xs inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Đã tạm ẩn
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('categories.edit', $cat->category_id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Sửa</a>
                                    
                                    <form action="{{ route('categories.destroy', $cat->category_id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc muốn {{ $st == 1 ? 'tạm ẩn' : 'kích hoạt lại' }} danh mục này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-xs px-3 py-1.5 rounded-lg transition {{ $st == 1 ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' }}">
                                            {{ $st == 1 ? '👁️ Tạm ẩn' : '🔓 Kích hoạt' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">Chưa có danh mục nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="p-4 border-t">
                    {{ $categories->links() }}
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
                return confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} danh mục đã chọn?`);
            };
        });
    </script>
@endsection