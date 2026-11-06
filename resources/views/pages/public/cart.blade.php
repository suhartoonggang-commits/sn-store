@extends('layouts.public')

@section('title', 'Keranjang Belanja — SN Store')
@section('description', 'Tinjau produk pilihan Anda sebelum melanjutkan checkout.')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="mb-8 border-b border-slate-200 pb-6">
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-rose-700">PESANAN ANDA</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-950 sm:text-4xl">Keranjang belanja</h1>
        </div>

        @if (session('status'))
            <p role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="border-y border-slate-200 py-16 text-center">
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-slate-100 text-slate-500">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.1a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                </span>
                <h2 class="mt-4 text-lg font-extrabold text-slate-900">Keranjang masih kosong</h2>
                <p class="mt-2 text-sm text-slate-500">Pilih produk yang ingin Anda beli.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Jelajahi produk</a>
            </div>
        @else
            <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($cartItems as $cartItem)
                        <article class="grid gap-4 py-5 sm:grid-cols-[112px_minmax(0,1fr)] sm:items-center">
                            <a href="{{ route('products.index') }}" class="aspect-square overflow-hidden rounded-lg bg-slate-100 sm:size-28">
                                @if ($cartItem->product->image_path)
                                    <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($cartItem->product->image_path) }}" alt="{{ $cartItem->product->name }}" class="size-full object-cover">
                                @else
                                    <span class="grid size-full place-items-center text-center text-xs text-slate-400">Gambar tidak tersedia</span>
                                @endif
                            </a>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="font-bold text-slate-900">{{ $cartItem->product->name }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">Rp{{ number_format((float) $cartItem->product->price, 0, ',', '.') }} per produk</p>
                                    <p class="mt-1 text-xs text-slate-500">Stok tersedia: {{ $cartItem->product->stock }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-4">
                                    <form method="POST" action="{{ route('cart.items.update', $cartItem) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label for="quantity-{{ $cartItem->id }}" class="sr-only">Jumlah {{ $cartItem->product->name }}</label>
                                        <input id="quantity-{{ $cartItem->id }}" type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1" max="{{ $cartItem->product->stock }}" class="w-20 rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                        <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-rose-300 hover:text-rose-700">Perbarui</button>
                                    </form>
                                    <p class="min-w-28 text-right text-sm font-extrabold text-slate-950">Rp{{ number_format((float) $cartItem->product->price * $cartItem->quantity, 0, ',', '.') }}</p>
                                    <form method="POST" action="{{ route('cart.items.destroy', $cartItem) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Hapus {{ $cartItem->product->name }}" class="grid size-9 place-items-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-700">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="h-fit border-t border-slate-200 pt-5 lg:border-t-0 lg:pt-0">
                    <h2 class="text-lg font-extrabold text-slate-900">Ringkasan belanja</h2>
                    <div class="mt-5 flex justify-between border-b border-slate-200 pb-4 text-sm">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-bold text-slate-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-4 text-xs leading-5 text-slate-500">Ongkos kirim atau biaya pembayaran, jika ada, akan diinformasikan saat metode pembayaran tersedia.</p>
                    <a href="{{ route('checkout.create') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Lanjut checkout</a>
                    <a href="{{ route('products.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-400">Lanjut belanja</a>
                </aside>
            </div>
        @endif
    </section>
@endsection