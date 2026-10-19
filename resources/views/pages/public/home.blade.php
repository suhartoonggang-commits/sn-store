@extends('layouts.public')

@section('title', 'SN Store — Belanja Elektronik Pilihan')
@section('description', 'Belanja gadget dan elektronik pilihan di SN Store. Temukan produk terbaru, harga spesial, dan pengalaman belanja yang nyaman.')

@section('content')
    @php
        $products = [
            ['name' => 'Sony Alpha A6400', 'category' => 'Kamera', 'price' => 'Rp 12.499.000', 'old_price' => 'Rp 13.999.000', 'rating' => '4.9', 'badge' => 'Terlaris', 'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Headphone Studio Pro', 'category' => 'Audio', 'price' => 'Rp 2.899.000', 'old_price' => 'Rp 3.499.000', 'rating' => '4.8', 'badge' => 'Hemat 17%', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Smartphone Nova X', 'category' => 'Smartphone', 'price' => 'Rp 8.799.000', 'old_price' => 'Rp 9.499.000', 'rating' => '4.9', 'badge' => 'Baru', 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Laptop Air 14', 'category' => 'Laptop', 'price' => 'Rp 14.999.000', 'old_price' => 'Rp 16.499.000', 'rating' => '4.9', 'badge' => 'Favorit', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Smartwatch Active 5', 'category' => 'Wearable', 'price' => 'Rp 3.249.000', 'old_price' => 'Rp 3.799.000', 'rating' => '4.7', 'badge' => 'Hemat 14%', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Speaker Bluetooth Boom', 'category' => 'Audio', 'price' => 'Rp 1.599.000', 'old_price' => 'Rp 1.899.000', 'rating' => '4.8', 'badge' => 'Terlaris', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Mechanical Keyboard K87', 'category' => 'Aksesori', 'price' => 'Rp 1.249.000', 'old_price' => 'Rp 1.499.000', 'rating' => '4.8', 'badge' => 'Pilihan', 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Tablet Note 11', 'category' => 'Tablet', 'price' => 'Rp 6.299.000', 'old_price' => 'Rp 6.999.000', 'rating' => '4.7', 'badge' => 'Baru', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Wireless Earbuds Air', 'category' => 'Audio', 'price' => 'Rp 1.899.000', 'old_price' => 'Rp 2.299.000', 'rating' => '4.8', 'badge' => 'Hemat 17%', 'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Konsol Game Next', 'category' => 'Gaming', 'price' => 'Rp 7.499.000', 'old_price' => 'Rp 7.999.000', 'rating' => '4.9', 'badge' => 'Terlaris', 'image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?auto=format&fit=crop&w=900&q=85'],
        ];
    @endphp

    <section
        class="mx-auto max-w-7xl px-4 pb-12 pt-6 sm:px-6 sm:pt-8 lg:px-8 lg:pb-16"
        x-data="{
            activeSlide: 0,
            slides: [
                {
                    eyebrow: 'TEKNOLOGI UNTUK HARI INI',
                    title: 'Upgrade harimu, mulai dari sini.',
                    description: 'Temukan perangkat pilihan yang siap menemani setiap ide dan petualanganmu.',
                    cta: 'Jelajahi produk',
                    image: 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=2000&q=90'
                },
                {
                    eyebrow: 'TEMUKAN SUARA FAVORITMU',
                    title: 'Biar setiap momen terdengar lebih seru.',
                    description: 'Headphone dan speaker pilihan untuk fokus, santai, dan semua di antaranya.',
                    cta: 'Lihat koleksi audio',
                    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=2000&q=90'
                },
                {
                    eyebrow: 'KERJA & KREASI TANPA BATAS',
                    title: 'Performa hebat untuk ide yang besar.',
                    description: 'Laptop, tablet, dan aksesori yang bikin produktivitas terasa lebih ringan.',
                    cta: 'Cari perangkatmu',
                    image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=2000&q=90'
                }
            ]
        }"
        x-init="setInterval(() => activeSlide = (activeSlide + 1) % slides.length, 6500)"
    >
        <div class="relative isolate min-h-[440px] overflow-hidden rounded-[2rem] bg-slate-950 sm:min-h-[500px] lg:min-h-[540px]" aria-label="Promo pilihan SN Store">
            <template x-for="(slide, index) in slides" :key="slide.eyebrow">
                <article
                    x-show="activeSlide === index"
                    x-transition.opacity.duration.700ms
                    :aria-hidden="activeSlide !== index"
                    class="absolute inset-0 flex items-center bg-cover bg-center"
                    :style="'background-image: linear-gradient(90deg, rgba(15, 16, 21, .94) 0%, rgba(15, 16, 21, .78) 45%, rgba(15, 16, 21, .16) 100%), url(' + slide.image + ')'"
                >
                    <div class="max-w-2xl px-7 py-16 sm:px-12 lg:px-16">
                        <p x-text="slide.eyebrow" class="text-xs font-extrabold tracking-[0.22em] text-rose-300 sm:text-sm"></p>
                        <h1 x-text="slide.title" class="mt-5 max-w-xl text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl"></h1>
                        <p x-text="slide.description" class="mt-5 max-w-lg text-sm leading-7 text-slate-200 sm:text-base"></p>
                        <a href="#produk-terbaru" class="mt-8 inline-flex items-center gap-3 rounded-full bg-rose-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-rose-950/30 transition hover:-translate-y-0.5 hover:bg-rose-500">
                            <span x-text="slide.cta"></span>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                        </a>
                    </div>
                </article>
            </template>

            <div class="absolute bottom-6 left-7 z-10 flex items-center gap-2.5 sm:left-12 lg:left-16">
                <template x-for="(slide, index) in slides" :key="slide.eyebrow + '-dot'">
                    <button
                        type="button"
                        @click="activeSlide = index"
                        :aria-label="'Tampilkan slide ' + (index + 1)"
                        :aria-current="activeSlide === index ? 'true' : 'false'"
                        :class="activeSlide === index ? 'w-8 bg-white' : 'w-2.5 bg-white/50 hover:bg-white/80'"
                        class="h-2.5 rounded-full transition-all"
                    ></button>
                </template>
                <div class="ml-3 flex gap-2">
                    <button type="button" @click="activeSlide = (activeSlide + slides.length - 1) % slides.length" aria-label="Slide sebelumnya" class="grid size-9 place-items-center rounded-full border border-white/30 text-white transition hover:bg-white/15">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                    </button>
                    <button type="button" @click="activeSlide = (activeSlide + 1) % slides.length" aria-label="Slide berikutnya" class="grid size-9 place-items-center rounded-full border border-white/30 text-white transition hover:bg-white/15">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>
            </div>

            <div class="absolute right-7 top-7 hidden rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold text-white backdrop-blur sm:block">
                <span class="mr-2 inline-block size-2 rounded-full bg-rose-400"></span>
                Pilihan terbaik, harga bersahabat
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8" aria-label="Keunggulan SN Store">
        <div class="grid gap-5 rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v10H3zM14 10h4l3 3v4h-7z" /><circle cx="7.5" cy="18" r="1.5" /><circle cx="18" cy="18" r="1.5" /></svg></span>
                <div><p class="text-sm font-extrabold">Gratis ongkir</p><p class="mt-0.5 text-xs text-slate-500">Untuk pesanan pilihan</p></div>
            </div>
            <div class="flex items-center gap-3">
                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8 3v5c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" /></svg></span>
                <div><p class="text-sm font-extrabold">Produk original</p><p class="mt-0.5 text-xs text-slate-500">Garansi resmi terjamin</p></div>
            </div>
            <div class="flex items-center gap-3">
                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16v11H4zM8 8V5h8v3M4 12h16" /></svg></span>
                <div><p class="text-sm font-extrabold">Pembayaran aman</p><p class="mt-0.5 text-xs text-slate-500">Transaksi mudah dan nyaman</p></div>
            </div>
            <div class="flex items-center gap-3">
                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13v-2a8 8 0 0 1 16 0v2M4 13h3v6H6a2 2 0 0 1-2-2v-4Zm16 0h-3v6h1a2 2 0 0 0 2-2v-4Z" /></svg></span>
                <div><p class="text-sm font-extrabold">Siap membantu</p><p class="mt-0.5 text-xs text-slate-500">Tim kami selalu ada untukmu</p></div>
            </div>
        </div>
    </section>

    <section id="produk-terbaru" class="mx-auto max-w-7xl scroll-mt-28 px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold tracking-[0.2em] text-rose-700">PILIHAN UNTUKMU</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Produk terbaru</h2>
                <p class="mt-2 text-sm text-slate-500 sm:text-base">Upgrade gadget-mu dengan koleksi favorit minggu ini.</p>
            </div>
            <a href="#produk-terbaru" class="inline-flex w-fit items-center gap-2 text-sm font-bold text-rose-700 transition hover:gap-3 hover:text-rose-800">
                Lihat semua produk
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($products as $product)
                <article
                    data-product-card
                    data-search="{{ strtolower($product['name'].' '.$product['category']) }}"
                    x-show="!searchQuery || $el.dataset.search.includes(searchQuery.toLowerCase())"
                    class="group overflow-hidden rounded-2xl border border-slate-100 bg-white transition duration-300 hover:-translate-y-1 hover:border-rose-100 hover:shadow-xl hover:shadow-slate-900/5"
                >
                    <div class="relative aspect-square overflow-hidden bg-slate-100">
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-extrabold text-rose-700 shadow-sm sm:text-xs">{{ $product['badge'] }}</span>
                        <span class="absolute bottom-3 right-3 rounded-full bg-slate-950/75 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur sm:text-xs">{{ $product['category'] }}</span>
                    </div>
                    <div class="p-3.5 sm:p-4">
                        <div class="flex items-center gap-1 text-amber-400" aria-label="Rating {{ $product['rating'] }} dari 5">
                            <svg class="size-3.5 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="m10 1.7 2.55 5.16 5.7.83-4.12 4.02.97 5.68L10 14.71l-5.1 2.68.97-5.68L1.75 7.69l5.7-.83L10 1.7Z" /></svg>
                            <span class="text-xs font-bold text-slate-600">{{ $product['rating'] }}</span>
                        </div>
                        <h3 class="mt-2 min-h-10 text-sm font-bold leading-5 text-slate-800 sm:text-base">{{ $product['name'] }}</h3>
                        <p class="mt-2 text-sm font-extrabold text-slate-950">{{ $product['price'] }}</p>
                        <p class="mt-0.5 text-xs text-slate-400 line-through">{{ $product['old_price'] }}</p>
                        <button type="button" @click="cartCount++" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-3 py-2.5 text-xs font-extrabold text-rose-700 transition hover:border-rose-700 hover:bg-rose-700 hover:text-white sm:text-sm">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.1a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                            Tambah ke keranjang
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="relative overflow-hidden rounded-[2rem] bg-rose-700 px-7 py-10 sm:px-12 sm:py-14 lg:px-16">
            <div class="absolute -right-16 -top-24 size-72 rounded-full border-[36px] border-white/10"></div>
            <div class="absolute -bottom-36 right-40 size-72 rounded-full bg-rose-600/70"></div>
            <div class="relative flex flex-col gap-7 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl">
                    <p class="text-xs font-extrabold tracking-[0.2em] text-rose-200">WAKTUNYA UPGRADE</p>
                    <h2 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">Teknologi hebat, harga makin dekat.</h2>
                    <p class="mt-3 text-sm leading-6 text-rose-100 sm:text-base">Jelajahi koleksi terbaru dan temukan perangkat yang pas untuk keseharianmu.</p>
                </div>
                <a href="#produk-terbaru" class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-extrabold text-rose-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-rose-50">
                    Mulai belanja
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                </a>
            </div>
        </div>
    </section>
@endsection
