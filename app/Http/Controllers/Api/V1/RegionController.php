<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Region\RegionResource;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(private RegionService $regionService)
    {
    }

    /**
     * Mendapatkan daftar negara (JSON API).
     */
    public function countries(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $countries = $this->regionService->getCountries(search: $search);

        return $this->successResponse(
            RegionResource::collection($countries),
            'Countries retrieved successfully'
        );
    }

    /**
     * Mendapatkan daftar provinsi.
     */
    public function provinces(Request $request): JsonResponse
    {
        $countryId = $request->input('country_id') ? (int) $request->input('country_id') : null;
        $search = $request->input('search');

        $provinces = $this->regionService->getProvinces(countryId: $countryId, search: $search);

        return $this->successResponse(
            RegionResource::collection($provinces),
            'Provinces retrieved successfully'
        );
    }

    /**
     * Mendapatkan daftar kabupaten/kota berdasarkan provinsi.
     */
    public function regencies(Request $request, int|string $provinceId): JsonResponse
    {
        $search = $request->input('search');
        $regencies = $this->regionService->getRegencies(provinceId: (int) $provinceId, search: $search);

        return $this->successResponse(
            RegionResource::collection($regencies),
            'Regencies retrieved successfully'
        );
    }

    /**
     * Mendapatkan daftar kecamatan berdasarkan kabupaten/kota.
     */
    public function districts(Request $request, int|string $regencyId): JsonResponse
    {
        $search = $request->input('search');
        $districts = $this->regionService->getDistricts(regencyId: (int) $regencyId, search: $search);

        return $this->successResponse(
            RegionResource::collection($districts),
            'Districts retrieved successfully'
        );
    }

    /**
     * Mendapatkan daftar kelurahan/desa berdasarkan kecamatan.
     */
    public function villages(Request $request, int|string $districtId): JsonResponse
    {
        $search = $request->input('search');
        $villages = $this->regionService->getVillages(districtId: (int) $districtId, search: $search);

        return $this->successResponse(
            RegionResource::collection($villages),
            'Villages retrieved successfully'
        );
    }
}
