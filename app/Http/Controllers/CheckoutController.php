<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Services\CheckoutService;
use App\Services\RajaOngkirShippingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Facade as Indonesia;
use RuntimeException;
use Throwable;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->cartItems()->doesntExist()) {
            return redirect()->route('cart.index')->with('status', 'Tambahkan produk sebelum checkout.');
        }

        $cartItems = $user->cartItems()->with('product')->get();

        return view('pages.public.checkout', [
            'user' => $user,
            'cartItems' => $cartItems,
            'subtotal' => $cartItems->sum(fn ($item): int => (int) round((float) $item->product->price) * $item->quantity),
            'couriers' => config('services.rajaongkir.couriers', []),
        ]);
    }

    public function store(
        Request $request,
        CheckoutService $checkoutService,
        PaymentGateway $paymentGateway,
        RajaOngkirShippingService $shippingService
    ): RedirectResponse {
        $tablePrefix = config('laravolt.indonesia.table_prefix', 'indonesia_');
        $customer = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:5000'],
            'province_id' => ['required', 'integer', Rule::exists($tablePrefix.'provinces', 'id')],
            'city_id' => ['required', 'integer', Rule::exists($tablePrefix.'cities', 'id')],
            'district_id' => ['required', 'integer', Rule::exists($tablePrefix.'districts', 'id')],
            'village_id' => ['required', 'integer', Rule::exists($tablePrefix.'villages', 'id')],
            'postal_code' => ['required', 'string', 'regex:/^\d{5}$/'],
            'shipping_courier' => ['required', 'string', Rule::in(config('services.rajaongkir.couriers', []))],
            'shipping_service' => ['required', 'string', 'max:50'],
        ]);

        $province = Indonesia::findProvince($customer['province_id']);
        $city = Indonesia::findCity($customer['city_id']);
        $district = Indonesia::findDistrict($customer['district_id']);
        $village = Indonesia::findVillage($customer['village_id']);

        if (! $province || ! $city || ! $district || ! $village) {
            throw ValidationException::withMessages([
                'province_id' => 'Wilayah yang dipilih tidak ditemukan. Silakan pilih ulang wilayah Anda.',
            ]);
        }

        if ($city->province_code !== $province->code) {
            throw ValidationException::withMessages(['city_id' => 'Kabupaten/kota tidak sesuai dengan provinsi.']);
        }

        if ($district->city_code !== $city->code) {
            throw ValidationException::withMessages(['district_id' => 'Kecamatan tidak sesuai dengan kabupaten/kota.']);
        }

        if ($village->district_code !== $district->code) {
            throw ValidationException::withMessages(['village_id' => 'Desa/kelurahan tidak sesuai dengan kecamatan.']);
        }

        $customer += [
            'province_code' => $province->code,
            'province_name' => $province->name,
            'city_code' => $city->code,
            'city_name' => $city->name,
            'district_code' => $district->code,
            'district_name' => $district->name,
            'village_code' => $village->code,
            'village_name' => $village->name,
        ];

        try {
            $shippingRates = $shippingService->quoteForUser(
                $request->user(),
                $customer['province_id'],
                $customer['city_id'],
                $customer['district_id'],
                $customer['shipping_courier'],
            );
        } catch (ConnectionException|RuntimeException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors(['shipping' => 'Ongkos kirim gagal diperbarui. Silakan coba kembali.']);
        }

        $selectedRate = collect($shippingRates)->first(
            fn (array $rate): bool => $rate['service'] === $customer['shipping_service']
        );

        if (! $selectedRate) {
            throw ValidationException::withMessages([
                'shipping_service' => 'Pilih layanan ongkir yang tersedia dari hasil cek ongkos kirim.',
            ]);
        }

        $customer += [
            'shipping_cost' => $selectedRate['cost'],
            'shipping_courier' => $selectedRate['courier_code'],
            'shipping_courier_name' => $selectedRate['courier_name'],
            'shipping_service' => $selectedRate['service'],
            'shipping_etd' => $selectedRate['etd'],
        ];

        $order = $checkoutService->placeOrder($request->user(), $customer);

        try {
            $payment = $paymentGateway->createSnapPayment($order);
        } catch (Throwable $exception) {
            report($exception);
            $checkoutService->failAndRestore($order);

            return redirect()
                ->route('cart.index')
                ->withErrors(['payment' => 'Pembayaran tidak dapat disiapkan. Silakan coba kembali.']);
        }

        $order->update([
            'payment_gateway' => 'midtrans',
            'snap_token' => $payment['token'],
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Pesanan berhasil dibuat. Lanjutkan pembayaran melalui Midtrans.');
    }
}
