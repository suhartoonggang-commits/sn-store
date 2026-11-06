<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirShippingService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class ShippingController extends Controller
{
    public function quotes(Request $request, RajaOngkirShippingService $shippingService): JsonResponse
    {
        $tablePrefix = config('laravolt.indonesia.table_prefix', 'indonesia_');
        $attributes = $request->validate([
            'province_id' => ['required', 'integer', Rule::exists($tablePrefix.'provinces', 'id')],
            'city_id' => ['required', 'integer', Rule::exists($tablePrefix.'cities', 'id')],
            'district_id' => ['required', 'integer', Rule::exists($tablePrefix.'districts', 'id')],
            'courier' => ['required', 'string', Rule::in(config('services.rajaongkir.couriers', []))],
        ]);

        try {
            $rates = $shippingService->quoteForUser(
                $request->user(),
                $attributes['province_id'],
                $attributes['city_id'],
                $attributes['district_id'],
                $attributes['courier'],
            );
        } catch (ConnectionException|RuntimeException $exception) {
            report($exception);

            return response()->json(['message' => 'Ongkos kirim belum dapat dihitung. Silakan coba kembali.'], 422);
        }

        return response()->json(['rates' => $rates]);
    }
}
