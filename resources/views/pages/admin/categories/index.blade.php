@extends('layouts.admin')

@section('title', 'Kategori — Admin SN Store')
@section('page_name', 'Kategori')
@section('description', 'Daftar kategori produk SN Store.')

@section('content')
<div x-data="{ async confirmDelete(event) {
    const form = event.currentTarget;
    const result = await window.Swal.fire({
        title: 'Hapus kategori?',
        text: `Kategori ${form.dataset.categoryName} akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#be123c',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
    });

    if (result.isConfirmed) {
        form.submit();
    }
} }">
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Kategori produk</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Daftar kategori yang tersimpan di database.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex w-fit items-center gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 shadow-sm shadow-slate-900/[0.02]">
                <span class="grid size-10 place-items-center rounded-xl bg-rose-50 text-rose-700">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold leading-5 text-slate-900">{{ number_format($categories->count(), 0, ',', '.') }}</span>
                    <span class="mt-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total kategori</span>
                </span>
            </div>
            @can('categories.create')
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-700/20 transition hover:-translate-y-0.5 hover:bg-rose-800">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                    Tambah kategori
                </a>
            @endcan
        </div>
    </section>

    @if (session('status'))
        <div role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="categories-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="categories-table-heading" class="text-sm font-extrabold text-slate-900">Daftar kategori</h2>
                <p class="mt-1 text-xs text-slate-500">Data kategori tersimpan</p>
            </div>
            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-[10px] font-bold text-rose-700">
                {{ number_format($categories->count(), 0, ',', '.') }} ditampilkan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">No.</th>
                        <th scope="col" class="px-4 py-3.5">Kategori</th>
                        <th scope="col" class="px-4 py-3.5">Slug</th>
                        <th scope="col" class="px-4 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Dibuat pada</th>
                        @if (auth()->user()->can('categories.update') || auth()->user()->can('categories.delete'))
                            <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Aksi</th>
                        @endif
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
                                        <span class="block text-sm font-bold text-slate-800">{{ $category->name }}</span>
                                        <span class="mt-0.5 block max-w-sm truncate text-xs text-slate-500">{{ $category->description ?: 'Tidak ada deskripsi' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <code class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $category->slug }}</code>
                            </td>
                            <td class="px-4 py-4">
                                @if ($category->is_active)
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
                                {{ $category->created_at->format('d/m/Y') }}
                            </td>
                            @if (auth()->user()->can('categories.update') || auth()->user()->can('categories.delete'))
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex justify-end gap-2">
                                        @can('categories.update')
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">
                                                Ubah
                                            </a>
                                        @endcan
                                        @can('categories.delete')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.categories.destroy', $category) }}"
                                                data-category-name="{{ $category->name }}"
                                                @submit.prevent="confirmDelete($event)"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('categories.update') || auth()->user()->can('categories.delete') ? 6 : 5 }}" class="px-6 py-16 text-center">
                                <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" d="M12 8v4m0 4h.01" /></svg>
                                </span>
                                <p class="mt-3 text-sm font-bold text-slate-700">Belum ada kategori</p>
                                <p class="mt-1 text-xs text-slate-500">Kategori yang tersimpan akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
