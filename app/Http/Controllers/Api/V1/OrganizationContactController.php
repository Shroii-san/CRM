<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationContact\StoreOrganizationContactRequest;
use App\Http\Requests\OrganizationContact\UpdateOrganizationContactRequest;
use App\Http\Resources\OrganizationContact\OrganizationContactResource;
use App\Services\OrganizationContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationContactController extends Controller
{
    public function __construct(private OrganizationContactService $contactService)
    {
    }

    /**
     * Mendapatkan daftar kontak organisasi (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $organizationId = $request->input('organization_id') ? (int) $request->input('organization_id') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $contacts = $this->contactService->list($organizationId, $search, $perPage, $page);

        return $this->paginatedResponse($contacts, OrganizationContactResource::class, 'Organization contact list retrieved successfully');
    }

    /**
     * Membuat data kontak PIC organisasi baru.
     */
    public function store(StoreOrganizationContactRequest $request): JsonResponse
    {
        $contact = $this->contactService->create($request->validated());

        return $this->successResponse(
            OrganizationContactResource::make($contact),
            'Organization contact created successfully',
            201
        );
    }

    /**
     * Memperbarui data kontak PIC organisasi.
     */
    public function update(UpdateOrganizationContactRequest $request, int|string $id): JsonResponse
    {
        $contact = $this->contactService->update((int) $id, $request->validated());

        return $this->successResponse(
            OrganizationContactResource::make($contact),
            'Organization contact updated successfully'
        );
    }

    /**
     * Menghapus data kontak PIC organisasi.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->contactService->delete((int) $id);

        return $this->successResponse(null, 'Organization contact deleted successfully');
    }

    /**
     * Mendapatkan kontak PIC berdasarkan ID Organisasi.
     */
    public function byOrganization(int|string $organizationId): JsonResponse
    {
        $contacts = $this->contactService->list((int) $organizationId, null, 100, 1);

        return $this->successResponse(
            OrganizationContactResource::collection($contacts),
            'Organization contacts retrieved successfully'
        );
    }
}
