<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationService $organizationService)
    {
    }

    /**
     * Mendapatkan daftar organisasi murni core (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $search = $request->input('search');
        $industryId = $request->input('industry_id') ? (int) $request->input('industry_id') : null;
        $tier = $request->input('tier');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $organizations = $this->organizationService->list(
            user: $user,
            search: $search,
            industryId: $industryId,
            tier: $tier,
            isActive: $isActive,
            perPage: $perPage,
            page: $page
        );

        return $this->paginatedResponse($organizations, OrganizationResource::class, 'Organization list retrieved successfully');
    }

    /**
     * Membuat data organisasi baru.
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = $this->organizationService->create($request->validated());

        return $this->successResponse(
            OrganizationResource::make($organization),
            'Organization created successfully',
            201
        );
    }

    /**
     * Memperbarui data organisasi.
     */
    public function update(UpdateOrganizationRequest $request, int|string $id): JsonResponse
    {
        $organization = $this->organizationService->update((int) $id, $request->validated());

        return $this->successResponse(
            OrganizationResource::make($organization),
            'Organization updated successfully'
        );
    }

    /**
     * Menghapus organisasi.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->organizationService->delete((int) $id);

        return $this->successResponse(null, 'Organization deleted successfully');
    }

    /**
     * Endpoint dropdown sederhana untuk pencarian organisasi.
     */
    public function dropdown(Request $request): JsonResponse
    {
        $organizations = $this->organizationService->list(
            user: $request->user(),
            search: $request->input('search'),
            perPage: 100
        );

        return $this->successResponse(
            OrganizationResource::collection($organizations),
            'Organization dropdown options retrieved successfully'
        );
    }
}
