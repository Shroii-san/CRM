<?php

namespace App\Http\Controllers;

use App\Http\Resources\Region\RegionResource;

use App\Services\RegionService;

class RegionController extends Controller
{

    public function getProvinces(RegionService $regionService)
    {
        $provinces = $regionService->getProvince();
        return RegionResource::collection($provinces);
    }

    public function getRegencies(RegionService $regionService, $provinceId)
    {
        $regencies = $regionService->getRegenciesByProvince($provinceId);
        return RegionResource::collection($regencies);
    }

    public function getDistricts(RegionService $regionService, $regencyId)
    {
        $districts = $regionService->getDistrictsByRegencies($regencyId);
        return RegionResource::collection($districts);
    }

    public function getVillages(RegionService $regionService, $districtId)
    {
        $villages = $regionService->getVillageByDistricts($districtId);
        return RegionResource::collection($villages);
    }
}
