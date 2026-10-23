<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="@yield('description', 'Panel administrasi SN Store.')">

        <title>@yield('title', 'Admin — SN Store')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body class="bg-slate-50 font-sans antialiased text-slate-900" x-data="{ sidebarOpen: false }">
        <button
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            aria-label="Tutup navigasi"
        ></button>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200/80 bg-white transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            aria-label="Navigasi admin"
        >
            <div class="flex h-20 items-center justify-between border-b border-slate-100 px-6">
                <a href="{{ auth()->user()->can('dashboard.view') ? route('dashboard') : route('home') }}" class="flex items-center gap-3" aria-label="SN Store Admin, dashboard">
                    <span class="grid size-11 place-items-center rounded-2xl bg-rose-700 text-sm font-extrabold tracking-tight text-white shadow-lg shadow-rose-700/20">SN</span>
                    <span>
                        <span class="block text-base font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></span>
                        <span class="block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Admin panel</span>
                    </span>
                </a>
                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 lg:hidden"
                    aria-label="Tutup navigasi"
                >
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-7" aria-label="Menu utama">
                <p class="px-3 pb-3 text-[10px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Menu utama</p>

                @can('dashboard.view')
                    <a
                        href="{{ route('dashboard') }}"
                        @click="sidebarOpen = false"
                        @if (request()->routeIs('dashboard')) aria-current="page" @endif
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition',
                            'bg-rose-50 font-bold text-rose-800 ring-1 ring-inset ring-rose-100' => request()->routeIs('dashboard'),
                            'font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs('dashboard'),
                        ])
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" /><rect x="13.5" y="3.5" width="7" height="5" rx="1.5" /><rect x="13.5" y="12.5" width="7" height="8" rx="1.5" /><rect x="3.5" y="14.5" width="7" height="6" rx="1.5" /></svg>
                        <span class="flex-1">Dashboard</span>
                        @if (request()->routeIs('dashboard'))
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                        @endif
                    </a>
                @endcan

                @can('categories.view')
                    <a
                        href="{{ route('admin.categories.index') }}"
                        @click="sidebarOpen = false"
                        @if (request()->routeIs('admin.categories.*')) aria-current="page" @endif
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition',
                            'bg-rose-50 font-bold text-rose-800 ring-1 ring-inset ring-rose-100' => request()->routeIs('admin.categories.*'),
                            'font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs('admin.categories.*'),
                        ])
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
                        <span class="flex-1">Kategori</span>
                        @if (request()->routeIs('admin.categories.*'))
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                        @endif
                    </a>
                @endcan

                @can('products.view')
                    <a href="{{ route('admin.products.index') }}" @click="sidebarOpen = false" @if (request()->routeIs('admin.products.*')) aria-current="page" @endif @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition',
                        'bg-rose-50 font-bold text-rose-800 ring-1 ring-inset ring-rose-100' => request()->routeIs('admin.products.*'),
                        'font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs('admin.products.*'),
                    ])>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5 12 4l8 4.5v9L12 22l-8-4.5v-9Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m4.3 8.7 7.7 4.4 7.7-4.4M12 13.1V22" /></svg>
                        <span class="flex-1">Produk</span>
                        @if (request()->routeIs('admin.products.*'))
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                        @endif
                    </a>
                @endcan

                @can('transactions.view')
                    <a href="{{ route('admin.transactions.index') }}" @click="sidebarOpen = false" @if (request()->routeIs('admin.transactions.*')) aria-current="page" @endif @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition',
                        'bg-rose-50 font-bold text-rose-800 ring-1 ring-inset ring-rose-100' => request()->routeIs('admin.transactions.*'),
                        'font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs('admin.transactions.*'),
                    ])>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14a1 1 0 0 1 1 1v15l-3-2-3 2-3 2-3 2-3-2-2 2V5a1 1 0 0 1 1-1Z" /><path stroke-linecap="round" d="M8 9h8M8 13h5" /></svg>
                        <span class="flex-1">Transaksi</span>
                        @if (request()->routeIs('admin.transactions.*'))
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                        @endif
                    </a>
                @endcan

                @can('users.view')
                    <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" @if (request()->routeIs('admin.users.*')) aria-current="page" @endif @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition',
                        'bg-rose-50 font-bold text-rose-800 ring-1 ring-inset ring-rose-100' => request()->routeIs('admin.users.*'),
                        'font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs('admin.users.*'),
                    ])>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.8 20a6.2 6.2 0 0 1 12.4 0M16 5a3.5 3.5 0 0 1 0 6.8M17.5 14a5.5 5.5 0 0 1 3.7 5.2" /></svg>
                        <span class="flex-1">User</span>
                        @if (request()->routeIs('admin.users.*'))
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                        @endif
                    </a>
                @endcan
            </nav>

            <div class="border-t border-slate-100 p-4">
                <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-rose-100 text-sm font-extrabold text-rose-800">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="grid size-9 place-items-center rounded-xl text-slate-400 transition hover:bg-white hover:text-rose-700" aria-label="Keluar">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-10">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-rose-200 hover:text-rose-700 lg:hidden"
                        aria-label="Buka navigasi"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    </button>
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Admin / <span class="text-slate-700">@yield('page_name', 'Dashboard')</span></p>
                        <p class="mt-0.5 text-sm font-extrabold tracking-tight text-slate-900">Panel administrasi</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-800 sm:px-4 sm:text-sm">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5.5 9v11h13V9M9 20v-6h6v6" /></svg>
                        <span>Lihat toko</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="grid size-10 place-items-center rounded-xl bg-rose-100 text-sm font-extrabold text-rose-800 transition hover:bg-rose-200" aria-label="Buka profil">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </a>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1600px] px-4 py-7 sm:px-6 sm:py-9 lg:px-10">
                @yield('content')
            </main>
        </div>
    </body>
</html>
