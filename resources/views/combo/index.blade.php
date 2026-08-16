@extends('layouts.app')

@section('content')
    {{-- Banner Trang Combo --}}
    <section class="py-16 bg-cover bg-center bg-no-repeat text-white relative overflow-hidden" style="background-image: url('{{ asset('images/anhnen.png') }}');">
        {{-- Overlay lớp phủ mờ tối giúp chữ hiển thị sắc nét --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[1px]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <span class="inline-block bg-white/20 backdrop-blur-md text-cream text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider border border-white/20 shadow-md">
                    🎁 Ưu Đãi Độc Quyền Chill Chill
                </span>
                <h1 class="text-4xl sm:text-5xl font-serif font-black tracking-tight leading-tight drop-shadow-md">
                    GÓI COMBO TIẾT KIỆM – UỐNG LÀ MÊ!
                </h1>
                <p class="text-base sm:text-lg text-white/90 font-medium drop-shadow">
                    Thưởng thức combo thức uống & bánh ngọt được kết hợp hoàn hảo với mức giá ưu đãi cực sốc lên tới <strong class="text-amber-300 font-bold underline">30%</strong>.
                </p>
            </div>
        </div>
    </section>

    {{-- Style cho thanh cuộn trong card combo --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.03); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ff7043; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d5523b; }
    </style>

    {{-- Danh Sách Combo --}}
    <section class="py-16 bg-[#FAF7F2]/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($combos as $combo)
                    <div class="reveal-up hover-lift bg-white rounded-3xl p-6 border border-coral/20 shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group h-full">
                        @if($combo->original_price > $combo->price)
                            @php
                                $percent = round((($combo->original_price - $combo->price) / $combo->original_price) * 100);
                            @endphp
                            <div class="absolute top-4 left-4 z-10 bg-gradient-to-r from-red-500 to-coral text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-1.348l-8 8a1 1 0 00-.246.979 1 1 0 00.706.707l8 8a1 1 0 001.45-1.348L5.592 11h11.816a1 1 0 00.992-1.127l-.4-3.2A1 1 0 0017 6H8.223l4.172-3.447z" clip-rule="evenodd"/></svg>
                                GIẢM {{ $percent }}%
                            </div>
                        @endif

                        <div>
                            <a href="{{ route('combo.show', $combo->combo_id) }}" class="block relative overflow-hidden rounded-2xl mb-5 h-56 bg-cream">
                                <img src="{{ asset($combo->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop') }}"
                                     alt="{{ $combo->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            <a href="{{ route('combo.show', $combo->combo_id) }}" class="block font-bold text-espresso text-2xl hover:text-coral transition-colors">
                                {{ $combo->name }}
                            </a>
                            <p class="text-xs text-espresso/60 mt-1 line-clamp-2 min-h-[32px]">{{ $combo->description ?? 'Gói kết hợp ưu đãi tiết kiệm dành riêng cho bạn.' }}</p>

                            {{-- Món bao gồm trong Combo --}}
                            <div class="mt-4 pt-3 border-t border-espresso/10">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso/50 block mb-2">Các món có trong Combo:</span>
                                <ul class="space-y-1.5 max-h-40 overflow-y-auto pr-1 custom-scrollbar">
                                    @foreach($combo->products as $prod)
                                        <li class="flex items-center justify-between text-xs text-espresso bg-cream/50 px-3 py-1.5 rounded-xl border border-coral/10">
                                            <div class="flex items-center gap-2 font-medium">
                                                <svg class="w-3.5 h-3.5 text-coral shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="line-clamp-1">{{ $prod->name }}</span>
                                            </div>
                                            <span class="font-bold text-coral bg-white px-2 py-0.5 rounded-md border border-coral/20 shrink-0">x{{ $prod->pivot->quantity }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-5 mt-6 border-t border-espresso/10">
                            <div>
                                @if($combo->original_price > $combo->price)
                                    <span class="text-xs text-espresso/40 line-through block font-medium">
                                        {{ number_format($combo->original_price, 0, ',', '.') }}đ
                                    </span>
                                @endif
                                <span class="text-2xl font-black text-coral">
                                    {{ number_format($combo->price, 0, ',', '.') }}đ
                                </span>
                            </div>

                            <button onclick="addComboToCart({{ $combo->combo_id }})"
                                    class="px-6 py-3.5 rounded-full bg-coral text-white font-bold text-xs hover:bg-[#d5523b] shadow-lg shadow-coral/30 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <span>Đặt Combo Ngay</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-white rounded-3xl border border-espresso/10">
                        <p class="text-espresso/60 text-base">Hiện chưa có gói Combo nào. Vui lòng quay lại sau!</p>
                    </div>
                @endforelse
            </div>

            @if($combos->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $combos->links() }}
                </div>
            @endif
        </div>
    </section>

    <script>
        function addComboToCart(comboId) {
            fetch("{{ route('cart.addCombo') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    combo_id: comboId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartBadges = document.querySelectorAll('.cart-count-badge, #cart-count');
                    cartBadges.forEach(b => b.textContent = data.cart_count);
                    alert("🎉 " + data.message);
                } else {
                    alert("⚠️ " + (data.message || "Có lỗi xảy ra khi thêm combo vào giỏ hàng."));
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("⚠️ Không thể kết nối với máy chủ!");
            });
        }
    </script>
@endsection
