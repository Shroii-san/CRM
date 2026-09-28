<?php

namespace App\Http\Controllers;

use App\Http\Resources\Region\RegionResource;
use App\Services\RegionService;

class RegionController extends Controller
{
    public function __construct(private RegionService $regionService)
    {
    }

    public function getProvinces()
    {
        $provinces = $this->regionService->getProvinces();
        return RegionResource::collection($provinces);
    }

    public function getRegencies($provinceId)
    {
        $regencies = $this->regionService->getRegencies((int) $provinceId);
        return RegionResource::collection($regencies);
    }

    public function getDistricts($regencyId)
    {
        $districts = $this->regionService->getDistricts((int) $regencyId);
        return RegionResource::collection($districts);
    }

    public function getVillages($districtId)
    {
        $villages = $this->regionService->getVillages((int) $districtId);
        return RegionResource::collection($villages);
    }
}
