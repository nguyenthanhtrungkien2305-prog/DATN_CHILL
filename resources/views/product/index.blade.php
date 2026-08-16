@extends('layouts.app')

@php
    $selectedCategory = request('category') ? $categories->firstWhere('category_id', request('category')) : null;
@endphp

@section('title', ($selectedCategory->name ?? 'Thực Đơn Đồ Uống') . ' - Chill Chill Coffee & Tea')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-[#FAF7F2] min-h-screen pb-10" x-data="{ viewMode: 'grid', isFilterOpen: false, showCategories: true, showPrice: true, showBrands: false, showSize: false, showSugar: false, showRating: false }">

    {{-- BANNER KHUYẾN MÃI NỔI BẬT ĐẦU TRANG --}}
    <div class="bg-gradient-to-r from-espresso via-coral to-amber-600 text-white shadow-md relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm font-semibold">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full bg-white/20 text-white font-extrabold uppercase tracking-wider text-[10px]">Ưu đãi Hot</span>
                <span class="truncate">FREESHIP TOÀN QUỐC cho đơn hàng từ 299K</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-white/10 px-3 py-1 rounded-xl backdrop-blur-sm border border-white/20">
                    NHẬP MÃ: <strong class="text-amber-300 font-extrabold">SUMMER20</strong> (Giảm 20% đơn từ 199K)
                </span>
                <a href="#combo-banner" class="hidden md:inline-flex items-center gap-1 underline text-white/90 hover:text-white transition-colors text-xs">
                    Chi tiết &rarr;
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">

        {{-- BREADCRUMB --}}
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-espresso/60 mb-5 font-medium">
            <a href="{{ url('/') }}" class="hover:text-coral transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> Trang chủ
            </a>
            <span>/</span>
            <a href="{{ route('product.index') }}" class="hover:text-coral transition-colors">Thực đơn</a>
            @if($selectedCategory)
                <span>/</span>
                <span class="text-coral font-bold truncate">{{ $selectedCategory->name }}</span>
            @endif
        </nav>

        {{-- HERO DANH MỤC --}}
        <div class="relative bg-white rounded-3xl p-6 sm:p-8 border border-espresso/5 shadow-sm overflow-hidden mb-6">
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-coral/10 rounded-full blur-3xl -z-0"></div>
            <div class="absolute right-1/3 -bottom-20 w-60 h-60 bg-amber-100/50 rounded-full blur-2xl -z-0"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center relative z-10">
                <div class="lg:col-span-7 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral/10 border border-coral/20 text-coral text-xs font-bold uppercase tracking-wider">
                        Thực Đơn Đa Dạng
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-extrabold text-espresso tracking-tight leading-tight">
                        {{ $selectedCategory->name ?? 'Thực Đơn Đồ Uống' }}
                    </h1>
                    <p class="text-espresso/70 text-xs sm:text-sm leading-relaxed max-w-2xl font-normal">
                        Khám phá hàng ngàn lựa chọn đồ uống thơm ngon, tươi mát từ trà sữa, cà phê, nước ép đến soda và sinh tố. Chất lượng tuyệt hảo – Giao hàng nhanh chóng – Giá cả hợp lý.
                    </p>
                </div>

                {{-- Hình ảnh minh họa Hero --}}
                <div class="lg:col-span-5 hidden md:flex items-center justify-center gap-3">
                    <div class="relative group">
                        <img src="/images/anhcauchuyen1.png" 
                             alt="Coffee Showcase" 
                             class="w-28 h-36 object-cover rounded-2xl shadow-md border-2 border-white transform -rotate-6 group-hover:rotate-0 transition-all duration-300">
                    </div>
                    <div class="relative group -mt-4">
                        <img src="/images/tiramisu.jpg" 
                             alt="Boba Tea Showcase" 
                             class="w-32 h-40 object-cover rounded-2xl shadow-xl border-2 border-white transform group-hover:scale-105 transition-all duration-300">
                    </div>
                    <div class="relative group">
                        <img src="/images/banhsukem.jpg" 
                             alt="Juice Showcase" 
                             class="w-28 h-36 object-cover rounded-2xl shadow-md border-2 border-white transform rotate-6 group-hover:rotate-0 transition-all duration-300">
                    </div>
                </div>
            </div>
        </div>

        {{-- BỐ CỤC CHÍNH (SIDEBAR FILTER + PRODUCT LISTING) --}}
        <form action="{{ route('product.index') }}" method="GET" id="filter-form">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <div class="flex flex-col lg:flex-row gap-6 items-start">

                {{-- BỘ LỌC SIDEBAR CÓ DÀN TRẢI KHÔNG GIAN ĐẦY ĐẶN KHÔNG TRỐNG --}}
                <aside class="w-full lg:w-72 shrink-0 space-y-4 lg:sticky lg:top-24">
                    {{-- Mobile Filter Toggle Button --}}
                    <div class="lg:hidden mb-4">
                        <button type="button" 
                                @click="isFilterOpen = !isFilterOpen"
                                class="w-full py-3 px-5 bg-white border border-espresso/10 rounded-2xl font-bold text-espresso flex items-center justify-between shadow-sm">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                Bộ Lọc Tìm Kiếm & Phân Loại
                            </span>
                            <svg class="w-5 h-5 text-espresso/40 transition-transform duration-200" :class="isFilterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4 lg:block" :class="isFilterOpen ? 'block' : 'hidden lg:block'">
                        {{-- KHỐI BỘ LỌC --}}
                        <div class="bg-white rounded-3xl p-5 border border-espresso/10 shadow-sm">
                            
                            {{-- Header Bộ Lọc --}}
                            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-espresso/10">
                                <h3 class="font-bold text-sm text-espresso flex items-center gap-2">
                                    <svg class="w-4 h-4 text-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Bộ lọc tìm kiếm
                                </h3>
                                <a href="{{ route('product.index') }}" class="text-xs font-semibold text-coral hover:underline">
                                    Xóa tất cả
                                </a>
                            </div>

                            {{-- MỤC 1: LOẠI ĐỒ UỐNG (CATEGORIES) --}}
                            <div class="mb-4 pb-4 border-b border-espresso/5">
                                <button type="button" 
                                        @click="showCategories = !showCategories" 
                                        class="w-full font-bold text-xs uppercase tracking-wider text-espresso mb-3 flex items-center justify-between text-left hover:text-coral transition-colors">
                                    <span>Loại đồ uống</span>
                                    <svg class="w-4 h-4 text-espresso/40 transition-transform duration-200" :class="showCategories ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                <div x-show="showCategories" class="space-y-2 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
                                    <label class="flex items-center justify-between group cursor-pointer text-xs py-1">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" 
                                                   name="category" 
                                                   value="" 
                                                   onchange="this.form.submit()"
                                                   {{ !request('category') ? 'checked' : '' }}
                                                   class="w-4 h-4 accent-coral">
                                            <span class="text-espresso/80 font-medium group-hover:text-coral transition-colors">Tất cả sản phẩm</span>
                                        </div>
                                    </label>

                                    @foreach($categories as $cat)
                                        <label class="flex items-center justify-between group cursor-pointer text-xs py-1">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" 
                                                       name="category" 
                                                       value="{{ $cat->category_id }}" 
                                                       onchange="this.form.submit()"
                                                       {{ request('category') == $cat->category_id ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-coral">
                                                <span class="text-espresso/80 font-medium group-hover:text-coral transition-colors">{{ $cat->name }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- MỤC 2: KHOẢNG GIÁ (PRICE RANGE) --}}
                            <div class="mb-4 pb-4 border-b border-espresso/5">
                                <button type="button" 
                                        @click="showPrice = !showPrice" 
                                        class="w-full font-bold text-xs uppercase tracking-wider text-espresso mb-3 flex items-center justify-between text-left hover:text-coral transition-colors">
                                    <span>Khoảng giá (đ)</span>
                                    <svg class="w-4 h-4 text-espresso/40 transition-transform duration-200" :class="showPrice ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                <div x-show="showPrice" class="space-y-3">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <span class="text-[10px] font-semibold text-espresso/60 block mb-1">Từ (đ)</span>
                                            <input type="number" 
                                                   name="min_price" 
                                                   value="{{ request('min_price') }}"
                                                   placeholder="0"
                                                   step="5000"
                                                   class="w-full px-3 py-1.5 rounded-xl border border-gray-200 bg-[#FAF7F2] text-xs font-semibold focus:outline-none focus:border-coral">
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-semibold text-espresso/60 block mb-1">Đến (đ)</span>
                                            <input type="number" 
                                                   name="max_price" 
                                                   value="{{ request('max_price') }}"
                                                   placeholder="200000"
                                                   step="5000"
                                                   class="w-full px-3 py-1.5 rounded-xl border border-gray-200 bg-[#FAF7F2] text-xs font-semibold focus:outline-none focus:border-coral">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-2 bg-espresso text-white font-bold text-xs rounded-xl hover:bg-coral transition-all shadow-sm">
                                        Áp dụng lọc giá
                                    </button>
                                </div>
                            </div>

                            {{-- MỤC 3: DUNG TÍCH / SIZE --}}
                            <div class="mb-4 pb-4 border-b border-espresso/5">
                                <button type="button" 
                                        @click="showSize = !showSize" 
                                        class="w-full font-bold text-xs uppercase tracking-wider text-espresso mb-2 flex items-center justify-between text-left hover:text-coral transition-colors">
                                    <span>Dung tích / Size</span>
                                    <svg class="w-4 h-4 text-espresso/40 transition-transform duration-200" :class="showSize ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="showSize" class="flex flex-wrap gap-1.5 text-xs pt-1">
                                    <span class="px-2.5 py-1 rounded-lg border border-coral bg-coral/10 text-coral font-bold cursor-pointer">Size S (350ml)</span>
                                    <span class="px-2.5 py-1 rounded-lg border border-gray-200 bg-gray-50 text-espresso/80 font-medium hover:border-coral cursor-pointer">Size M (500ml)</span>
                                    <span class="px-2.5 py-1 rounded-lg border border-gray-200 bg-gray-50 text-espresso/80 font-medium hover:border-coral cursor-pointer">Size L (700ml)</span>
                                </div>
                            </div>

                            {{-- MỤC 4: ĐỘ NGỌT --}}
                            <div class="mb-4 pb-4 border-b border-espresso/5">
                                <button type="button" 
                                        @click="showSugar = !showSugar" 
                                        class="w-full font-bold text-xs uppercase tracking-wider text-espresso mb-2 flex items-center justify-between text-left hover:text-coral transition-colors">
                                    <span>Tùy chọn Độ Ngọt</span>
                                    <svg class="w-4 h-4 text-espresso/40 transition-transform duration-200" :class="showSugar ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="showSugar" class="grid grid-cols-3 gap-1.5 text-center text-xs pt-1">
                                    <span class="py-1 rounded-lg border border-gray-200 bg-gray-50 font-medium hover:border-coral cursor-pointer">0% Ít</span>
                                    <span class="py-1 rounded-lg border border-gray-200 bg-gray-50 font-medium hover:border-coral cursor-pointer">50% Vừa</span>
                                    <span class="py-1 rounded-lg border border-coral bg-coral/10 text-coral font-bold cursor-pointer">100% Chuẩn</span>
                                </div>
                            </div>

                            {{-- MỤC 5: ĐÁNH GIÁ SAO --}}
                            <div>
                                <button type="button" 
                                        @click="showRating = !showRating" 
                                        class="w-full font-bold text-xs uppercase tracking-wider text-espresso mb-2 flex items-center justify-between text-left hover:text-coral transition-colors">
                                    <span>Đánh giá sao</span>
                                    <svg class="w-4 h-4 text-espresso/40 transition-transform duration-200" :class="showRating ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="showRating" class="space-y-1.5 text-xs pt-1">
                                    <label class="flex items-center gap-2 cursor-pointer py-0.5">
                                        <input type="checkbox" checked class="rounded accent-coral">
                                        <span class="text-amber-500 font-bold">★★★★★ <span class="text-espresso/80 font-medium ml-1">5 sao</span></span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer py-0.5">
                                        <input type="checkbox" checked class="rounded accent-coral">
                                        <span class="text-amber-500 font-bold">★★★★☆ <span class="text-espresso/80 font-medium ml-1">trở lên</span></span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        {{-- WIDGET HOTLINE / GIAO HÀNG TỐC HÀNH TẠO SỰ ĐẦY ĐẶN GIAO DIỆN --}}
                        <div class="bg-gradient-to-br from-espresso to-[#3d231b] rounded-3xl p-5 text-white shadow-md space-y-3 border border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-coral/20 text-coral flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs uppercase tracking-wider text-coral">Giao Hàng Siêu Tốc</h4>
                                    <p class="text-[11px] text-white/70">Nhận nước nóng/lạnh trong 30p</p>
                                </div>
                            </div>
                            <p class="text-xs text-white/80 leading-relaxed">Cần tư vấn đặt đồ uống cho văn phòng hoặc sự kiện?</p>
                            <a href="tel:19006868" class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-coral text-white font-bold text-xs hover:bg-white hover:text-espresso transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Hotline: 1900 6868</span>
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- NỘI DUNG CHÍNH: DANH SÁCH SẢN PHẨM --}}
                <main class="flex-1 min-w-0 w-full">

                    {{-- THANH SẮP XẾP & CHẾ ĐỘ XEM --}}
                    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-espresso/5 shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs sm:text-sm text-espresso/70 font-medium">
                            Hiển thị 
                            <strong class="text-espresso font-bold">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong> 
                            trong 
                            <strong class="text-coral font-bold">{{ $products->total() ?? 0 }}</strong> 
                            sản phẩm
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            {{-- Dropdown Sắp xếp --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-espresso/60 whitespace-nowrap hidden sm:inline">Sắp xếp:</span>
                                <select name="sort" 
                                        onchange="this.form.submit()"
                                        class="px-3.5 py-2 rounded-2xl bg-[#FAF7F2] border border-gray-200 text-xs sm:text-sm font-semibold text-espresso focus:outline-none focus:border-coral cursor-pointer">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Phổ biến / Mới nhất</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                                </select>
                            </div>

                            {{-- Chuyển đổi chế độ xem Grid / List --}}
                            <div class="flex items-center gap-1 bg-[#FAF7F2] p-1 rounded-2xl border border-gray-200">
                                <button type="button" 
                                        @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-coral text-white shadow-sm' : 'text-espresso/60 hover:text-coral'"
                                        class="p-2 rounded-xl transition-all" 
                                        title="Chế độ Lưới">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="viewMode = 'list'"
                                        :class="viewMode === 'list' ? 'bg-coral text-white shadow-sm' : 'text-espresso/60 hover:text-coral'"
                                        class="p-2 rounded-xl transition-all" 
                                        title="Chế độ Danh sách">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- DANH SÁCH SẢN PHẨM (GRID / LIST) --}}
                    <div>
                        {{-- GRID VIEW --}}
                        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($products as $product)
                                @php
                                    $pId = $product->product_id;
                                    $pSlug = $product->slug ?? $pId;
                                    $pImage = format_image_url($product->image_url ?? null, '/images/trasuaccdd.jpg');
                                @endphp
                                <div class="reveal-up hover-lift hover-glow bg-white rounded-3xl p-4 border border-espresso/5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
                                    <div>
                                        {{-- Ảnh sản phẩm --}}
                                        <div class="relative overflow-hidden rounded-2xl mb-3 h-52 bg-cream">
                                            <a href="{{ route('product.show', $pSlug) }}" class="block w-full h-full">
                                                <img src="{{ $pImage }}"
                                                     onerror="this.onerror=null;this.src='/images/trasuaccdd.jpg';"
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            </a>
                                        </div>

                                        {{-- Tên sản phẩm --}}
                                        <a href="{{ route('product.show', $pSlug) }}" class="block font-serif font-bold text-espresso hover:text-coral text-base sm:text-lg transition-colors line-clamp-1">
                                            {{ $product->name }}
                                        </a>

                                        <p class="text-xs text-espresso/60 mt-1 line-clamp-2 min-h-[32px]">{{ $product->description ?? 'Hương vị thơm ngon khó cưỡng, chuẩn vị pha chế.' }}</p>

                                        {{-- Giá bán --}}
                                        <div class="mt-3 text-base sm:text-lg font-black text-coral">
                                            {{ number_format($product->price ?? 0, 0, ',', '.') }}đ
                                        </div>
                                    </div>

                                    {{-- Nút Thêm Vào Giỏ --}}
                                    <div class="pt-3 mt-3 border-t border-espresso/5">
                                        <button type="button"
                                                onclick="quickAddToCart({{ $pId }})"
                                                class="w-full py-2.5 rounded-xl bg-coral text-white font-bold text-xs hover:bg-[#d5523b] hover:shadow-md transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Thêm vào giỏ</span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-16 text-center bg-white rounded-3xl p-8 border border-espresso/5 shadow-sm">
                                    <h3 class="text-lg font-serif font-bold text-espresso">Không tìm thấy sản phẩm phù hợp</h3>
                                    <p class="text-sm text-espresso/60 mt-1">Thử thay đổi bộ lọc khoảng giá hoặc xem tất cả danh mục nhé.</p>
                                    <a href="{{ route('product.index') }}" class="inline-block mt-4 px-6 py-2.5 bg-coral text-white font-bold text-xs rounded-full shadow-md hover:bg-espresso transition-colors">
                                        Xem tất cả sản phẩm
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        {{-- LIST VIEW --}}
                        <div x-show="viewMode === 'list'" class="space-y-4" style="display: none;">
                            @foreach($products as $product)
                                @php
                                    $pId = $product->product_id;
                                    $pSlug = $product->slug ?? $pId;
                                    $pImage = format_image_url($product->image_url ?? null, '/images/trasuaccdd.jpg');
                                @endphp
                                <div class="bg-white rounded-3xl p-4 border border-espresso/5 shadow-sm hover:shadow-lg transition-all flex flex-col sm:flex-row items-center gap-5">
                                    <div class="relative w-full sm:w-36 h-36 shrink-0 overflow-hidden rounded-2xl bg-cream">
                                        <a href="{{ route('product.show', $pSlug) }}">
                                            <img src="{{ $pImage }}" 
                                                 onerror="this.onerror=null;this.src='/images/trasuaccdd.jpg';"
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                        </a>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1.5 text-center sm:text-left">
                                        <a href="{{ route('product.show', $pSlug) }}" class="block font-serif font-bold text-espresso text-lg hover:text-coral transition-colors truncate">
                                            {{ $product->name }}
                                        </a>
                                        <p class="text-xs text-espresso/60 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                                    </div>
                                    <div class="flex flex-col items-center sm:items-end justify-between shrink-0 gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-espresso/5 w-full sm:w-auto">
                                        <span class="text-xl font-black text-coral">
                                            {{ number_format($product->price ?? 0, 0, ',', '.') }}đ
                                        </span>
                                        <button type="button"
                                                onclick="quickAddToCart({{ $pId }})"
                                                class="px-5 py-2.5 rounded-xl bg-coral text-white font-bold text-xs hover:bg-[#d5523b] transition-all shadow-md flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Thêm vào giỏ</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- COMBO KHUYẾN MÃI TẬN DỤNG KHÔNG GIAN ĐẸP MẮT --}}
                    @php
                        $bTitle = $comboBanner->title ?? 'COMBO TIẾT KIỆM – UỐNG LÀ MÊ!';
                        $bBadge = $comboBanner->badge ?? 'Combo Tiết Kiệm Độc Quyền';
                        $bDesc = $comboBanner->description ?? 'Chọn ngay combo đồ uống & bánh ngọt yêu thích với giá ưu đãi cực sốc lên đến 25%.';
                        $bBtnText = $comboBanner->button_text ?? 'Xem ngay combo';
                        $bBtnLink = !empty($comboBanner->button_link) ? $comboBanner->button_link : route('combo.index');
                        $bBg = $comboBanner->bg_gradient ?? 'from-espresso via-coral to-amber-600';
                        if ($bBtnLink === '/combo' || $bBtnLink === 'combo') {
                            $bBtnLink = route('combo.index');
                        }
                    @endphp
                    <div id="combo-banner" class="mt-8 mb-6 relative overflow-hidden rounded-3xl bg-gradient-to-r {{ $bBg }} text-white p-6 sm:p-8 shadow-lg border border-white/10">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center relative z-10">
                            <div class="lg:col-span-8 space-y-3">
                                @if($bBadge)
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-amber-300 font-extrabold text-xs uppercase tracking-wider">
                                        {{ $bBadge }}
                                    </div>
                                @endif
                                <h3 class="text-2xl sm:text-3xl font-serif font-black tracking-tight text-white leading-tight">
                                    {{ $bTitle }}
                                </h3>
                                @if($bDesc)
                                    <p class="text-white/90 text-xs sm:text-sm font-medium max-w-xl leading-relaxed">
                                        {!! nl2br(e($bDesc)) !!}
                                    </p>
                                @endif
                            </div>

                            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col items-center justify-center lg:items-end gap-3">
                                <a href="{{ $bBtnLink }}" class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white text-espresso font-extrabold text-xs sm:text-sm hover:bg-cream hover:scale-105 transition-all duration-300 shadow-xl flex items-center justify-center gap-2 group/btn">
                                    <span>{{ $bBtnText }}</span>
                                    &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- PHÂN TRANG CUSTOM VIỆT HÓA ĐẸP MẮT --}}
                    @if(method_exists($products, 'hasPages') && $products->hasPages())
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white px-6 py-4 rounded-3xl border border-espresso/5 shadow-sm">
                            <div class="text-xs sm:text-sm font-medium text-espresso/60">
                                Hiển thị <strong class="text-espresso font-bold">{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong> trong tổng số <strong class="text-coral font-bold">{{ $products->total() }}</strong> sản phẩm
                            </div>

                            <div class="flex items-center gap-1.5 [&_p]:hidden">
                                {{ $products->links('pagination::tailwind') }}
                            </div>
                        </div>
                    @endif

                </main>
            </div>
        </form>

    </div>
</div>
@endsection