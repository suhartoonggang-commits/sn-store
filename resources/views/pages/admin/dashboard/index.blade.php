@extends('layouts.admin')

@section('title', 'Dashboard — Admin SN Store')
@section('page_name', 'Dashboard')
@section('description', 'Ringkasan aktivitas dan pengelolaan SN Store.')

@section('content')
    <section class="relative isolate overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-rose-700 via-rose-700 to-slate-950 px-6 py-8 shadow-xl shadow-rose-950/10 sm:px-9 sm:py-10">
        <div class="pointer-events-none absolute -right-12 -top-28 -z-10 size-80 rounded-full border-[40px] border-white/5"></div>
        <div class="pointer-events-none absolute -bottom-32 right-40 -z-10 size-64 rounded-full bg-rose-500/20 blur-3xl"></div>

        <div class="relative flex flex-col justify-between gap-8 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.18em] text-rose-100">
                    <span class="size-1.5 rounded-full bg-emerald-300"></span>
                    Panel administrator
                </span>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">
                    Selamat datang kembali, {{ auth()->user()->name }}.
                </h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-rose-100/90 sm:text-base">
                    Pantau toko dan kelola kebutuhan SN Store dari satu tempat. Modul pengelolaan lainnya sedang disiapkan.
                </p>
            </div>

            <a href="{{ route('home') }}" class="inline-flex w-fit shrink-0 items-center gap-2.5 rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-rose-800 shadow-lg shadow-slate-950/10 transition hover:-translate-y-0.5 hover:bg-rose-50">
                Kunjungi toko
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="overview-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Sekilas toko</p>
                <h2 id="overview-heading" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Ringkasan</h2>
            </div>
            <p class="text-xs font-medium text-slate-400">Data ditampilkan sesuai modul yang tersedia</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-rose-50 text-rose-700">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5 12 4l8 4.5v9L12 22l-8-4.5v-9Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m4.3 8.7 7.7 4.4 7.7-4.4M12 13.1V22" /></svg>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-500">Segera tersedia</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-slate-500">Produk aktif</p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">—</p>
                <p class="mt-2 text-xs text-slate-400">Data katalog belum terhubung</p>
            </article>

            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
                    </span>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Data terdaftar</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-slate-500">Kategori</p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($categoryCount, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-400">Kategori produk terdaftar</p>
            </article>

            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-sky-50 text-sky-700">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14a1 1 0 0 1 1 1v15l-3-2-3 2-3-2-3 2-3-2-2 2V5a1 1 0 0 1 1-1Z" /><path stroke-linecap="round" d="M8 9h8M8 13h5" /></svg>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-500">Segera tersedia</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-slate-500">Transaksi hari ini</p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">—</p>
                <p class="mt-2 text-xs text-slate-400">Data transaksi belum terhubung</p>
            </article>

            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.8 20a6.2 6.2 0 0 1 12.4 0M16 5a3.5 3.5 0 0 1 0 6.8M17.5 14a5.5 5.5 0 0 1 3.7 5.2" /></svg>
                    </span>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">Data contoh</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-slate-500">Total pengguna</p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($userCount, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-400">Akun pengguna contoh (dummy)</p>
            </article>
        </div>
    </section>

    <div class="mt-8 grid grid-cols-1 gap-5 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02] sm:p-6 xl:col-span-2" aria-labelledby="activity-heading">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 id="activity-heading" class="text-base font-extrabold tracking-tight text-slate-900">Aktivitas toko</h2>
                    <p class="mt-1 text-xs text-slate-500">Ikhtisar aktivitas transaksi terbaru</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-[10px] font-bold text-amber-700">
                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                    Menunggu data transaksi
                </span>
            </div>

            <div class="relative mt-7 h-56 overflow-hidden rounded-xl bg-slate-50/80 px-4 pb-8 pt-4 sm:px-6">
                <div class="absolute inset-x-4 top-4 bottom-10 flex flex-col justify-between sm:inset-x-6" aria-hidden="true">
                    <span class="border-t border-dashed border-slate-200"></span>
                    <span class="border-t border-dashed border-slate-200"></span>
                    <span class="border-t border-dashed border-slate-200"></span>
                    <span class="border-t border-slate-200"></span>
                </div>
                <div class="absolute inset-0 grid place-items-center px-4">
                    <div class="max-w-sm text-center">
                        <span class="mx-auto grid size-11 place-items-center rounded-xl border border-slate-200 bg-white text-slate-400 shadow-sm">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h17M7 15l4-4 3 2 5-6" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 7h3v3" /></svg>
                        </span>
                        <p class="mt-3 text-sm font-bold text-slate-700">Belum ada aktivitas untuk ditampilkan</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Grafik akan terisi saat modul transaksi mulai digunakan.</p>
                    </div>
                </div>
                <div class="absolute inset-x-4 bottom-3 flex justify-between text-[10px] font-semibold text-slate-400 sm:inset-x-6" aria-hidden="true">
                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02] sm:p-6" aria-labelledby="modules-heading">
            <div>
                <h2 id="modules-heading" class="text-base font-extrabold tracking-tight text-slate-900">Modul pengelolaan</h2>
                <p class="mt-1 text-xs text-slate-500">Fitur yang akan hadir di panel admin</p>
            </div>

            <ul class="mt-5 divide-y divide-slate-100">
                <li class="flex items-center gap-3 py-3 first:pt-0">
                    <span class="grid size-9 place-items-center rounded-lg bg-rose-50 text-rose-700">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /></svg>
                    </span>
                    <span class="flex-1 text-sm font-semibold text-slate-700">Kategori &amp; produk</span>
                    <span class="size-2 rounded-full bg-slate-300" aria-label="Dalam persiapan"></span>
                </li>
                <li class="flex items-center gap-3 py-3">
                    <span class="grid size-9 place-items-center rounded-lg bg-sky-50 text-sky-700">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14a1 1 0 0 1 1 1v15l-3-2-3 2-3-2-3 2-3-2-2 2V5a1 1 0 0 1 1-1Z" /></svg>
                    </span>
                    <span class="flex-1 text-sm font-semibold text-slate-700">Transaksi</span>
                    <span class="size-2 rounded-full bg-slate-300" aria-label="Dalam persiapan"></span>
                </li>
                <li class="flex items-center gap-3 py-3 last:pb-0">
                    <span class="grid size-9 place-items-center rounded-lg bg-emerald-50 text-emerald-700">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3.5" /><path stroke-linecap="round" d="M2.8 20a6.2 6.2 0 0 1 12.4 0M16 5a3.5 3.5 0 0 1 0 6.8M17.5 14a5.5 5.5 0 0 1 3.7 5.2" /></svg>
                    </span>
                    <span class="flex-1 text-sm font-semibold text-slate-700">Pengguna</span>
                    <span class="size-2 rounded-full bg-slate-300" aria-label="Dalam persiapan"></span>
                </li>
            </ul>

            <div class="mt-5 rounded-xl bg-rose-50/80 p-4">
                <p class="text-xs font-bold text-rose-900">Satu langkah dalam satu waktu</p>
                <p class="mt-1 text-xs leading-5 text-rose-800/80">Dashboard admin sudah siap. Pengelolaan katalog dan pesanan dapat ditambahkan bertahap.</p>
            </div>
        </section>
    </div>
@endsection
