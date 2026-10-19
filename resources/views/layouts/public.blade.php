<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="@yield('description', 'Temukan gadget dan elektronik pilihan dengan harga terbaik di SN Store.')">

        <title>@yield('title', config('app.name', 'SN Store'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans antialiased text-slate-900"
        x-data="{ searchQuery: '', cartCount: 0, cartOpen: false, mobileMenuOpen: false }"
    >
        <div class="bg-rose-800 px-4 py-2 text-center text-xs font-semibold tracking-wide text-white sm:text-sm">
            <span class="hidden sm:inline">PENGIRIMAN GRATIS</span>
            <span class="sm:hidden">GRATIS ONGKIR</span>
            <span class="mx-1 text-rose-200">·</span>
            untuk pembelian pertama dengan kode
            <span class="ml-1 rounded bg-white/15 px-2 py-0.5 font-extrabold tracking-widest">SNHEMAT</span>
        </div>

        <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="SN Store, beranda">
                    <span class="grid size-10 place-items-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white shadow-lg shadow-rose-700/20">SN</span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></span>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 lg:flex" aria-label="Navigasi utama">
                    <a href="{{ route('home') }}" class="text-rose-700 transition hover:text-rose-800">Beranda</a>
                    <a href="{{ route('home') }}#produk-terbaru" class="transition hover:text-rose-700">Produk</a>
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none items-center gap-1 transition hover:text-rose-700">
                            Kategori
                            <svg class="size-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                        </summary>
                        <div class="absolute left-0 top-full mt-3 w-52 rounded-2xl border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10">
                            <a href="{{ route('home') }}#produk-terbaru" class="block rounded-xl px-3 py-2.5 transition hover:bg-rose-50 hover:text-rose-700">Audio &amp; Headphone</a>
                            <a href="{{ route('home') }}#produk-terbaru" class="block rounded-xl px-3 py-2.5 transition hover:bg-rose-50 hover:text-rose-700">Smartphone &amp; Tablet</a>
                            <a href="{{ route('home') }}#produk-terbaru" class="block rounded-xl px-3 py-2.5 transition hover:bg-rose-50 hover:text-rose-700">Komputer &amp; Aksesori</a>
                            <a href="{{ route('home') }}#produk-terbaru" class="block rounded-xl px-3 py-2.5 transition hover:bg-rose-50 hover:text-rose-700">Kamera &amp; Wearable</a>
                        </div>
                    </details>
                </nav>

                <form action="{{ route('home') }}" method="GET" @submit.prevent role="search" class="hidden min-w-48 max-w-xs flex-1 items-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 transition focus-within:border-rose-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-rose-100 md:flex lg:max-w-sm">
                    <label for="desktop-search" class="sr-only">Cari produk</label>
                    <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m16 16 4 4" /></svg>
                    <input id="desktop-search" type="search" name="q" x-model.debounce.200ms="searchQuery" placeholder="Cari gadget impianmu..." class="w-full border-0 bg-transparent py-0 pl-2 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-0">
                    <button type="submit" class="sr-only">Cari</button>
                </form>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="hidden rounded-full px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:inline-flex">Akun saya</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-rose-200 hover:text-rose-700 sm:inline-flex">Masuk</a>
                    @endauth

                    <div class="relative">
                        <button type="button" @click="cartOpen = !cartOpen" :aria-expanded="cartOpen.toString()" aria-label="Buka keranjang belanja" class="relative grid size-10 place-items-center rounded-full bg-slate-100 text-slate-800 transition hover:bg-rose-50 hover:text-rose-700">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.1a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                            <span x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1 -top-1 grid size-5 place-items-center rounded-full bg-rose-700 text-[10px] font-bold text-white"></span>
                        </button>
                        <div x-show="cartOpen" @click.outside="cartOpen = false" x-transition class="absolute right-0 top-full mt-3 w-64 rounded-2xl border border-slate-100 bg-white p-5 shadow-xl shadow-slate-900/10">
                            <p class="font-bold text-slate-900">Keranjang belanja</p>
                            <p x-show="cartCount === 0" class="mt-1 text-sm text-slate-500">Keranjangmu masih kosong. Yuk, temukan gadget favoritmu!</p>
                            <p x-show="cartCount > 0" class="mt-1 text-sm text-slate-500"><span x-text="cartCount"></span> produk siap menemani harimu.</p>
                            <a href="{{ route('home') }}#produk-terbaru" @click="cartOpen = false" class="mt-4 inline-flex text-sm font-bold text-rose-700 hover:text-rose-800">Lanjut belanja <span aria-hidden="true" class="ml-1">→</span></a>
                        </div>
                    </div>

                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Buka menu navigasi" class="grid size-10 place-items-center rounded-full text-slate-700 transition hover:bg-slate-100 lg:hidden">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    </button>
                </div>
            </div>

            <form action="{{ route('home') }}" method="GET" @submit.prevent role="search" class="mx-auto max-w-7xl px-4 pb-3 sm:px-6 md:hidden">
                <label for="mobile-search" class="sr-only">Cari produk</label>
                <div class="flex items-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 focus-within:border-rose-300 focus-within:ring-4 focus-within:ring-rose-100">
                    <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m16 16 4 4" /></svg>
                    <input id="mobile-search" type="search" name="q" x-model.debounce.200ms="searchQuery" placeholder="Cari gadget impianmu..." class="w-full border-0 bg-transparent py-0 pl-2 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-0">
                    <button type="submit" class="sr-only">Cari</button>
                </div>
            </form>

            <nav x-show="mobileMenuOpen" x-transition class="border-t border-slate-100 bg-white px-4 py-3 lg:hidden" aria-label="Navigasi seluler">
                <div class="mx-auto flex max-w-7xl flex-col gap-1 text-sm font-semibold text-slate-700">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="rounded-xl px-3 py-2.5 text-rose-700 hover:bg-rose-50">Beranda</a>
                    <a href="{{ route('home') }}#produk-terbaru" @click="mobileMenuOpen = false" class="rounded-xl px-3 py-2.5 hover:bg-slate-50">Produk terbaru</a>
                    <a href="{{ route('home') }}#produk-terbaru" @click="mobileMenuOpen = false" class="rounded-xl px-3 py-2.5 hover:bg-slate-50">Kategori produk</a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-50">Masuk ke akun</a>
                    @endguest
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="bg-slate-950 text-slate-300">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
                <div class="lg:col-span-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5" aria-label="SN Store, beranda">
                        <span class="grid size-10 place-items-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white">SN</span>
                        <span class="text-lg font-extrabold tracking-tight text-white">sn<span class="text-rose-400">store</span></span>
                    </a>
                    <p class="mt-5 max-w-md text-sm leading-7 text-slate-400">
                        Temukan teknologi yang bikin hidup lebih mudah. SN Store menghadirkan pilihan gadget dan elektronik berkualitas, harga bersahabat, serta pengalaman belanja yang nyaman.
                    </p>
                    <div class="mt-5 flex items-center gap-2 text-sm text-slate-400">
                        <svg class="size-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                        <span>Melayani seluruh Indonesia</span>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-extrabold text-white">Jelajahi</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                        <li><a href="{{ route('home') }}#produk-terbaru" class="transition hover:text-white">Produk terbaru</a></li>
                        <li><a href="{{ route('home') }}#tentang-kami" class="transition hover:text-white">Tentang kami</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-white">Masuk / Daftar</a></li>
                    </ul>
                </div>

                <div id="tentang-kami">
                    <h2 class="text-sm font-extrabold text-white">Hubungi kami</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-400">
                        <li class="flex items-center gap-2.5"><svg class="size-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" /></svg><a href="mailto:halo@snstore.id" class="transition hover:text-white">halo@snstore.id</a></li>
                        <li class="flex items-center gap-2.5"><svg class="size-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h3l2 5-2 1.5a15 15 0 0 0 5.5 5.5L15 14l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" /></svg><a href="tel:+62215550123" class="transition hover:text-white">+62 21 5550 123</a></li>
                        <li class="text-slate-500">Senin–Sabtu, 09.00–18.00 WIB</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                    <p>&copy; {{ date('Y') }} SN Store. Semua hak dilindungi.</p>
                    <p>Belanja cerdas, hidup lebih praktis.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
