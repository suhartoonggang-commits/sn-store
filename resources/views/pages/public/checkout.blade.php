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
                <fieldset
                    class="space-y-5"
                    x-data="{
                        provinceId: @js((string) old('province_id', '')),
                        cityId: @js((string) old('city_id', '')),
                        districtId: @js((string) old('district_id', '')),
                        villageId: @js((string) old('village_id', '')),
                        courier: @js(old('shipping_courier', 'jne')),
                        shippingRates: [],
                        shippingService: @js(old('shipping_service', '')),
                        shippingCost: 0,
                        checkingShipping: false,
                        shippingError: '',
                        provinces: [],
                        cities: [],
                        districts: [],
                        villages: [],
                        loadingCities: false,
                        loadingDistricts: false,
                        loadingVillages: false,
                        async fetchRegions(url) {
                            try {
                                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                                return response.ok ? await response.json() : [];
                            } catch {
                                return [];
                            }
                        },
                        async initialize() {
                            this.provinces = await this.fetchRegions('{{ route('regions.provinces') }}');
                            if (this.provinceId) {
                                await this.loadCities(false);
                            }
                            if (this.cityId) {
                                await this.loadDistricts(false);
                            }
                            if (this.districtId) {
                                await this.loadVillages(false);
                            }
                        },
                        async loadCities(reset = true) {
                            if (reset) {
                                this.cityId = '';
                                this.districtId = '';
                                this.villageId = '';
                                this.districts = [];
                                this.villages = [];
                                this.resetShipping();
                            }
                            this.cities = [];
                            if (!this.provinceId) return;
                            this.loadingCities = true;
                            this.cities = await this.fetchRegions(`/regions/provinces/${this.provinceId}/cities`);
                            this.loadingCities = false;
                        },
                        async loadDistricts(reset = true) {
                            if (reset) {
                                this.districtId = '';
                                this.villageId = '';
                                this.villages = [];
                                this.resetShipping();
                            }
                            this.districts = [];
                            if (!this.cityId) return;
                            this.loadingDistricts = true;
                            this.districts = await this.fetchRegions(`/regions/cities/${this.cityId}/districts`);
                            this.loadingDistricts = false;
                        },
                        async loadVillages(reset = true) {
                            if (reset) this.villageId = '';
                            this.villages = [];
                            if (!this.districtId) return;
                            this.loadingVillages = true;
                            this.villages = await this.fetchRegions(`/regions/districts/${this.districtId}/villages`);
                            this.loadingVillages = false;
                        },
                        resetShipping() {
                            this.shippingRates = [];
                            this.shippingService = '';
                            this.shippingCost = 0;
                            this.shippingError = '';
                            this.$dispatch('shipping-cost-updated', 0);
                        },
                        async checkShipping() {
                            this.shippingError = '';
                            this.shippingRates = [];
                            this.shippingService = '';
                            this.shippingCost = 0;
                            this.$dispatch('shipping-cost-updated', 0);

                            if (!this.provinceId || !this.cityId || !this.districtId) {
                                this.shippingError = 'Pilih provinsi, kabupaten/kota, dan kecamatan terlebih dahulu.';
                                return;
                            }

                            this.checkingShipping = true;
                            try {
                                const response = await fetch('{{ route('shipping.quotes') }}', {
                                    method: 'POST',
                                    headers: {
                                        Accept: 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                    body: JSON.stringify({
                                        province_id: this.provinceId,
                                        city_id: this.cityId,
                                        district_id: this.districtId,
                                        courier: this.courier,
                                    }),
                                });
                                const payload = await response.json();
                                if (!response.ok) {
                                    this.shippingError = payload.message || 'Ongkos kirim gagal dihitung.';
                                    return;
                                }
                                this.shippingRates = payload.rates || [];
                                if (this.shippingRates.length === 0) {
                                    this.shippingError = 'Tidak ada layanan ongkir untuk wilayah ini.';
                                }
                            } catch {
                                this.shippingError = 'Tidak dapat menghubungi layanan ongkir. Silakan coba kembali.';
                            } finally {
                                this.checkingShipping = false;
                            }
                        },
                        selectShipping(rate) {
                            this.shippingService = rate.service;
                            this.shippingCost = rate.cost;
                            this.$dispatch('shipping-cost-updated', rate.cost);
                        }
                    }"
                    x-init="initialize()"
                >
                    <legend class="text-lg font-extrabold text-slate-900">Informasi penerima</legend>
                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama lengkap</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                        @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="province_id" class="mb-2 block text-sm font-bold text-slate-700">Provinsi</label>
                            <select id="province_id" name="province_id" x-model="provinceId" @change="loadCities()" required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Pilih provinsi</option>
                                <template x-for="province in provinces" :key="province.id">
                                    <option :value="province.id" x-text="province.name"></option>
                                </template>
                            </select>
                            @error('province_id') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="city_id" class="mb-2 block text-sm font-bold text-slate-700">Kabupaten/kota</label>
                            <select id="city_id" name="city_id" x-model="cityId" @change="loadDistricts()" :disabled="!provinceId || loadingCities" disabled required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Pilih kabupaten/kota</option>
                                <template x-for="city in cities" :key="city.id">
                                    <option :value="city.id" x-text="city.name"></option>
                                </template>
                            </select>
                            @error('city_id') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="district_id" class="mb-2 block text-sm font-bold text-slate-700">Kecamatan</label>
                            <select id="district_id" name="district_id" x-model="districtId" @change="loadVillages()" :disabled="!cityId || loadingDistricts" disabled required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Pilih kecamatan</option>
                                <template x-for="district in districts" :key="district.id">
                                    <option :value="district.id" x-text="district.name"></option>
                                </template>
                            </select>
                            @error('district_id') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="village_id" class="mb-2 block text-sm font-bold text-slate-700">Desa/kelurahan</label>
                            <select id="village_id" name="village_id" x-model="villageId" :disabled="!districtId || loadingVillages" disabled required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Pilih desa/kelurahan</option>
                                <template x-for="village in villages" :key="village.id">
                                    <option :value="village.id" x-text="village.name"></option>
                                </template>
                            </select>
                            @error('village_id') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="mb-2 block text-sm font-bold text-slate-700">Kode pos</label>
                            <input id="postal_code" name="postal_code" type="text" inputmode="numeric" autocomplete="postal-code" value="{{ old('postal_code') }}" pattern="[0-9]{5}" maxlength="5" required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            @error('postal_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
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
                    <div class="border-t border-slate-200 pt-5">
                        <h2 class="text-base font-extrabold text-slate-900">Ongkos kirim</h2>
                        <p class="mt-1 text-xs text-slate-500">Asal pengiriman: Makassar</p>
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="w-full sm:max-w-xs">
                                <label for="shipping_courier" class="mb-2 block text-sm font-bold text-slate-700">Kurir</label>
                                <select id="shipping_courier" name="shipping_courier" x-model="courier" @change="resetShipping()" required class="w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                    @foreach ($couriers as $courierOption)
                                        <option value="{{ $courierOption }}">{{ strtoupper($courierOption) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" @click="checkShipping()" :disabled="checkingShipping || !districtId" class="inline-flex shrink-0 items-center justify-center rounded-lg border border-rose-700 px-4 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400">
                                <span x-text="checkingShipping ? 'Menghitung...' : 'Cek ongkos kirim'"></span>
                            </button>
                        </div>
                        @error('shipping_courier') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        @error('shipping_service') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        <p x-show="shippingError" x-text="shippingError" role="alert" class="mt-3 text-sm text-rose-700"></p>
                        <div x-show="shippingRates.length > 0" class="mt-4 grid gap-2 sm:grid-cols-2">
                            <template x-for="rate in shippingRates" :key="rate.key">
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-3 transition hover:border-rose-300 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50">
                                    <input type="radio" name="shipping_service" :value="rate.service" x-model="shippingService" @change="selectShipping(rate)" required class="mt-1 border-slate-300 text-rose-700 focus:ring-rose-500">
                                    <span class="min-w-0 flex-1">
                                        <span class="flex items-start justify-between gap-2 text-sm font-bold text-slate-900">
                                            <span x-text="rate.courier_name + ' ' + rate.service"></span>
                                            <span class="shrink-0" x-text="'Rp' + Number(rate.cost).toLocaleString('id-ID')"></span>
                                        </span>
                                        <span class="mt-1 block text-xs text-slate-500" x-text="rate.description + (rate.etd ? ' · ' + rate.etd : '')"></span>
                                    </span>
                                </label>
                            </template>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-rose-700 px-5 py-3.5 text-sm font-bold text-white transition hover:bg-rose-800 sm:w-auto">Buat pesanan</button>
            </form>

            <aside x-data="{ shippingCost: 0 }" @shipping-cost-updated.window="shippingCost = Number($event.detail)" class="h-fit border-t border-slate-200 pt-6 lg:border-t-0 lg:pt-0">
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
                <div class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm">
                    <p class="flex justify-between text-slate-500"><span>Subtotal produk</span><span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span></p>
                    <p class="flex justify-between text-slate-500"><span>Ongkos kirim</span><span x-text="shippingCost ? 'Rp' + shippingCost.toLocaleString('id-ID') : 'Belum dipilih'"></span></p>
                    <p class="flex justify-between border-t border-slate-200 pt-3 font-extrabold text-slate-900"><span>Total</span><span x-text="'Rp' + ({{ $subtotal }} + shippingCost).toLocaleString('id-ID')"></span></p>
                </div>
                <p class="mt-5 border-l-2 border-rose-400 pl-3 text-xs leading-5 text-slate-600">Pembayaran pesanan akan dilanjutkan melalui halaman aman Midtrans.</p>
            </aside>
        </div>
    </section>
@endsection