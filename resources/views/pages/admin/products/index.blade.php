<div>
    <!-- It always seems impossible until it is done. - Nelson Mandela -->
</div>
@extends('layouts.admin')

@section('title', 'Produk — Admin SN Store')
@section('page_name', 'Produk')
@section('description', 'Ringkasan produk terjual berdasarkan kategori SN Store.')

@section('content')
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Produk terjual</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Informasi harga, jumlah terjual, pembeli, dan kategori produk berdasarkan transaksi contoh.
            </p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
            <span class="size-1.5 rounded-full bg-amber-500"></span>
            Data pratinjau, belum tersimpan di database
        </span>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]" aria-labelledby="product-categories-heading">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 id="product-categories-heading" class="text-sm font-extrabold text-slate-900">Kategori tersedia</h2>
                <p class="mt-1 text-xs text-slate-500">Kategori contoh yang sama dengan menu Kategori</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-rose-700 transition hover:text-rose-900">Lihat kategori</a>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    <span class="size-1.5 rounded-full {{ $category['is_active'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                    {{ $category['name'] }}
                </span>
            @endforeach
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="products-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="products-table-heading" class="text-sm font-extrabold text-slate-900">Daftar produk terjual</h2>
                <p class="mt-1 text-xs text-slate-500">Penjualan dihitung dari transaksi dengan pembayaran lunas</p>
            </div>
            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-[10px] font-bold text-rose-700">
                {{ number_format($products->count(), 0, ',', '.') }} produk contoh
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">No.</th>
                        <th scope="col" class="px-4 py-3.5">Produk</th>
                        <th scope="col" class="px-4 py-3.5">Kategori</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Harga</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Jumlah terjual</th>
                        <th scope="col" class="px-4 py-3.5">Pembeli</th>
                        <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Pendapatan contoh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-xs font-semibold tabular-nums text-slate-400 sm:px-6">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4">
                                <span class="block text-sm font-bold text-slate-800">{{ $product['name'] }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Berdasarkan pembayaran lunas</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700">{{ $product['category'] }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-semibold tabular-nums text-slate-700">Rp{{ number_format($product['price'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-sm font-bold tabular-nums text-slate-700">{{ number_format($product['sold_quantity'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-xs font-medium leading-5 text-slate-600">{{ implode(', ', $product['buyers']) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-extrabold tabular-nums text-slate-800 sm:px-6">Rp{{ number_format($product['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <p class="text-sm font-bold text-slate-700">Belum ada produk terjual</p>
                                <p class="mt-1 text-xs text-slate-500">Produk dengan pembayaran lunas akan ditampilkan di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
