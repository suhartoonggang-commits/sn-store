@extends('layouts.public')

@section('title', 'Pesanan '.$order->order_number.' — SN Store')
@section('description', 'Ringkasan pesanan Anda di SN Store.')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        @if (session('status'))
            <p role="status" class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">{{ session('status') }}</p>
        @endif

        <div class="border-b border-slate-200 pb-6">
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-rose-700">PESANAN DIBUAT</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-950">{{ $order->order_number }}</h1>
            @if ($order->payment_status === \App\Models\Order::PAYMENT_PAID)
                <p class="mt-3 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-900">Pembayaran berhasil</p>
            @elseif ($order->payment_status === \App\Models\Order::PAYMENT_FAILED)
                <p class="mt-3 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-900">Pembayaran gagal atau kedaluwarsa</p>
            @else
                <p class="mt-3 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Menunggu pembayaran</p>
            @endif
        </div>

        <div class="mt-8 grid gap-8 md:grid-cols-[minmax(0,1fr)_260px]">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Item pesanan</h2>
                <div class="mt-4 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-4 py-4 text-sm">
                            <div>
                                <p class="font-bold text-slate-800">{{ $item->product_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $item->quantity }} × Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <p class="shrink-0 font-bold text-slate-900">Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <h2 class="mt-8 text-lg font-extrabold text-slate-900">Informasi penerima</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Nama</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-slate-500">Nomor HP</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->customer_phone }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->customer_email }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="mt-1 whitespace-pre-line font-semibold text-slate-900">{{ $order->customer_address }}</dd></div>
                    <div><dt class="text-slate-500">Desa/kelurahan</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->village_name }}</dd></div>
                    <div><dt class="text-slate-500">Kecamatan</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->district_name }}</dd></div>
                    <div><dt class="text-slate-500">Kabupaten/kota</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->city_name }}</dd></div>
                    <div><dt class="text-slate-500">Provinsi</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->province_name }}</dd></div>
                    <div><dt class="text-slate-500">Kode pos</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->postal_code }}</dd></div>
                </dl>
            </div>

            <aside class="h-fit border-t border-slate-200 pt-5 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                <h2 class="text-lg font-extrabold text-slate-900">Total pesanan</h2>
                <p class="mt-4 text-2xl font-extrabold text-slate-950">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</p>
                @if ($order->payment_status === \App\Models\Order::PAYMENT_PENDING && $order->snap_token)
                    <button id="pay-with-midtrans" type="button" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Bayar dengan Midtrans</button>
                    <p class="mt-3 text-xs leading-5 text-slate-500">Status pembayaran diperbarui setelah Midtrans mengonfirmasi transaksi.</p>
                    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
                    <script>
                        document.getElementById('pay-with-midtrans').addEventListener('click', () => {
                            window.snap.pay(@js($order->snap_token), {
                                onSuccess: () => window.location.reload(),
                                onPending: () => window.location.reload(),
                                onError: () => window.location.reload(),
                            });
                        });
                    </script>
                @elseif ($order->payment_status === \App\Models\Order::PAYMENT_PENDING)
                    <p class="mt-3 text-xs leading-5 text-slate-500">Token pembayaran sedang disiapkan. Muat ulang halaman beberapa saat lagi.</p>
                @elseif ($order->payment_status === \App\Models\Order::PAYMENT_FAILED)
                    <p class="mt-3 text-xs leading-5 text-slate-500">Stok telah dilepas dan produk dikembalikan ke keranjang.</p>
                @else
                    <p class="mt-3 text-xs leading-5 text-slate-500">Pembayaran diterima oleh Midtrans.</p>
                @endif
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex text-sm font-bold text-rose-700 hover:text-rose-800">Kembali ke katalog</a>
            </aside>
        </div>
    </section>
@endsection