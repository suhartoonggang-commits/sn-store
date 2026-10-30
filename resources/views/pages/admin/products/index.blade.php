@extends('layouts.admin')

@section('title', 'Produk — Admin SN Store')
@section('page_name', 'Produk')
@section('description', 'Daftar produk tersimpan di database SN Store.')

@section('content')
<div x-data="{ async confirmDelete(event) {
    const form = event.currentTarget;
    const result = await window.Swal.fire({
        title: 'Hapus produk?',
        text: `Produk ${form.dataset.productName} akan dihapus permanen.`,
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
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Produk</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Kelola katalog, gambar, harga, dan stok produk yang tersimpan di database.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex w-fit items-center gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 shadow-sm shadow-slate-900/[0.02]">
                <span class="grid size-10 place-items-center rounded-xl bg-rose-50 text-rose-700">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5 12 4l8 4.5v9L12 22l-8-4.5v-9Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m4.3 8.7 7.7 4.4 7.7-4.4M12 13.1V22" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold leading-5 text-slate-900">{{ number_format($products->total(), 0, ',', '.') }}</span>
                    <span class="mt-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total produk</span>
                </span>
            </div>
            @can('products.create')
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-700/20 transition hover:-translate-y-0.5 hover:bg-rose-800">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                    Tambah produk
                </a>
            @endcan
        </div>
    </section>

    @if (session('status'))
        <div role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="products-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="products-table-heading" class="text-sm font-extrabold text-slate-900">Daftar produk</h2>
                <p class="mt-1 text-xs text-slate-500">Data katalog produk dari database</p>
            </div>
            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-[10px] font-bold text-rose-700">
                {{ number_format($products->total(), 0, ',', '.') }} produk
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">No.</th>
                        <th scope="col" class="px-4 py-3.5">Produk</th>
                        <th scope="col" class="px-4 py-3.5">Kategori</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Harga</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Stok</th>
                        <th scope="col" class="px-4 py-3.5">Status</th>
                        @if (auth()->user()->can('products.update') || auth()->user()->can('products.delete'))
                            <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-xs font-semibold tabular-nums text-slate-400 sm:px-6">{{ $products->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}" class="size-12 shrink-0 rounded-xl border border-slate-100 object-cover">
                                    @else
                                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-slate-100 text-slate-400">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2" /><circle cx="8.5" cy="9" r="1.5" /><path stroke-linecap="round" stroke-linejoin="round" d="m20.5 15-5-5L7 19.5" /></svg>
                                        </span>
                                    @endif
                                    <span class="min-w-0">
                                        <span class="block text-sm font-bold text-slate-800">{{ $product->name }}</span>
                                        @if ($product->description)
                                            <span class="mt-0.5 block max-w-sm truncate text-xs text-slate-500">{{ $product->description }}</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700">{{ $product->category?->name ?? 'Tanpa kategori' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-semibold tabular-nums text-slate-700">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-sm font-bold tabular-nums text-slate-700">{{ number_format($product->stock, 0, ',', '.') }}</td>
                            <td class="px-4 py-4">
                                @if ($product->is_active)
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
                            @if (auth()->user()->can('products.update') || auth()->user()->can('products.delete'))
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex justify-end gap-2">
                                        @can('products.update')
                                            <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">
                                                Ubah
                                            </a>
                                        @endcan
                                        @can('products.delete')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.products.destroy', $product) }}"
                                                data-product-name="{{ $product->name }}"
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
                            <td colspan="{{ auth()->user()->can('products.update') || auth()->user()->can('products.delete') ? 7 : 6 }}" class="px-6 py-16 text-center">
                                <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5 12 4l8 4.5v9L12 22l-8-4.5v-9Z" /><path stroke-linecap="round" d="M12 8v4m0 4h.01" /></svg>
                                </span>
                                <p class="mt-3 text-sm font-bold text-slate-700">Belum ada produk</p>
                                <p class="mt-1 text-xs text-slate-500">Produk yang tersimpan akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
