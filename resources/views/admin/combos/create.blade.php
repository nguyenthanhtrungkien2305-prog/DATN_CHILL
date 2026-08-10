@extends('admin.layouts.app')

@section('title', 'Thêm mới Combo Sản phẩm - Chill Chill Admin')

@section('content')
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800">Thêm Gói Combo Sản Phẩm Mới</h2>
        <a href="{{ route('combos.index') }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium">← Quay lại danh sách</a>
    </header>

    <div class="p-8 max-w-5xl mx-auto">
        {{-- Lỗi Validate --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('combos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Tên Combo --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tên gói Combo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           placeholder="Ví dụ: Combo Cà Phê Moka + Bánh Tiramisu" 
                           class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                </div>

                {{-- Giá Combo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Giá bán Combo (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1000"
                           placeholder="Ví dụ: 59000" 
                           class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm font-bold text-[#e8634a]">
                </div>

                {{-- Giá gốc --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Giá gốc tổng cộng (VNĐ) <span class="text-gray-400 font-normal">(Để trống để tự tính)</span></label>
                    <input type="number" name="original_price" id="original_price_input" value="{{ old('original_price') }}" min="0" step="1000"
                           placeholder="Tự động tính từ các món" 
                           class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm text-gray-600 bg-gray-50">
                </div>

                {{-- Tải ảnh đại diện Combo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hình ảnh Combo</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-[#e8634a] hover:file:bg-orange-100">
                    <p class="text-xs text-gray-400 mt-1">Hoặc dán URL hình ảnh ở ô bên cạnh.</p>
                </div>

                {{-- Hoặc URL hình ảnh --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hoặc URL ảnh trực tuyến</label>
                    <input type="text" name="image_url" value="{{ old('image_url') }}" 
                           placeholder="https://images.unsplash.com/..." 
                           class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">
                </div>

                {{-- Mô tả --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả chi tiết Combo</label>
                    <textarea name="description" rows="3" placeholder="Mô tả hấp dẫn về combo thức uống & bánh ngọt..." 
                              class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm">{{ old('description') }}</textarea>
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái hiển thị</label>
                    <select name="status" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:border-[#e8634a] text-sm bg-white">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Hiển thị ngay trên trang chủ</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tạm ẩn</option>
                    </select>
                </div>
            </div>

            <hr class="border-gray-100 my-6">

            {{-- Chọn sản phẩm vào Combo --}}
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Danh sách Sản Phẩm Trong Combo <span class="text-red-500">*</span></h3>
                        <p class="text-xs text-gray-500">Tìm kiếm hoặc chọn các sản phẩm thuộc gói combo này.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="add-product-btn" class="bg-gray-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-700 transition shrink-0">
                            + Thêm món thủ công
                        </button>
                    </div>
                </div>

                {{-- Công cụ Tìm Kiếm Nhanh Sản Phẩm --}}
                <div class="bg-orange-50/60 border border-orange-200/80 p-4 rounded-2xl mb-6 relative">
                    <label class="block text-xs font-bold text-[#e8634a] uppercase tracking-wider mb-1">🔍 Tìm nhanh sản phẩm thêm vào Combo:</label>
                    <div class="relative">
                        <input type="text" id="quick-search-input" 
                               placeholder="Nhập tên sản phẩm để tìm (ví dụ: Cà phê, Trà sữa, Bánh...)" 
                               class="w-full pl-10 pr-10 py-2.5 border border-orange-300 rounded-xl focus:outline-none focus:border-[#e8634a] focus:ring-2 focus:ring-orange-200 text-sm bg-white">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" id="clear-search-btn" class="hidden absolute right-3 top-3 text-gray-400 hover:text-gray-600 text-xs font-bold">✕</button>

                        {{-- Kết quả Tìm kiếm sổ xuống --}}
                        <div id="search-results-dropdown" class="hidden absolute z-30 left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 max-h-60 overflow-y-auto divide-y divide-gray-100">
                        </div>
                    </div>
                </div>

                {{-- Danh sách các món trong combo --}}
                <div id="combo-items-container" class="space-y-3">
                    {{-- Dòng món mặc định thứ 1 --}}
                    <div class="combo-item-row flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <div class="flex-1 flex items-center gap-2">
                            <input type="text" placeholder="🔎 Lọc tên món..." class="row-filter-input w-36 px-2.5 py-1.5 border rounded-lg text-xs bg-white focus:outline-none focus:border-[#e8634a]">
                            <select name="items[0][product_id]" class="product-select flex-1 px-3 py-2 border rounded-lg text-sm bg-white focus:outline-none focus:border-[#e8634a]" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->product_id }}" data-price="{{ $prod->price }}" data-name="{{ mb_strtolower($prod->name) }}">
                                        {{ $prod->name }} ({{ number_format($prod->price, 0, ',', '.') }}đ)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-between sm:justify-start gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 font-semibold">Số lượng:</span>
                                <input type="number" name="items[0][quantity]" value="1" min="1" class="item-qty w-20 px-3 py-2 border rounded-lg text-sm text-center font-bold focus:outline-none focus:border-[#e8634a]" required>
                            </div>
                            <button type="button" class="remove-row-btn p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Xóa món này">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nút lưu --}}
            <div class="pt-6 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('combos.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-medium text-sm hover:bg-gray-50 transition">Hủy</a>
                <button type="submit" class="px-8 py-2.5 rounded-xl bg-[#e8634a] text-white font-bold text-sm hover:bg-[#d5523b] shadow-md transition">
                    Lưu Gói Combo
                </button>
            </div>
        </form>
    </div>

    <script>
        const allProductsList = @json($products);

        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 1;
            const container = document.getElementById('combo-items-container');
            const addBtn = document.getElementById('add-product-btn');
            const searchInput = document.getElementById('quick-search-input');
            const searchDropdown = document.getElementById('search-results-dropdown');
            const clearSearchBtn = document.getElementById('clear-search-btn');

            // --- TÌM KIẾM NHANH SẢN PHẨM ---
            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim().toLowerCase();
                if (keyword.length > 0) {
                    clearSearchBtn.classList.remove('hidden');
                    const filtered = allProductsList.filter(p => p.name.toLowerCase().includes(keyword));
                    renderSearchResults(filtered);
                } else {
                    clearSearchBtn.classList.add('hidden');
                    searchDropdown.classList.add('hidden');
                }
            });

            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
                searchDropdown.classList.add('hidden');
            });

            function renderSearchResults(items) {
                if (items.length === 0) {
                    searchDropdown.innerHTML = `<div class="p-4 text-center text-xs text-gray-400">Không tìm thấy sản phẩm phù hợp.</div>`;
                } else {
                    searchDropdown.innerHTML = items.map(p => `
                        <div class="search-item-result p-3 hover:bg-orange-50 cursor-pointer flex items-center justify-between transition" data-id="${p.product_id}" data-price="${p.price}" data-name="${p.name}">
                            <div class="flex items-center gap-3">
                                <img src="${p.image_url || 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=150&auto=format&fit=crop'}" class="w-10 h-10 object-cover rounded-lg border">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800">${p.name}</h4>
                                    <span class="text-xs text-[#e8634a] font-bold">${Number(p.price).toLocaleString('vi-VN')}đ</span>
                                </div>
                            </div>
                            <span class="bg-[#e8634a] text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-[#d5523b]">+ Thêm vào Combo</span>
                        </div>
                    `).join('');
                }
                searchDropdown.classList.remove('hidden');
            }

            searchDropdown.addEventListener('click', function(e) {
                const itemEl = e.target.closest('.search-item-result');
                if (itemEl) {
                    const productId = itemEl.getAttribute('data-id');
                    addProductRow(productId);
                    searchInput.value = '';
                    searchDropdown.classList.add('hidden');
                    clearSearchBtn.classList.add('hidden');
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#quick-search-input') && !e.target.closest('#search-results-dropdown')) {
                    searchDropdown.classList.add('hidden');
                }
            });

            // --- THÊM DÒNG MÓN ---
            addBtn.addEventListener('click', function() {
                addProductRow();
            });

            function addProductRow(selectedId = null) {
                const newRow = document.createElement('div');
                newRow.className = 'combo-item-row flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200';
                
                let optionsHtml = '<option value="">-- Chọn sản phẩm --</option>';
                allProductsList.forEach(prod => {
                    const isSel = (selectedId && parseInt(selectedId) === parseInt(prod.product_id)) ? 'selected' : '';
                    optionsHtml += `<option value="${prod.product_id}" data-price="${prod.price}" data-name="${prod.name.toLowerCase()}" ${isSel}>${prod.name} (${Number(prod.price).toLocaleString('vi-VN')}đ)</option>`;
                });

                newRow.innerHTML = `
                    <div class="flex-1 flex items-center gap-2">
                        <input type="text" placeholder="🔎 Lọc tên món..." class="row-filter-input w-36 px-2.5 py-1.5 border rounded-lg text-xs bg-white focus:outline-none focus:border-[#e8634a]">
                        <select name="items[${itemIndex}][product_id]" class="product-select flex-1 px-3 py-2 border rounded-lg text-sm bg-white focus:outline-none focus:border-[#e8634a]" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 font-semibold">Số lượng:</span>
                            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="item-qty w-20 px-3 py-2 border rounded-lg text-sm text-center font-bold focus:outline-none focus:border-[#e8634a]" required>
                        </div>
                        <button type="button" class="remove-row-btn p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Xóa món này">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                itemIndex++;
                updateOriginalPrice();
            }

            // --- LỌC MÓN TRONG TỪNG DÒNG SELECT ---
            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('row-filter-input')) {
                    const kw = e.target.value.trim().toLowerCase();
                    const select = e.target.closest('.combo-item-row').querySelector('.product-select');
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) return;
                        const name = opt.getAttribute('data-name') || '';
                        if (name.includes(kw)) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                }
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row-btn')) {
                    const rows = container.querySelectorAll('.combo-item-row');
                    if (rows.length > 1) {
                        e.target.closest('.combo-item-row').remove();
                        updateOriginalPrice();
                    } else {
                        alert('Combo phải chứa ít nhất 1 sản phẩm.');
                    }
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select') || e.target.classList.contains('item-qty')) {
                    updateOriginalPrice();
                }
            });

            function updateOriginalPrice() {
                let total = 0;
                const rows = container.querySelectorAll('.combo-item-row');
                rows.forEach(row => {
                    const select = row.querySelector('.product-select');
                    const qtyInput = row.querySelector('.item-qty');
                    if (select && select.selectedIndex > 0) {
                        const option = select.options[select.selectedIndex];
                        const price = parseFloat(option.getAttribute('data-price')) || 0;
                        const qty = parseInt(qtyInput.value) || 1;
                        total += price * qty;
                    }
                });
                const origInput = document.getElementById('original_price_input');
                if (origInput) {
                    origInput.value = total > 0 ? total : '';
                }
            }
        });
    </script>
@endsection
