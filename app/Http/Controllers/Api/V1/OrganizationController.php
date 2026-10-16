<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Services\organization\OrganizationService;
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
        // Collect filters - hanya yang ada di filterDefinitions
        $filters = array_filter([
            'industry_id' => $request->filled('industry_id')
                ? (int) $request->input('industry_id')
                : null,
            'tier' => $request->input('tier'),
            'is_active' => $request->filled('is_active')
                ? ($request->boolean('is_active') ? 1 : 0)
                : null,
        ], fn($val) => $val !== null && $val !== '');

        $organizations = $this->organizationService->list(
            search: $request->input('search'),
            filters: $filters,
            perPage: (int) $request->input('per_page', 50),
            page: (int) $request->input('page', 1),
            sortBy: $request->input('sort_by', 'id'),
            sortDir: $request->input('sort_dir', 'asc'),
        );

        return $this->paginatedResponse($organizations, OrganizationResource::class, 'Organization list retrieved successfully');
    }

    /**
     * Mendapatkan detail satu organisasi (JSON API).
     */
    public function show(int|string $id): JsonResponse
    {
        $organization = $this->organizationService->findWithDetails((int) $id);

        return $this->successResponse(
            OrganizationResource::make($organization),
            'Organization details retrieved successfully'
        );
    }

    public function filterOptions(): JsonResponse
    {
        return response()->json(
            $this->organizationService->getFilterOptions()
        );
    }

    /**
     * Membuat data organisasi baru.
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = $this->organizationService->create(
            $request->validated(),
            $request->file('logo'),
            $request->user()
        );

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
        $organization = $this->organizationService->update(
            (int) $id,
            $request->validated(),
            $request->file('logo'),
            $request->user()
        );

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
            search: $request->input('search'),
            perPage: 100
        );

        return $this->successResponse(
            OrganizationResource::collection($organizations),
            'Organization dropdown options retrieved successfully'
        );
    }
}
