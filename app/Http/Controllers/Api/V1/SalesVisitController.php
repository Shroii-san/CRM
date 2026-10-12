<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesVisit\StoreSalesVisitRequest;
use App\Http\Requests\SalesVisit\UpdateSalesVisitRequest;
use App\Http\Resources\SalesVisit\SalesVisitResource;
use App\Services\SalesVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesVisitController extends Controller
{
    public function __construct(private SalesVisitService $visitService)
    {
    }

    /**
     * Mendapatkan daftar kunjungan sales (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $salesId = $request->input('sales_id') ? (int) $request->input('sales_id') : null;
        $organizationId = $request->input('organization_id') ? (int) $request->input('organization_id') : null;
        $isFollowUp = $request->has('is_follow_up') ? $request->boolean('is_follow_up') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $visits = $this->visitService->list(
            user: $user,
            salesId: $salesId,
            organizationId: $organizationId,
            isFollowUp: $isFollowUp,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return $this->paginatedResponse($visits, SalesVisitResource::class, 'Sales visit list retrieved successfully');
    }

    /**
     * Membuat kunjungan sales baru.
     */
    public function store(StoreSalesVisitRequest $request): JsonResponse
    {
        $visit = $this->visitService->create($request->validated(), $request->user());

        return $this->successResponse(
            SalesVisitResource::make($visit),
            'Sales visit created successfully',
            201
        );
    }

    /**
     * Detail kunjungan sales.
     */
    public function show(int|string $id): JsonResponse
    {
        $visit = $this->visitService->find((int) $id);

        return $this->successResponse(
            SalesVisitResource::make($visit),
            'Sales visit detail retrieved successfully'
        );
    }

    /**
     * Memperbarui kunjungan sales.
     */
    public function update(UpdateSalesVisitRequest $request, int|string $id): JsonResponse
    {
        $visit = $this->visitService->update((int) $id, $request->validated());

        return $this->successResponse(
            SalesVisitResource::make($visit),
            'Sales visit updated successfully'
        );
    }

    /**
     * Menghapus kunjungan sales.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->visitService->delete((int) $id);

        return $this->successResponse(null, 'Sales visit deleted successfully');
    }
}
