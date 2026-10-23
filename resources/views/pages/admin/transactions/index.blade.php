@extends('layouts.admin')

@section('title', 'Transaksi — Admin SN Store')
@section('page_name', 'Transaksi')
@section('description', 'Pratinjau pembayaran online dan transaksi SN Store.')

@section('content')
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Pesanan toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Pembayaran &amp; transaksi</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Ringkasan pembayaran dan riwayat transaksi contoh untuk pratinjau alur online.
            </p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
            <span class="size-1.5 rounded-full bg-amber-500"></span>
            Belum tersambung ke payment gateway
        </span>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Ringkasan pembayaran contoh">
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
            <p class="text-xs font-semibold text-slate-500">Transaksi contoh</p>
            <p class="mt-2 text-2xl font-extrabold tabular-nums text-slate-900">{{ number_format($paymentCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-400">Semua status pembayaran</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">
            <p class="text-xs font-semibold text-emerald-800">Pembayaran lunas</p>
            <p class="mt-2 text-2xl font-extrabold tabular-nums text-emerald-900">{{ number_format($paidCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-emerald-700/80">Status contoh, bukan konfirmasi gateway</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/70 p-5">
            <p class="text-xs font-semibold text-amber-800">Menunggu pembayaran</p>
            <p class="mt-2 text-2xl font-extrabold tabular-nums text-amber-900">{{ number_format($pendingCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-amber-700/80">Tidak ada instruksi pembayaran aktif</p>
        </article>
    </section>

    <section class="mt-6 rounded-2xl border border-rose-100 bg-rose-50/70 p-5 sm:p-6" aria-labelledby="payment-preview-heading">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-rose-700">Pratinjau metode pembayaran</p>
                <h2 id="payment-preview-heading" class="mt-1 text-base font-extrabold text-slate-900">Pembayaran online</h2>
                <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-600">Contoh metode QRIS, virtual account, transfer bank, dan dompet digital. Referensi gateway di bawah hanya data simulasi.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-white bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm">
                <span class="size-2 rounded-full bg-amber-400"></span>
                Midtrans (demo)
            </span>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="transactions-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="transactions-table-heading" class="text-sm font-extrabold text-slate-900">Tabel transaksi</h2>
                <p class="mt-1 text-xs text-slate-500">Data contoh belum terhubung ke model transaksi atau database</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ number_format($transactions->count(), 0, ',', '.') }} transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Kode / waktu</th>
                        <th scope="col" class="px-4 py-3.5">Pembeli</th>
                        <th scope="col" class="px-4 py-3.5">Produk</th>
                        <th scope="col" class="px-4 py-3.5">Metode / gateway</th>
                        <th scope="col" class="px-4 py-3.5">Referensi demo</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Total</th>
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <span class="block text-xs font-extrabold text-slate-800">{{ $transaction['code'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['date'] }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-bold text-slate-700">{{ $transaction['buyer'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['email'] }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-bold text-slate-700">{{ $transaction['product'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['quantity'] }} × Rp{{ number_format($transaction['price'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-semibold text-slate-700">{{ $transaction['payment_method'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['payment_gateway'] }}</span>
                            </td>
                            <td class="px-4 py-4"><code class="rounded bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-600">{{ $transaction['gateway_reference'] }}</code></td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-xs font-extrabold tabular-nums text-slate-800">Rp{{ number_format($transaction['price'] * $transaction['quantity'], 0, ',', '.') }}</td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex flex-col items-start gap-1.5">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold',
                                        'bg-emerald-50 text-emerald-700' => $transaction['payment_status'] === 'Lunas',
                                        'bg-amber-50 text-amber-700' => $transaction['payment_status'] === 'Menunggu pembayaran',
                                        'bg-slate-100 text-slate-600' => $transaction['payment_status'] === 'Dibatalkan',
                                    ])>{{ $transaction['payment_status'] }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $transaction['order_status'] }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <p class="text-sm font-bold text-slate-700">Belum ada transaksi</p>
                                <p class="mt-1 text-xs text-slate-500">Transaksi akan tampil di sini setelah sumber data disambungkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
