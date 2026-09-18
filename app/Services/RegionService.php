<?php

namespace App\Services;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class RegionService
{
    public function getProvince()
    {
        return Province::orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function getRegenciesByProvince($provinceId)
    {

        $province = Province::findOrFail($provinceId);

        return Regency::where('province_id', $provinceId)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'province_id')
            ->get();
    }

    public function getDistrictsByRegencies($regencyId)
    {

        $regency = Regency::findOrFail($regencyId);

        return District::where('regency_id', $regencyId)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'regency_id')
            ->get();
        ;
    }

    public function getVillageByDistricts($districtId)
    {
        $district = District::findOrFail($districtId);

        return Village::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'district_id')
            ->get();

    }
}
