@extends('layouts.app')

@section('title', $post->title . ' - Chill Chill Blog')

@section('content')
<div class="bg-[#FAF7F2] min-h-screen pb-24">

    {{-- HERO BANNER ARTICLE --}}
    <section class="pt-24 pb-12 bg-gradient-to-b from-[#FFF0D4]/60 to-[#FAF7F2] relative">
        <div class="max-w-4xl mx-auto px-6">
            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('post.index') }}" class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-espresso/60 hover:text-coral transition-colors">
                    ← Quay lại Blog
                </a>
                <span class="text-espresso/30">•</span>
                <span class="text-xs font-extrabold uppercase tracking-widest text-coral bg-coral/10 px-3 py-1 rounded-full border border-coral/20">
                    {{ $post->category_name ?? 'Blog' }}
                </span>
            </div>

            <h1 class="font-serif font-black text-3xl md:text-5xl text-espresso mb-6 leading-snug">
                {{ $post->title }}
            </h1>

            <div class="flex items-center gap-4 text-xs font-medium text-espresso/60 border-t border-espresso/10 pt-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-coral text-white font-bold flex items-center justify-center text-sm shadow-xs">
                        {{ strtoupper(substr($post->author_name ?? 'C', 0, 1)) }}
                    </div>
                    <span>Tác giả: <strong class="text-espresso">{{ $post->author_name ?? 'Quản trị viên' }}</strong></span>
                </div>
                <span>•</span>
                <span>Ngày đăng: {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y - H:i') }}</span>
            </div>
        </div>
    </section>

    {{-- NỘI DUNG CHÍNH --}}
    <section class="max-w-4xl mx-auto px-6 py-4">
        {{-- Ảnh bìa đại diện --}}
        @if($post->thumbnail)
            <div class="w-full aspect-[16/9] rounded-[32px] overflow-hidden shadow-lg border border-espresso/5 mb-12">
                <img src="{{ $post->thumbnail }}" class="w-full h-full object-cover" alt="{{ $post->title }}">
            </div>
        @endif

        {{-- Nội dung bài viết --}}
        <div class="bg-white rounded-[32px] p-8 md:p-14 shadow-sm border border-espresso/5 prose prose-lg max-w-none text-espresso leading-relaxed space-y-6">
            {!! nl2br(e($post->content)) !!}
        </div>

        {{-- Nút chia sẻ / Quay lại --}}
        <div class="mt-10 flex justify-between items-center bg-[#FFF9ED] p-6 rounded-2xl border border-espresso/5">
            <a href="{{ route('post.index') }}" class="px-6 py-2.5 bg-espresso text-white rounded-full font-bold text-xs uppercase tracking-wider hover:bg-coral transition-colors shadow-xs">
                ← Tất cả bài viết
            </a>
            <span class="text-xs text-espresso/60 font-medium">Cảm ơn bạn đã đọc bài viết này! ☕</span>
        </div>
    </section>

    {{-- BÀI VIẾT LIÊN QUAN --}}
    @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 pt-16">
            <h3 class="font-serif font-bold text-2xl text-espresso mb-8 flex items-center gap-2">
                <span>📚 Bài viết mới khác</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedPosts as $rp)
                    <article class="bg-[#FFF9ED] rounded-[24px] overflow-hidden group shadow-xs hover:shadow-md transition-all border border-espresso/5 flex flex-col">
                        <a href="{{ route('post.show', $rp->slug) }}" class="aspect-[16/10] overflow-hidden block">
                            <img src="{{ $rp->thumbnail ?: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600&auto=format&fit=crop' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </a>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-coral uppercase tracking-widest block mb-2">{{ $rp->category_name ?? 'Blog' }}</span>
                                <h4 class="font-serif font-bold text-base text-espresso group-hover:text-coral transition-colors line-clamp-2 mb-2">
                                    <a href="{{ route('post.show', $rp->slug) }}">{{ $rp->title }}</a>
                                </h4>
                            </div>
                            <span class="text-[11px] text-espresso/40 block mt-4">{{ \Carbon\Carbon::parse($rp->created_at)->format('d/m/Y') }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
