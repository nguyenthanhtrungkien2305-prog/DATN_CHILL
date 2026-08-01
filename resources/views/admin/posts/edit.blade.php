@extends('admin.layouts.app')

@section('title', 'Sửa Bài Viết - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('posts.index') }}" class="text-gray-500 hover:text-[#e8634a] transition">← Quay lại</a>
            <h2 class="text-xl font-semibold text-gray-800">Sửa Bài Viết</h2>
        </div>
    </header>

    <div class="p-8 max-w-4xl mx-auto w-full">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('posts.update', $post->post_id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" placeholder="Ví dụ: Khám phá hương vị cà phê Robusta..." required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] font-medium text-lg">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục bài viết <span class="text-red-500">*</span></label>
                        <select name="categories_post_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->categories_post_id }}" {{ old('categories_post_id', $post->categories_post_id) == $cat->categories_post_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái bài viết</label>
                        <select name="status" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                            <option value="1" {{ old('status', $post->status) == 1 ? 'selected' : '' }}>🟢 Hiển thị công khai</option>
                            <option value="0" {{ old('status', $post->status) == 0 ? 'selected' : '' }}>⚪ Lưu nháp / Ẩn bài</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đường dẫn ảnh đại diện (Thumbnail URL)</label>
                    <input type="url" name="thumbnail" value="{{ old('thumbnail', $post->thumbnail) }}" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a]">
                    @if($post->thumbnail)
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ $post->thumbnail }}" class="w-20 h-14 object-cover rounded-lg border">
                            <span class="text-xs text-gray-400">Xem trước ảnh đại diện hiện tại</span>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung chi tiết bài viết <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="12" placeholder="Nhập nội dung bài viết chi tiết..." required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-[#e8634a] leading-relaxed">{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="pt-6 flex gap-4 justify-end">
                    <a href="{{ route('posts.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Hủy</a>
                    <button type="submit" class="px-6 py-3 bg-[#e8634a] text-white rounded-lg hover:bg-[#d5523b] font-medium shadow-sm">Cập nhật bài viết</button>
                </div>
            </form>
        </div>
    </div>
@endsection
