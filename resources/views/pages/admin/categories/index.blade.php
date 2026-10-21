@extends('layouts.admin')

@section('title', 'Kategori — Admin SN Store')
@section('page_name', 'Kategori')
@section('description', 'Daftar kategori produk SN Store.')

@section('content')
<div x-data="{ addCategoryOpen: false, showDemoNotice: false }" @keydown.escape.window="addCategoryOpen = false">
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Kategori produk</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Contoh kategori produk untuk pratinjau panel admin. Data ini belum tersimpan di database.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex w-fit items-center gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 shadow-sm shadow-slate-900/[0.02]">
                <span class="grid size-10 place-items-center rounded-xl bg-rose-50 text-rose-700">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold leading-5 text-slate-900">{{ number_format($categories->count(), 0, ',', '.') }}</span>
                    <span class="mt-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori contoh</span>
                </span>
            </div>
            <button
                type="button"
                @click="addCategoryOpen = true; showDemoNotice = false"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-700/20 transition hover:-translate-y-0.5 hover:bg-rose-800"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                Tambah data
            </button>
        </div>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="categories-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="categories-table-heading" class="text-sm font-extrabold text-slate-900">Daftar kategori</h2>
                <p class="mt-1 text-xs text-slate-500">Data contoh, belum terhubung ke database</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1.5 text-[10px] font-bold text-amber-700">
                {{ number_format($categories->count(), 0, ',', '.') }} ditampilkan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">No.</th>
                        <th scope="col" class="px-4 py-3.5">Kategori</th>
                        <th scope="col" class="px-4 py-3.5">Slug</th>
                        <th scope="col" class="px-4 py-3.5">Jumlah produk (dummy)</th>
                        <th scope="col" class="px-4 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Tanggal contoh</th>
                        <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-xs font-semibold tabular-nums text-slate-400 sm:px-6">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-rose-50 text-rose-700">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /></svg>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-bold text-slate-800">{{ $category['name'] }}</span>
                                        <span class="mt-0.5 block max-w-sm truncate text-xs text-slate-500">{{ $category['description'] }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <code class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $category['slug'] }}</code>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-semibold tabular-nums text-slate-700">{{ $category['product_count'] }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if ($category['is_active'])
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-500">
                                        <span class="size-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500 sm:px-6">
                                {{ $category['created_at'] }}
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex justify-end gap-1.5">
                                    <button type="button" title="Edit kategori (demo)" aria-label="Edit kategori {{ $category['name'] }}" class="grid size-9 place-items-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-700">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14 5 5 5M4 20l4.2-.8L19 8.4a2.1 2.1 0 0 0-3-3L5.2 16.2 4 20Z" /></svg>
                                    </button>
                                    <button type="button" title="Hapus kategori (demo)" aria-label="Hapus kategori {{ $category['name'] }}" class="grid size-9 place-items-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-700">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" d="M12 8v4m0 4h.01" /></svg>
                                </span>
                                <p class="mt-3 text-sm font-bold text-slate-700">Belum ada kategori</p>
                                <p class="mt-1 text-xs text-slate-500">Kategori akan muncul di sini setelah ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>

    <div
        x-cloak
        x-show="addCategoryOpen"
        x-transition.opacity
        class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
        @click.self="addCategoryOpen = false"
    >
        <section
            x-show="addCategoryOpen"
            x-transition
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-category-title"
            class="my-auto w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl shadow-slate-950/20"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-rose-700">Kategori baru</p>
                    <h2 id="add-category-title" class="mt-1 text-lg font-extrabold tracking-tight text-slate-900">Tambah kategori</h2>
                    <p class="mt-1 text-xs text-slate-500">Pratinjau formulir — belum menyimpan data.</p>
                </div>
                <button type="button" @click="addCategoryOpen = false" class="grid size-9 place-items-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup formulir">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                </button>
            </div>

            <form class="space-y-5 p-6" @submit.prevent="showDemoNotice = true">
                <label class="block">
                    <span class="mb-2 block text-xs font-bold text-slate-700">Nama kategori</span>
                    <input type="text" name="name" placeholder="Contoh: Perangkat rumah" class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200">
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-bold text-slate-700">Deskripsi</span>
                    <textarea name="description" rows="3" placeholder="Jelaskan kategori produk ini..." class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200"></textarea>
                </label>

                <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3.5">
                    <input type="checkbox" name="is_active" checked class="rounded border-slate-300 text-rose-700 focus:ring-rose-300">
                    <span>
                        <span class="block text-xs font-bold text-slate-700">Kategori aktif</span>
                        <span class="mt-0.5 block text-[11px] text-slate-500">Tampilkan kategori ini di toko.</span>
                    </span>
                </label>

                <p x-show="showDemoNotice" x-transition role="status" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold leading-5 text-amber-800">
                    Ini hanya pratinjau. Data tidak disimpan karena koneksi database belum digunakan.
                </p>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" @click="addCategoryOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-800">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                        Simpan (demo)
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
