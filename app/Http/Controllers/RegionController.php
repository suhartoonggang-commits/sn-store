<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Laravolt\Indonesia\Facade as Indonesia;

class RegionController extends Controller
{
    public function provinces(): JsonResponse
    {
        return $this->regionsResponse(Indonesia::allProvinces());
    }

    public function cities(int $provinceId): JsonResponse
    {
        $province = Indonesia::findProvince($provinceId, ['cities']);
        abort_if($province === null, 404);

        return $this->regionsResponse($province->cities);
    }

    public function districts(int $cityId): JsonResponse
    {
        $city = Indonesia::findCity($cityId, ['districts']);
        abort_if($city === null, 404);

        return $this->regionsResponse($city->districts);
    }

    public function villages(int $districtId): JsonResponse
    {
        $district = Indonesia::findDistrict($districtId, ['villages']);
        abort_if($district === null, 404);

        return $this->regionsResponse($district->villages);
    }

    private function regionsResponse(Collection $regions): JsonResponse
    {
        return response()->json($regions
            ->sortBy('name')
            ->values()
            ->map(fn ($region): array => [
                'id' => $region->id,
                'name' => $region->name,
            ]));
    }
}
