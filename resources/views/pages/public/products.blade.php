@extends('layouts.public')

@section('title', 'Semua Produk — SN Store')
@section('description', 'Cari dan jelajahi produk elektronik pilihan di SN Store.')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="mb-8 border-b border-slate-200 pb-6">
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-rose-700">KATALOG SN STORE</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-950 sm:text-4xl">Semua produk</h1>
            <p class="mt-2 text-sm text-slate-500">Temukan perangkat yang pas untuk kebutuhanmu.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-10">
            <aside class="h-fit border-b border-slate-200 pb-6 lg:sticky lg:top-32 lg:border-b-0 lg:pb-0" aria-label="Filter produk">
                <form action="{{ route('products.index') }}" method="GET" role="search">
                    <label for="catalog-search" class="text-sm font-extrabold text-slate-900">Cari produk</label>
                    <div class="mt-3 flex gap-2">
                        <input id="catalog-search" type="search" name="q" value="{{ $searchQuery }}" placeholder="Nama produk..." class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                        <button type="submit" aria-label="Cari produk" class="grid size-10 shrink-0 place-items-center rounded-lg bg-rose-700 text-white transition hover:bg-rose-800">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m16 16 4 4" /></svg>
                        </button>
                    </div>
                    @if ($selectedCategory)
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif
                </form>

                <div class="mt-7">
                    <h2 class="text-sm font-extrabold text-slate-900">Kategori</h2>
                    <nav class="mt-3 flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:gap-1 lg:overflow-visible lg:pb-0" aria-label="Filter kategori">
                        <a href="{{ route('products.index', ['q' => $searchQuery ?: null]) }}" @class([
                            'flex shrink-0 items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition',
                            'bg-rose-50 font-bold text-rose-800' => ! $selectedCategory,
                            'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => $selectedCategory,
                        ])>
                            <span>Semua kategori</span>
                            <span class="text-xs text-slate-400">{{ $navigationCategories->sum('products_count') }}</span>
                        </a>
                        @foreach ($navigationCategories as $category)
                            <a href="{{ route('products.index', ['category' => $category->slug, 'q' => $searchQuery ?: null]) }}" @class([
                                'flex shrink-0 items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition',
                                'bg-rose-50 font-bold text-rose-800' => $selectedCategory === $category->slug,
                                'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => $selectedCategory !== $category->slug,
                            ])>
                                <span>{{ $category->name }}</span>
                                <span class="text-xs text-slate-400">{{ $category->products_count }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            <div class="min-w-0">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        <span class="font-bold text-slate-900">{{ $products->total() }}</span>
                        produk ditemukan
                    </p>
                    @if ($searchQuery || $selectedCategory)
                        <a href="{{ route('products.index') }}" class="text-sm font-bold text-rose-700 hover:text-rose-800">Hapus filter</a>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 xl:grid-cols-4">
                    @forelse ($products as $product)
                        <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-rose-200 hover:shadow-lg hover:shadow-slate-900/5">
                            <div class="relative aspect-square overflow-hidden bg-slate-100">
                                @if ($product->image_path)
                                    <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="grid size-full place-items-center text-center text-xs font-semibold text-slate-400">Gambar belum tersedia</div>
                                @endif
                                @if ($product->category)
                                    <span class="absolute bottom-3 left-3 max-w-[calc(100%-1.5rem)] truncate rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-bold text-slate-700 shadow-sm">{{ $product->category->name }}</span>
                                @endif
                            </div>
                            <div class="p-3.5 sm:p-4">
                                <h2 class="min-h-10 text-sm font-bold leading-5 text-slate-800 sm:text-base">{{ $product->name }}</h2>
                                <p class="mt-2 text-sm font-extrabold text-slate-950">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs text-slate-500">Stok: {{ $product->stock }}</p>
                                @if ($product->stock > 0)
                                    @auth
                                        <form method="POST" action="{{ route('cart.items.store', $product) }}" class="mt-4">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" aria-label="Tambah {{ $product->name }} ke keranjang" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rose-700 px-3 py-2.5 text-xs font-bold text-white transition hover:bg-rose-800 sm:text-sm">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.1a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                                                Tambah ke keranjang
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rose-700 px-3 py-2.5 text-xs font-bold text-white transition hover:bg-rose-800 sm:text-sm">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.1a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                                            Login untuk membeli
                                        </a>
                                    @endauth
                                @else
                                    <button type="button" disabled class="mt-4 inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-slate-200 px-3 py-2.5 text-xs font-bold text-slate-500 sm:text-sm">Stok habis</button>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="col-span-full py-16 text-center text-sm text-slate-500">Tidak ada produk yang cocok dengan filter ini.</p>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection