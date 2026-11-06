<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Facade as Indonesia;
use RuntimeException;

class RajaOngkirShippingService
{
    /**
     * @return array<int, array{key: string, courier_code: string, courier_name: string, service: string, description: string, cost: int, etd: string}>
     */
    public function quoteForUser(User $user, int|string $provinceId, int|string $cityId, int|string $districtId, string $courier): array
    {
        $regions = $this->resolveSelectedRegion($provinceId, $cityId, $districtId);
        $weight = $user->cartItems()->sum('quantity') * max(1, (int) config('services.rajaongkir.default_item_weight_grams', 1000));

        return $this->quote($regions['province'], $regions['city'], $regions['district'], $weight, $courier);
    }

    /**
     * @param  array{name: string, code: string}  $province
     * @param  array{name: string, code: string}  $city
     * @param  array{name: string, code: string}  $district
     * @return array<int, array{key: string, courier_code: string, courier_name: string, service: string, description: string, cost: int, etd: string}>
     */
    public function quote(array $province, array $city, array $district, int $weight, string $courier): array
    {
        $courier = strtolower($courier);

        if (! in_array($courier, config('services.rajaongkir.couriers', []), true)) {
            throw ValidationException::withMessages(['courier' => 'Kurir yang dipilih tidak tersedia.']);
        }

        $originId = $this->districtId(
            (string) config('services.rajaongkir.origin_province', 'Sulawesi Selatan'),
            (string) config('services.rajaongkir.origin_city', 'Makassar'),
            (string) config('services.rajaongkir.origin_district', 'Makassar'),
        );
        $destinationId = $this->districtId($province['name'], $city['name'], $district['name']);

        $response = $this->request('POST', '/calculate/district/domestic-cost', [
            'origin' => $originId,
            'destination' => $destinationId,
            'weight' => max(1, $weight),
            'courier' => $courier,
            'price' => 'lowest',
        ]);

        return $this->normalizeRates($response['data'] ?? [], $courier);
    }

    /**
     * @return array{province: array{name: string, code: string}, city: array{name: string, code: string}, district: array{name: string, code: string}}
     */
    public function resolveSelectedRegion(int|string $provinceId, int|string $cityId, int|string $districtId): array
    {
        $province = Indonesia::findProvince($provinceId);
        $city = Indonesia::findCity($cityId);
        $district = Indonesia::findDistrict($districtId);

        if (! $province || ! $city || ! $district || $city->province_code !== $province->code || $district->city_code !== $city->code) {
            throw ValidationException::withMessages(['district_id' => 'Wilayah pengiriman yang dipilih tidak valid.']);
        }

        return [
            'province' => ['name' => $province->name, 'code' => $province->code],
            'city' => ['name' => $city->name, 'code' => $city->code],
            'district' => ['name' => $district->name, 'code' => $district->code],
        ];
    }

    private function districtId(string $provinceName, string $cityName, string $districtName): string
    {
        $province = $this->matchLocation(
            $this->cachedLocations('provinces', '/destination/province'),
            $provinceName,
            'provinsi',
        );
        $cities = $this->cachedLocations('cities-'.$province['id'], '/destination/city/'.$province['id']);
        $city = $this->matchLocation($cities, $cityName, 'kota/kabupaten');
        $districts = $this->cachedLocations('districts-'.$city['id'], '/destination/district/'.$city['id']);
        $district = $this->matchLocation($districts, $districtName, 'kecamatan');

        return (string) $district['id'];
    }

    /**
     * @return array<int, array{id: int|string, name: string, zip_code?: string}>
     */
    private function cachedLocations(string $cacheKey, string $path): array
    {
        return Cache::remember('rajaongkir.locations.'.$cacheKey, now()->addDay(), function () use ($path): array {
            $response = $this->request('GET', $path);
            $data = $response['data'] ?? null;

            if (! is_array($data)) {
                throw new RuntimeException('Respons lokasi RajaOngkir tidak valid.');
            }

            return $data;
        });
    }

    /**
     * @param  array<int, array{id: int|string, name: string}>  $locations
     * @return array{id: int|string, name: string}
     */
    private function matchLocation(array $locations, string $name, string $level): array
    {
        $normalizedName = $this->normalizeName($name);

        foreach ($locations as $location) {
            if ($this->normalizeName((string) $location['name']) === $normalizedName) {
                return $location;
            }
        }

        throw new RuntimeException("{$level} pengiriman tidak tersedia di RajaOngkir: {$name}.");
    }

    private function normalizeName(string $name): string
    {
        $name = Str::upper(Str::ascii($name));
        $name = preg_replace('/\s*\([^)]*\)/', '', $name) ?? $name;
        $name = preg_replace('/^(KABUPATEN|KAB\.?|KOTA|KECAMATAN)\s+/', '', $name) ?? $name;
        $name = preg_replace('/\s+/', ' ', trim($name)) ?? trim($name);

        return match ($name) {
            'NANGGROE ACEH DARUSSALAM', 'ACEH DARUSSALAM' => 'ACEH',
            'DAERAH KHUSUS IBUKOTA JAKARTA' => 'DKI JAKARTA',
            'DAERAH ISTIMEWA YOGYAKARTA' => 'DI YOGYAKARTA',
            default => $name,
        };
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, ?array $payload = null): array
    {
        $apiKey = config('services.rajaongkir.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('RajaOngkir API key belum dikonfigurasi.');
        }

        $url = rtrim((string) config('services.rajaongkir.base_url'), '/').$path;
        $request = Http::withHeaders(['key' => $apiKey])->timeout(15);
        $response = $method === 'POST'
            ? $request->asForm()->post($url, $payload ?? [])
            : $request->get($url);

        if ($response->failed() || data_get($response->json(), 'meta.status') === 'error') {
            throw new RuntimeException('RajaOngkir gagal menghitung ongkos kirim.');
        }

        return $response->json() ?? [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     * @return array<int, array{key: string, courier_code: string, courier_name: string, service: string, description: string, cost: int, etd: string}>
     */
    private function normalizeRates(array $data, string $requestedCourier): array
    {
        $rates = [];

        foreach ($data as $row) {
            $courierCode = strtolower((string) ($row['code'] ?? $row['courier_code'] ?? $requestedCourier));
            $courierName = (string) ($row['name'] ?? $row['courier_name'] ?? strtoupper($courierCode));
            $options = $row['costs'] ?? [$row];

            foreach ($options as $option) {
                $cost = $option['cost'] ?? null;
                if (is_array($cost)) {
                    $cost = $cost[0]['value'] ?? $cost[0]['cost'] ?? null;
                }

                if (! is_numeric($cost)) {
                    continue;
                }

                $service = (string) ($option['service'] ?? $option['service_code'] ?? '');
                if ($service === '') {
                    continue;
                }

                $rates[] = [
                    'key' => $courierCode.'|'.$service,
                    'courier_code' => $courierCode,
                    'courier_name' => $courierName,
                    'service' => $service,
                    'description' => (string) ($option['description'] ?? $option['service_description'] ?? $service),
                    'cost' => (int) round((float) $cost),
                    'etd' => (string) ($option['etd'] ?? $option['estimate'] ?? ''),
                ];
            }
        }

        return $rates;
    }
}