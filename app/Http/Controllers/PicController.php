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

    public function store(StoreOrganizationContactRequest $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiContactController->store($request);
        }

        $this->contactService->create($request->validated());
        return redirect()->route('pic')->with('success', 'PIC berhasil ditambahkan');
    }

    public function update(UpdateOrganizationContactRequest $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiContactController->update($request, (int) $id);
        }

        $this->contactService->update((int) $id, $request->validated());
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

    public function storePICAjax(StoreOrganizationContactRequest $request)
    {
        return $this->apiContactController->store($request);
    }
}
