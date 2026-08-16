@extends('admin.layouts.app')

@section('title', 'Quản lý Bài Viết - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Bài Viết & Blog</h2>
        <div class="flex items-center gap-3">
            <button type="submit" 
                    form="bulkDeletePostForm" 
                    id="bulkDeleteBtn" 
                    onclick="return confirmBulkDelete(event)" 
                    disabled 
                    class="bg-red-500 text-white px-5 py-2.5 rounded-lg opacity-50 cursor-not-allowed transition font-medium text-sm flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Xóa mục đã chọn (0)</span>
            </button>

            <a href="{{ route('posts.create') }}" class="bg-[#e8634a] hover:bg-[#d5523b] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                <span>+</span> Viết Bài Mới
            </a>
        </div>
    </header>

    <div class="p-8 max-w-7xl mx-auto w-full">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 shadow-xs">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-xs">
                {{ session('error') }}
            </div>
        @endif

        {{-- Thanh tìm kiếm & Lọc danh mục --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form action="{{ route('posts.index') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-4 items-center justify-between">
                <div class="flex flex-wrap md:flex-nowrap gap-4 w-full md:w-auto flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tiêu đề..." class="w-full md:w-80 px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] text-sm">
                    
                    <select name="category_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] text-sm">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->categories_post_id }}" {{ request('category_id') == $cat->categories_post_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-900 transition">Tìm kiếm</button>
                    
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('posts.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 flex items-center">Đặt lại</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Bảng danh sách bài viết --}}
        <form id="bulkDeletePostForm" action="{{ route('posts.bulk_delete') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 accent-[#e8634a] rounded cursor-pointer" title="Chọn tất cả">
                            </th>
                            <th class="p-4 font-medium w-16">ID</th>
                            <th class="p-4 font-medium w-24">Hình ảnh</th>
                            <th class="p-4 font-medium">Tiêu đề bài viết</th>
                            <th class="p-4 font-medium">Danh mục</th>
                            <th class="p-4 font-medium">Tác giả</th>
                            <th class="p-4 font-medium text-center">Trạng thái</th>
                            <th class="p-4 font-medium">Ngày tạo</th>
                            <th class="p-4 font-medium text-center w-36">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                        @forelse($posts as $p)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $p->post_id }}" class="row-checkbox w-4 h-4 accent-[#e8634a] rounded cursor-pointer">
                            </td>
                            <td class="p-4 text-gray-500">{{ $p->post_id }}</td>
                            <td class="p-4">
                                <img src="{{ format_image_url($p->thumbnail, '/images/logo1.png') }}" class="w-16 h-12 rounded-lg object-cover border border-gray-200 shadow-2xs" onerror="this.onerror=null; this.src='/images/logo1.png';">
                            </td>
                            <td class="p-4">
                                <a href="{{ route('post.show', $p->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-[#e8634a] transition line-clamp-2">
                                    {{ $p->title }}
                                </a>
                                <span class="text-xs text-gray-400 block mt-0.5">/tin-tuc/{{ $p->slug }}</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-orange-50 text-[#e8634a] font-bold px-2.5 py-1 rounded-md text-xs border border-orange-200">
                                    {{ $p->category_name ?? 'Chưa xếp' }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-gray-600">
                                {{ $p->author_name ?? 'Quản trị viên' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($p->status)
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full text-xs border border-emerald-200">Hiển thị</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 font-bold px-2.5 py-1 rounded-full text-xs border border-gray-200">Ẩn / Bản nháp</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('posts.edit', $p->post_id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Sửa">
                                        ✏️
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-500">
                                Chưa có bài viết nào trong hệ thống.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Phân trang --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $posts->links() }}
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
                return confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} bài viết đã chọn?`);
            };
        });
    </script>
@endsection
