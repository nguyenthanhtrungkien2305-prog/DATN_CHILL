@extends('admin.layouts.app')

@section('title', 'Theo Dõi Lượt Bán & Giảm Giá Sản Phẩm - Chill Chill')

@section('content')
<div class="p-6 space-y-6 max-w-[1600px] mx-auto">

    {{-- HEADER & TIÊU ĐỀ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Theo dõi lượt bán & Giảm giá sản phẩm
            </h1>
            <p class="text-sm text-gray-500 mt-1">Phân tích hiệu suất bán hàng của từng món và thiết lập chương trình giảm giá kích cầu cho các món có lượt mua thấp</p>
        </div>
        <div class="flex items-center">
            <form method="POST" action="{{ route('admin.product_sales.auto_discount') }}" onsubmit="return confirm('Hệ thống sẽ quét tìm 5 sản phẩm có lượt mua ít nhất và áp dụng mức giảm giá ngẫu nhiên từ 2% đến 10%. Bạn có muốn tiếp tục?');">
                @csrf
                <input type="hidden" name="count" value="5">
                <input type="hidden" name="min" value="2">
                <input type="hidden" name="max" value="10">
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition shadow-md">
                    Chạy giảm giá 5 món bán thấp (2-10%)
                </button>
            </form>
        </div>
    </div>

    {{-- BANNER TỰ ĐỘNG HÓA HÀNG TUẦN --}}
    <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/5 border border-amber-300/60 rounded-2xl p-4 text-xs text-amber-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
        <div>
            <p class="font-bold text-sm text-gray-900">Lịch tự động hàng tuần:</p>
            <p class="text-gray-600 mt-0.5">Hệ thống được thiết lập tự động quét và áp dụng mức giảm giá từ <strong>2% - 10%</strong> cho <strong>5 sản phẩm có lượt mua ít nhất</strong> vào mỗi <strong>Thứ Hai lúc 00:00</strong>.</p>
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Đang bật tự động (Mỗi tuần)
            </span>
        </div>
    </div>

    {{-- THÔNG BÁO FLASH MESSAGE --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm font-medium shadow-sm">
            <div>{{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm font-medium shadow-sm">
            <div>{{ session('error') }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold ml-4">✕</button>
        </div>
    @endif

    {{-- 4 THẺ THỐNG KÊ TỔNG QUAN (METRICS CARDS) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Card 1: Tổng sản phẩm --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng số món</p>
            <h3 class="text-3xl font-black text-gray-800 mt-2">{{ $totalProductsCount }}</h3>
            <p class="text-xs text-gray-500 mt-1">Đang kinh doanh trên web</p>
        </div>

        {{-- Card 2: Món bán thấp (Cần ưu đãi) --}}
        <a href="{{ route('admin.product_sales.index', ['filter' => 'low']) }}" class="bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-2xl p-5 border border-amber-200 shadow-sm hover:shadow-md transition block">
            <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Lượt mua thấp (≤ 5)</p>
            <h3 class="text-3xl font-black text-amber-900 mt-2">{{ $lowSalesCount }} <span class="text-xs font-normal text-amber-700">món</span></h3>
            <p class="text-xs text-amber-800 font-semibold mt-1">Cần áp dụng giảm giá kích cầu</p>
        </a>

        {{-- Card 3: Món đang giảm giá --}}
        <a href="{{ route('admin.product_sales.index', ['filter' => 'discounted']) }}" class="bg-gradient-to-br from-coral/10 to-red-50/40 rounded-2xl p-5 border border-coral/30 shadow-sm hover:shadow-md transition block">
            <p class="text-xs font-bold text-coral uppercase tracking-wider">Đang áp dụng giảm giá</p>
            <h3 class="text-3xl font-black text-espresso mt-2">{{ $discountedCount }} <span class="text-xs font-normal text-gray-600">món</span></h3>
            <p class="text-xs text-coral font-semibold mt-1">Đang có nhãn sale trên web</p>
        </a>

        {{-- Card 4: Tổng lượt bán & Doanh thu --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng sản lượng đã bán</p>
            <h3 class="text-3xl font-black text-emerald-600 mt-2">{{ number_format($totalSoldVolume) }} <span class="text-xs font-normal text-gray-500">ly/món</span></h3>
            <p class="text-xs text-gray-500 mt-1">Doanh thu: <strong>{{ number_format($totalRevenueVolume, 0, ',', '.') }}đ</strong></p>
        </div>

    </div>

    {{-- KHU VỰC BỘ LỌC, TAB VÀ TÌM KIẾM --}}
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 space-y-5">
        
        {{-- Hàng 1: Tabs phân loại trạng thái --}}
        <div class="flex items-center justify-between flex-wrap gap-3 border-b border-gray-100 pb-4">
            <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1">
                <a href="{{ route('admin.product_sales.index', array_merge(request()->except('filter', 'page'), ['filter' => 'all'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $currentFilter === 'all' ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tất cả sản phẩm ({{ $totalProductsCount }})
                </a>

                <a href="{{ route('admin.product_sales.index', array_merge(request()->except('filter', 'page'), ['filter' => 'low'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $currentFilter === 'low' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    Lượt mua thấp ≤ 5 ({{ $lowSalesCount }})
                </a>

                <a href="{{ route('admin.product_sales.index', array_merge(request()->except('filter', 'page'), ['filter' => 'discounted'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $currentFilter === 'discounted' ? 'bg-[#e8634a] text-white shadow-sm' : 'bg-red-50 text-coral hover:bg-red-100' }}">
                    Đang giảm giá ({{ $discountedCount }})
                </a>

                <a href="{{ route('admin.product_sales.index', array_merge(request()->except('filter', 'page'), ['filter' => 'normal'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $currentFilter === 'normal' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    Bán ổn định (6-20)
                </a>

                <a href="{{ route('admin.product_sales.index', array_merge(request()->except('filter', 'page'), ['filter' => 'hot'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $currentFilter === 'hot' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Bán chạy > 20 ({{ $hotSalesCount }})
                </a>
            </div>

            {{-- Thông tin phân trang --}}
            <div class="text-xs text-gray-500 font-medium">
                Hiển thị {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} trên tổng số {{ $products->total() }} món
            </div>
        </div>

        {{-- Hàng 2: Form Lọc nâng cao & Tìm kiếm --}}
        <form method="GET" action="{{ route('admin.product_sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <input type="hidden" name="filter" value="{{ $currentFilter }}">

            {{-- Ô tìm kiếm --}}
            <div class="lg:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên sản phẩm..." 
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-[#e8634a] transition">
            </div>

            {{-- Lọc danh mục --}}
            <div class="lg:col-span-3">
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-[#e8634a] transition cursor-pointer">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sắp xếp --}}
            <div class="lg:col-span-3">
                <select name="sort_by" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-[#e8634a] transition cursor-pointer">
                    <option value="sold_asc" {{ request('sort_by') == 'sold_asc' ? 'selected' : '' }}>Lượt bán: Ít nhất trước</option>
                    <option value="sold_desc" {{ request('sort_by') == 'sold_desc' ? 'selected' : '' }}>Lượt bán: Nhiều nhất trước</option>
                    <option value="discount_desc" {{ request('sort_by') == 'discount_desc' ? 'selected' : '' }}>Mức giảm giá: Cao nhất trước</option>
                    <option value="revenue_desc" {{ request('sort_by') == 'revenue_desc' ? 'selected' : '' }}>Doanh thu: Cao nhất trước</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Tên món: A - Z</option>
                </select>
            </div>

            {{-- Nút lọc --}}
            <div class="lg:col-span-1 flex gap-2">
                <button type="submit" class="w-full py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-black transition">
                    Lọc
                </button>
            </div>
        </form>

    </div>

    {{-- THANH TÁC VỤ HÀNG LOẠT (BULK ACTIONS BAR) --}}
    <form id="bulk-action-form" method="POST" action="">
        @csrf
        <div id="bulk-toolbar" class="hidden bg-[#2B2623] text-white p-4 rounded-2xl mb-4 flex flex-wrap items-center justify-between gap-4 shadow-xl animate-in fade-in duration-200">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold">
                    Đã chọn <strong id="selected-count" class="text-coral text-base font-black">0</strong> sản phẩm
                </span>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="openBulkDiscountModal()" class="px-4 py-2 bg-[#e8634a] hover:bg-[#d5523b] text-white rounded-xl text-xs font-bold transition shadow-md">
                    Giảm giá hàng loạt
                </button>
                <button type="button" onclick="submitBulkResetDiscount()" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">
                    Khôi phục giá gốc (0%)
                </button>
            </div>
        </div>

        {{-- BẢNG DANH SÁCH SẢN PHẨM & HIỆU SUẤT BÁN --}}
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200/80 text-gray-600 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-4 w-12 text-center">
                                <input type="checkbox" id="check-all" onclick="toggleSelectAll(this)" class="w-4 h-4 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a] cursor-pointer">
                            </th>
                            <th class="py-4 px-4">Sản phẩm</th>
                            <th class="py-4 px-4">Danh mục</th>
                            <th class="py-4 px-4 text-right">Giá gốc</th>
                            <th class="py-4 px-4 text-center">Mức giảm (%)</th>
                            <th class="py-4 px-4 text-right">Giá sau giảm</th>
                            <th class="py-4 px-4 text-center">Lượt đã bán</th>
                            <th class="py-4 px-4 text-right">Tổng doanh thu</th>
                            <th class="py-4 px-4 text-center">Đánh giá hiệu suất</th>
                            <th class="py-4 px-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($products as $p)
                            @php
                                $imgSrc = format_image_url($p->image_url, '/images/logo1.jpg', $p->name);
                            @endphp
                            <tr class="hover:bg-amber-50/30 transition-colors {{ $p->performance_tier === 'low' ? 'bg-amber-50/10' : '' }}">
                                
                                {{-- Checkbox --}}
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" name="product_ids[]" value="{{ $p->product_id }}" onchange="updateBulkToolbar()" class="product-checkbox w-4 h-4 text-[#e8634a] rounded border-gray-300 focus:ring-[#e8634a] cursor-pointer">
                                </td>

                                {{-- Sản phẩm --}}
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $imgSrc }}" alt="{{ $p->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200/80 shrink-0 bg-gray-50">
                                        <div>
                                            <a href="{{ route('product.show', $p->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-[#e8634a] transition-colors line-clamp-1">
                                                {{ $p->name }}
                                            </a>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[11px] text-gray-400">ID: #{{ $p->product_id }}</span>
                                                @if($p->is_featured)
                                                    <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.2 rounded">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Danh mục --}}
                                <td class="py-4 px-4">
                                    <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">
                                        {{ $p->category_name ?? 'Chưa phân loại' }}
                                    </span>
                                </td>

                                {{-- Giá gốc --}}
                                <td class="py-4 px-4 text-right font-medium text-gray-700">
                                    @if($p->min_price == $p->max_price)
                                        {{ number_format($p->min_price, 0, ',', '.') }}đ
                                    @else
                                        {{ number_format($p->min_price, 0, ',', '.') }}đ - {{ number_format($p->max_price, 0, ',', '.') }}đ
                                    @endif
                                </td>

                                {{-- Mức giảm giá hiện tại (%) --}}
                                <td class="py-4 px-4 text-center">
                                    @if($p->discount_percent > 0)
                                        <span class="inline-flex items-center bg-red-100 text-red-700 font-black text-xs px-2.5 py-1 rounded-full border border-red-200">
                                            -{{ $p->discount_percent }}%
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold">0% (Giá gốc)</span>
                                    @endif
                                </td>

                                {{-- Giá sau giảm --}}
                                <td class="py-4 px-4 text-right">
                                    @if($p->discount_percent > 0)
                                        <span class="font-extrabold text-[#e8634a]">
                                            @if($p->sale_min_price == $p->sale_max_price)
                                                {{ number_format($p->sale_min_price, 0, ',', '.') }}đ
                                            @else
                                                {{ number_format($p->sale_min_price, 0, ',', '.') }}đ - {{ number_format($p->sale_max_price, 0, ',', '.') }}đ
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Không giảm</span>
                                    @endif
                                </td>

                                {{-- Lượt đã bán --}}
                                <td class="py-4 px-4 text-center">
                                    <span class="font-black text-base {{ $p->sold_count > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ number_format($p->sold_count) }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block">ly đã bán</span>
                                </td>

                                {{-- Doanh thu --}}
                                <td class="py-4 px-4 text-right font-bold text-gray-800">
                                    {{ number_format($p->total_revenue, 0, ',', '.') }}đ
                                </td>

                                {{-- Đánh giá hiệu suất --}}
                                <td class="py-4 px-4 text-center">
                                    @if($p->performance_tier === 'low')
                                        <span class="inline-flex items-center bg-amber-100 text-amber-900 border border-amber-300/80 text-xs font-bold px-2.5 py-1 rounded-xl">
                                            Lượt mua thấp
                                        </span>
                                    @elseif($p->performance_tier === 'hot')
                                        <span class="inline-flex items-center bg-emerald-100 text-emerald-800 border border-emerald-300/80 text-xs font-bold px-2.5 py-1 rounded-xl">
                                            Bán chạy
                                        </span>
                                    @else
                                        <span class="inline-flex items-center bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold px-2.5 py-1 rounded-xl">
                                            Ổn định
                                        </span>
                                    @endif
                                </td>

                                {{-- Hành động --}}
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Nút Giảm giá nhanh --}}
                                        <button type="button" onclick="openSingleDiscountModal({{ $p->product_id }}, '{{ addslashes($p->name) }}', {{ $p->discount_percent }})" 
                                                class="px-3 py-1.5 bg-[#e8634a] hover:bg-[#d5523b] text-white text-xs font-bold rounded-lg transition shadow-sm" title="Đặt % Giảm giá cho món này">
                                            Giảm giá
                                        </button>

                                        {{-- Nút Hủy giảm nếu đang có giảm giá --}}
                                        @if($p->discount_percent > 0)
                                            <form method="POST" action="{{ route('admin.product_sales.discount', $p->product_id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy giảm giá cho món này và khôi phục về giá gốc?');">
                                                @csrf
                                                <input type="hidden" name="discount_percent" value="0">
                                                <button type="submit" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition" title="Hủy giảm giá">
                                                    Hủy
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-gray-400">
                                    <p class="text-base font-semibold">Không tìm thấy sản phẩm nào phù hợp với bộ lọc</p>
                                    <a href="{{ route('admin.product_sales.index') }}" class="text-xs text-[#e8634a] font-bold mt-1 inline-block hover:underline">Xóa tất cả bộ lọc</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if($products->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif

        </div>
    </form>

</div>

{{-- MODAL 1: THIẾT LẬP GIẢM GIÁ CHO 1 MÓN DUY NHẤT --}}
<div id="single-discount-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 max-w-md w-full p-6 sm:p-8 relative overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150">
        
        <button type="button" onclick="closeSingleDiscountModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center text-sm font-bold transition">
            ✕
        </button>

        <div class="text-center mb-6">
            <h3 class="font-bold text-xl text-gray-900">Thiết lập Giảm giá Kích cầu</h3>
            <p id="modal-single-product-name" class="text-sm text-[#e8634a] font-bold mt-1 line-clamp-1"></p>
            <p class="text-xs text-gray-500 mt-1">Mức giảm giá sẽ tự động được áp dụng trực tiếp lên giá bán trên toàn bộ Website</p>
        </div>

        <form id="single-discount-form" method="POST" action="" class="space-y-5">
            @csrf

            {{-- Nhập % giảm giá --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Mức giảm giá (%):</label>
                <div class="relative flex items-center">
                    <input type="number" id="single-discount-input" name="discount_percent" min="0" max="90" required placeholder="Nhập số từ 0 đến 90" 
                           class="w-full px-4 py-3.5 bg-gray-50 border-2 border-amber-300 rounded-2xl text-center text-2xl font-black text-gray-800 focus:bg-white focus:border-[#e8634a] focus:outline-none transition">
                    <span class="absolute right-4 text-xl font-black text-gray-400">%</span>
                </div>
            </div>

            {{-- Các nút gợi ý % nhanh --}}
            <div>
                <span class="text-xs text-gray-500 block mb-2 font-medium">Chọn nhanh mức giảm phổ biến:</span>
                <div class="grid grid-cols-5 gap-2">
                    <button type="button" onclick="setSingleDiscount(10)" class="py-2 bg-gray-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-gray-700 transition">10%</button>
                    <button type="button" onclick="setSingleDiscount(15)" class="py-2 bg-gray-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-gray-700 transition">15%</button>
                    <button type="button" onclick="setSingleDiscount(20)" class="py-2 bg-amber-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-amber-900 transition">20%</button>
                    <button type="button" onclick="setSingleDiscount(30)" class="py-2 bg-amber-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-amber-900 transition">30%</button>
                    <button type="button" onclick="setSingleDiscount(50)" class="py-2 bg-red-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-red-900 transition">50%</button>
                </div>
            </div>

            {{-- Nút xác nhận --}}
            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeSingleDiscountModal()" class="w-1/3 py-3 border border-gray-300 rounded-2xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Hủy
                </button>
                <button type="submit" class="w-2/3 py-3 bg-[#e8634a] hover:bg-[#d5523b] text-white rounded-2xl text-xs font-bold transition shadow-lg shadow-orange-500/20">
                    Áp dụng giảm giá
                </button>
            </div>
        </form>

    </div>
</div>

{{-- MODAL 2: THIẾT LẬP GIẢM GIÁ HÀNG LOẠT (BULK DISCOUNT MODAL) --}}
<div id="bulk-discount-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 max-w-md w-full p-6 sm:p-8 relative overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150">
        
        <button type="button" onclick="closeBulkDiscountModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center text-sm font-bold transition">
            ✕
        </button>

        <div class="text-center mb-6">
            <h3 class="font-bold text-xl text-gray-900">Giảm giá Hàng loạt</h3>
            <p class="text-sm text-gray-600 mt-1">Đang áp dụng cho <strong id="modal-bulk-count" class="text-[#e8634a]">0</strong> sản phẩm đã chọn</p>
        </div>

        <div class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Nhập % giảm giá đồng loạt:</label>
                <div class="relative flex items-center">
                    <input type="number" id="bulk-discount-input" min="1" max="90" value="20" placeholder="Nhập % giảm" 
                           class="w-full px-4 py-3.5 bg-gray-50 border-2 border-amber-300 rounded-2xl text-center text-2xl font-black text-gray-800 focus:bg-white focus:border-[#e8634a] focus:outline-none transition">
                    <span class="absolute right-4 text-xl font-black text-gray-400">%</span>
                </div>
            </div>

            <div>
                <span class="text-xs text-gray-500 block mb-2 font-medium">Mức giảm khuyến nghị cho món bán thấp:</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="document.getElementById('bulk-discount-input').value=15" class="py-2 bg-gray-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-gray-700 transition">15%</button>
                    <button type="button" onclick="document.getElementById('bulk-discount-input').value=20" class="py-2 bg-amber-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-amber-900 transition">20%</button>
                    <button type="button" onclick="document.getElementById('bulk-discount-input').value=30" class="py-2 bg-amber-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-amber-900 transition">30%</button>
                    <button type="button" onclick="document.getElementById('bulk-discount-input').value=50" class="py-2 bg-red-100 hover:bg-[#e8634a] hover:text-white rounded-xl text-xs font-bold text-red-900 transition">50%</button>
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeBulkDiscountModal()" class="w-1/3 py-3 border border-gray-300 rounded-2xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Hủy
                </button>
                <button type="button" onclick="submitBulkDiscount()" class="w-2/3 py-3 bg-[#e8634a] hover:bg-[#d5523b] text-white rounded-2xl text-xs font-bold transition shadow-lg shadow-orange-500/20">
                    Áp dụng tất cả
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // === XỬ LÝ CHỌN CHECKBOX & HIỂN THỊ TOOLBAR HÀNG LOẠT ===
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBulkToolbar();
    }

    function updateBulkToolbar() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const count = checkedBoxes.length;
        const toolbar = document.getElementById('bulk-toolbar');
        const countDisplay = document.getElementById('selected-count');

        if (countDisplay) countDisplay.innerText = count;

        if (count > 0) {
            toolbar.classList.remove('hidden');
        } else {
            toolbar.classList.add('hidden');
        }
    }

    // === MODAL GIẢM GIÁ 1 SẢN PHẨM ===
    function openSingleDiscountModal(productId, productName, currentDiscount) {
        const modal = document.getElementById('single-discount-modal');
        const form = document.getElementById('single-discount-form');
        const nameEl = document.getElementById('modal-single-product-name');
        const input = document.getElementById('single-discount-input');

        form.action = `/admin/product-sales/${productId}/discount`;
        nameEl.innerText = productName;
        input.value = currentDiscount > 0 ? currentDiscount : 20;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        input.focus();
    }

    function closeSingleDiscountModal() {
        const modal = document.getElementById('single-discount-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setSingleDiscount(val) {
        const input = document.getElementById('single-discount-input');
        if (input) input.value = val;
    }

    // === MODAL GIẢM GIÁ HÀNG LOẠT ===
    function openBulkDiscountModal() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm!');
            return;
        }

        document.getElementById('modal-bulk-count').innerText = checkedBoxes.length;
        const modal = document.getElementById('bulk-discount-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeBulkDiscountModal() {
        const modal = document.getElementById('bulk-discount-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitBulkDiscount() {
        const val = document.getElementById('bulk-discount-input').value;
        if (!val || val < 1 || val > 90) {
            alert('Vui lòng nhập mức giảm giá hợp lệ từ 1% đến 90%!');
            return;
        }

        const form = document.getElementById('bulk-action-form');
        form.action = "{{ route('admin.product_sales.bulk_discount') }}";

        // Thêm input ẩn discount_percent vào form
        let input = form.querySelector('input[name="discount_percent"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'discount_percent';
            form.appendChild(input);
        }
        input.value = val;

        form.submit();
    }

    function submitBulkResetDiscount() {
        if (!confirm('Bạn có chắc chắn muốn khôi phục giá gốc (0% giảm giá) cho toàn bộ các sản phẩm đã chọn?')) {
            return;
        }

        const form = document.getElementById('bulk-action-form');
        form.action = "{{ route('admin.product_sales.bulk_reset') }}";
        form.submit();
    }

    // Đóng modal khi bấm ESC hoặc click ra ngoài nền
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSingleDiscountModal();
            closeBulkDiscountModal();
        }
    });
</script>
@endsection
