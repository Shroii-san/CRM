<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\OrganizationController as WebOrganizationController;
use App\Http\Controllers\Api\V1\OrganizationController as ApiOrganizationController;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Services\OrganizationService;
use App\Services\RegionService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private OrganizationService $organizationService,
        private RegionService $regionService,
        private WebOrganizationController $webOrganizationController,
        private ApiOrganizationController $apiOrganizationController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search')) {
            return $this->apiOrganizationController->index($request);
        }

        return $this->webOrganizationController->index($request);
    }

    public function store(StoreOrganizationRequest $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiOrganizationController->store($request);
        }

        $this->organizationService->create($request->validated());
        return redirect()->route('company')->with('success', 'Organisasi berhasil ditambahkan');
    }

    public function show($id)
    {
        return $this->webOrganizationController->show($id);
    }

    public function update(UpdateOrganizationRequest $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiOrganizationController->update($request, (int) $id);
        }

        $this->organizationService->update((int) $id, $request->validated());
        return redirect()->route('company')->with('success', 'Organisasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->organizationService->delete((int) $id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Organization deleted successfully']);
        }

        return redirect()->route('company')->with('success', 'Organisasi berhasil dihapus');
    }

    public function search(Request $request)
    {
        return $this->apiOrganizationController->index($request);
    }

    public function getCompaniesForDropdown(Request $request)
    {
        return $this->apiOrganizationController->dropdown($request);
    }

    public function storeCompanyAjax(StoreOrganizationRequest $request)
    {
        return $this->apiOrganizationController->store($request);
    }

    // Proxy Region Wilayah
    public function getRegencies($provinceId)
    {
        $regencies = $this->regionService->getRegenciesByProvince($provinceId);
        return response()->json(['success' => true, 'data' => $regencies]);
    }

    public function getDistricts($regencyId)
    {
        $districts = $this->regionService->getDistrictsByRegencies($regencyId);
        return response()->json(['success' => true, 'data' => $districts]);
    }

    public function getVillages($districtId)
    {
        $villages = $this->regionService->getVillageByDistricts($districtId);
        return response()->json(['success' => true, 'data' => $villages]);
    }
}