<?php

namespace App\Services;

use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;

class RegionService
{
    /**
     * Mendapatkan daftar negara.
     */
    public function getCountries(?string $search = null)
    {
        $query = Country::query()->orderBy('name', 'asc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }

    /**
     * Mendapatkan daftar provinsi.
     */
    public function getProvinces(?int $countryId = null, ?string $search = null)
    {
        $query = Province::query()->orderBy('name', 'asc');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }

    /**
     * Mendapatkan daftar kabupaten/kota berdasarkan provinsi.
     */
    public function getRegencies(?int $provinceId = null, ?string $search = null)
    {
        $query = Regency::query()->orderBy('name', 'asc');

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }

    /**
     * Mendapatkan daftar kecamatan berdasarkan kabupaten/kota.
     */
    public function getDistricts(?int $regencyId = null, ?string $search = null)
    {
        $query = District::query()->orderBy('name', 'asc');

        if ($regencyId) {
            $query->where('regency_id', $regencyId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }

    /**
     * Mendapatkan daftar kelurahan/desa berdasarkan kecamatan.
     */
    public function getVillages(?int $districtId = null, ?string $search = null)
    {
        $query = Village::query()->orderBy('name', 'asc');

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }
}
