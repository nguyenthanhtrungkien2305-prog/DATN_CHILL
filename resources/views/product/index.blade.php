@extends('layouts.app')

@section('title', 'Thực Đơn - Chill Chill')

@section('content')
<div class="bg-[#FAF7F2] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row gap-8">
        
        {{-- ========================================= --}}
        {{-- CỘT TRÁI: BỘ LỌC (SIDEBAR) --}}
        {{-- ========================================= --}}
        <aside class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-[24px] shadow-sm sticky top-24 border border-espresso/5">
                <div class="flex items-center justify-between mb-6 border-b border-espresso/10 pb-4">
                    <h2 class="font-serif font-bold text-2xl text-espresso">Bộ lọc</h2>
                    <a href="{{ route('product.index') }}" class="text-sm text-coral hover:underline">Xóa lọc</a>
                </div>

                <form action="{{ route('product.index') }}" method="GET" class="space-y-8">
                    {{-- Lọc Danh Mục --}}
                    <div>
                        <h3 class="font-bold text-espresso mb-4 uppercase text-sm tracking-wider">Danh mục</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category" value="" class="w-4 h-4 accent-coral" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-espresso/80 group-hover:text-coral transition">Tất cả món</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat->category_id }}" class="w-4 h-4 accent-coral" {{ request('category') == $cat->category_id ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="text-espresso/80 group-hover:text-coral transition">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lọc Giá Tiền --}}
                    <div>
                        <h3 class="font-bold text-espresso mb-4 uppercase text-sm tracking-wider">Khoảng giá</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Tối thiểu" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-coral">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Tối đa" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-coral">
                        </div>
                    </div>

                    {{-- Nút Áp dụng (Chỉ cần thiết cho phần nhập giá, vì radio tự động submit) --}}
                    <button type="submit" class="w-full py-3 bg-espresso text-white rounded-full font-bold hover:bg-coral transition-colors">
                        Áp dụng lọc giá
                    </button>
                    
                    {{-- Giữ lại trạng thái sắp xếp nếu có --}}
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                </form>
            </div>
        </aside>

        {{-- ========================================= --}}
        {{-- CỘT PHẢI: LƯỚI SẢN PHẨM --}}
        {{-- ========================================= --}}
        <main class="w-full md:w-3/4">
            
            {{-- Header của danh sách --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 bg-white p-4 rounded-[20px] shadow-sm border border-espresso/5">
                <p class="text-espresso/70 font-medium">Hiển thị <strong class="text-espresso">{{ $products->count() }}</strong> trên tổng <strong class="text-espresso">{{ $products->total() }}</strong> sản phẩm</p>
                
                {{-- Dropdown Sắp xếp --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-espresso/70">Sắp xếp:</span>
                    <select onchange="window.location.href=this.value" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-coral text-espresso font-medium bg-transparent cursor-pointer">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                    </select>
                </div>
            </div>

            {{-- Grid 3x3 Sản Phẩm --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <article class="product-card bg-white rounded-[24px] p-4 flex flex-col relative group border border-transparent hover:border-coral/20 shadow-sm">
                            <div class="w-full aspect-square rounded-[16px] overflow-hidden bg-cream relative mb-4">
                                <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/400' }}" class="product-image w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $product->name }}" />
                                </a>
                                
                                {{-- Nút giỏ hàng --}}
                                <button class="absolute z-10 bottom-4 right-4 bg-white text-espresso w-10 h-10 rounded-full flex items-center justify-center shadow-lg opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-coral hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                </button>
                            </div>
                            
                            <h3 class="font-serif font-bold text-xl text-espresso mb-1 group-hover:text-coral transition-colors line-clamp-1">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="text-sm text-espresso/60 mb-3 line-clamp-2">{{ $product->description }}</p>
                            
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-espresso font-black text-lg">Từ {{ number_format($product->price, 0, ',', '.') }} đ</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Phân trang Pagination --}}
                <div class="mt-12 flex justify-center">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            @else
                {{-- Trạng thái trống (Empty State) --}}
                <div class="bg-white rounded-[24px] p-12 text-center border border-espresso/5 shadow-sm">
                    <svg class="w-16 h-16 text-espresso/20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <h3 class="text-2xl font-serif font-bold text-espresso mb-2">Không tìm thấy sản phẩm!</h3>
                    <p class="text-espresso/60 mb-6">Rất tiếc, không có món nước nào phù hợp với bộ lọc của bạn.</p>
                    <a href="{{ route('product.index') }}" class="inline-block bg-coral text-white font-bold px-8 py-3 rounded-full hover:bg-espresso transition-colors">Xóa bộ lọc</a>
                </div>
            @endif

        </main>
    </div>
</div>
@endsection