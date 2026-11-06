@extends('layouts.public')

@section('title', 'Checkout — SN Store')
@section('description', 'Lengkapi informasi pengiriman untuk menyelesaikan pesanan Anda.')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="mb-8 border-b border-slate-200 pb-6">
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-rose-700">LANGKAH TERAKHIR</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-950 sm:text-4xl">Checkout</h1>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_360px]">
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                @csrf
                <fieldset class="space-y-5">
                    <legend class="text-lg font-extrabold text-slate-900">Informasi penerima</legend>
                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama lengkap</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                        @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-bold text-slate-700">Nomor HP</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $user->phone) }}" required maxlength="30" class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            @error('phone') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            @error('email') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="address" class="mb-2 block text-sm font-bold text-slate-700">Alamat lengkap</label>
                        <textarea id="address" name="address" rows="4" autocomplete="street-address" required maxlength="5000" class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">{{ old('address', $user->address) }}</textarea>
                        @error('address') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                </fieldset>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-rose-700 px-5 py-3.5 text-sm font-bold text-white transition hover:bg-rose-800 sm:w-auto">Buat pesanan</button>
            </form>

            <aside class="h-fit border-t border-slate-200 pt-6 lg:border-t-0 lg:pt-0">
                <h2 class="text-lg font-extrabold text-slate-900">Pesanan Anda</h2>
                <div class="mt-4 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($cartItems as $cartItem)
                        <div class="flex justify-between gap-4 py-4 text-sm">
                            <div>
                                <p class="font-bold text-slate-800">{{ $cartItem->product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $cartItem->quantity }} × Rp{{ number_format((float) $cartItem->product->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="shrink-0 font-bold text-slate-900">Rp{{ number_format((float) $cartItem->product->price * $cartItem->quantity, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 flex justify-between text-sm font-extrabold text-slate-900"><span>Total</span><span>Rp{{ number_format($cartItems->sum(fn ($item) => (float) $item->product->price * $item->quantity), 0, ',', '.') }}</span></p>
                <p class="mt-5 border-l-2 border-rose-400 pl-3 text-xs leading-5 text-slate-600">Pembayaran pesanan akan dilanjutkan melalui halaman aman Midtrans.</p>
            </aside>
        </div>
    </section>
@endsection