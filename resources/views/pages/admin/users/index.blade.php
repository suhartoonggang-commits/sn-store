@extends('layouts.admin')

@section('title', 'User — Admin SN Store')
@section('page_name', 'User')
@section('description', 'Daftar pembeli dan riwayat transaksi contoh SN Store.')

@section('content')
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Pelanggan toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">User &amp; riwayat transaksi</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Daftar pembeli yang tercatat pada transaksi contoh dan ringkasan riwayatnya.
            </p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
            <span class="size-1.5 rounded-full bg-amber-500"></span>
            Data pratinjau, bukan data akun database
        </span>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="users-table-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="users-table-heading" class="text-sm font-extrabold text-slate-900">User yang bertransaksi</h2>
                <p class="mt-1 text-xs text-slate-500">Ringkasan jumlah transaksi dan total pembayaran lunas</p>
            </div>
            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-[10px] font-bold text-rose-700">{{ number_format($users->count(), 0, ',', '.') }} user contoh</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">No.</th>
                        <th scope="col" class="px-4 py-3.5">Nama user</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Jumlah transaksi</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Pembayaran lunas</th>
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Transaksi terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-xs font-semibold tabular-nums text-slate-400 sm:px-6">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4">
                                <span class="block text-sm font-bold text-slate-800">{{ $user['name'] }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">{{ $user['email'] }}</span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-semibold tabular-nums text-slate-700">{{ number_format($user['transaction_count'], 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-extrabold tabular-nums text-slate-800">Rp{{ number_format($user['paid_total'], 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500 sm:px-6">{{ $user['last_transaction'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <p class="text-sm font-bold text-slate-700">Belum ada user bertransaksi</p>
                                <p class="mt-1 text-xs text-slate-500">User akan tampil setelah sumber transaksi disambungkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="user-history-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 id="user-history-heading" class="text-sm font-extrabold text-slate-900">History transaksi user</h2>
                <p class="mt-1 text-xs text-slate-500">Semua status transaksi contoh, termasuk pembayaran yang belum lunas</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs font-bold text-rose-700 transition hover:text-rose-900">Lihat semua transaksi</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Kode transaksi</th>
                        <th scope="col" class="px-4 py-3.5">User</th>
                        <th scope="col" class="px-4 py-3.5">Produk</th>
                        <th scope="col" class="px-4 py-3.5">Tanggal</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Total</th>
                        <th scope="col" class="px-5 py-3.5 sm:px-6">Status pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4 text-xs font-extrabold text-slate-800 sm:px-6">{{ $transaction['code'] }}</td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-bold text-slate-700">{{ $transaction['buyer'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['email'] }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-semibold text-slate-700">{{ $transaction['product'] }}</span>
                                <span class="mt-1 block text-[11px] text-slate-500">{{ $transaction['quantity'] }} item</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-500">{{ $transaction['date'] }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-xs font-extrabold tabular-nums text-slate-800">Rp{{ number_format($transaction['price'] * $transaction['quantity'], 0, ',', '.') }}</td>
                            <td class="px-5 py-4 sm:px-6">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    'bg-emerald-50 text-emerald-700' => $transaction['payment_status'] === 'Lunas',
                                    'bg-amber-50 text-amber-700' => $transaction['payment_status'] === 'Menunggu pembayaran',
                                    'bg-slate-100 text-slate-600' => $transaction['payment_status'] === 'Dibatalkan',
                                ])>{{ $transaction['payment_status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <p class="text-sm font-bold text-slate-700">Belum ada history transaksi</p>
                                <p class="mt-1 text-xs text-slate-500">Riwayat user akan tampil setelah sumber transaksi disambungkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
