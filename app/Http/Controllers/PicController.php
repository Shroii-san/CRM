<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\OrganizationContactController as WebContactController;
use App\Http\Controllers\Api\V1\OrganizationContactController as ApiContactController;
use App\Http\Requests\OrganizationContact\StoreOrganizationContactRequest;
use App\Http\Requests\OrganizationContact\UpdateOrganizationContactRequest;
use App\Services\OrganizationContactService;
use Illuminate\Http\Request;

class PicController extends Controller
{
    public function __construct(
        private OrganizationContactService $contactService,
        private WebContactController $webContactController,
        private ApiContactController $apiContactController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search')) {
            return $this->apiContactController->index($request);
        }

        return $this->webContactController->index($request);
    }

    public function store(Request $request)
    {
        $this->normalizeLegacyInputs($request);
        $storeRequest = StoreOrganizationContactRequest::createFrom($request);

        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiContactController->store($storeRequest);
        }

        $this->contactService->create($storeRequest->validated());
        return redirect()->route('pic')->with('success', 'PIC berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $this->normalizeLegacyInputs($request);
        $updateRequest = UpdateOrganizationContactRequest::createFrom($request);

        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiContactController->update($updateRequest, (int) $id);
        }

        $this->contactService->update((int) $id, $updateRequest->validated());
        return redirect()->route('pic')->with('success', 'PIC berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->contactService->delete((int) $id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'PIC deleted successfully']);
        }

        return redirect()->route('pic')->with('success', 'PIC berhasil dihapus');
    }

    public function search(Request $request)
    {
        return $this->apiContactController->index($request);
    }

    public function getPICsByCompany($companyId)
    {
        return $this->apiContactController->byOrganization($companyId);
    }

    public function storePICAjax(Request $request)
    {
        $this->normalizeLegacyInputs($request);
        $storeRequest = StoreOrganizationContactRequest::createFrom($request);

        return $this->apiContactController->store($storeRequest);
    }

    private function normalizeLegacyInputs(Request $request): void
    {
        if ($request->has('company_id') && !$request->has('organization_id')) {
            $request->merge(['organization_id' => $request->input('company_id')]);
        }
        if ($request->has('pic_name') && !$request->has('name')) {
            $request->merge(['name' => $request->input('pic_name')]);
        }
        if ($request->has('pic_email') && !$request->has('email')) {
            $request->merge(['email' => $request->input('pic_email')]);
        }
        if ($request->has('pic_phone') && !$request->has('phone')) {
            $request->merge(['phone' => $request->input('pic_phone')]);
        }
        if ($request->has('position') && !$request->has('job_title')) {
            $request->merge(['job_title' => $request->input('position')]);
        }
    }
}
