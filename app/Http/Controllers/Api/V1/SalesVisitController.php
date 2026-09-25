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

        return SalesVisitResource::collection($visits)
            ->additional([
                'meta' => [
                    'total'        => $visits->total(),
                    'per_page'     => $visits->perPage(),
                    'current_page' => $visits->currentPage(),
                    'last_page'    => $visits->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat kunjungan sales baru.
     */
    public function store(StoreSalesVisitRequest $request): JsonResponse
    {
        $visit = $this->visitService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Sales visit created successfully',
            'data'    => SalesVisitResource::make($visit)
        ], 201);
    }

    /**
     * Detail kunjungan sales.
     */
    public function show(int|string $id): JsonResponse
    {
        $visit = $this->visitService->find((int) $id);

        return response()->json([
            'data' => SalesVisitResource::make($visit)
        ]);
    }

    /**
     * Memperbarui kunjungan sales.
     */
    public function update(UpdateSalesVisitRequest $request, int|string $id): JsonResponse
    {
        $visit = $this->visitService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Sales visit updated successfully',
            'data'    => SalesVisitResource::make($visit)
        ]);
    }

    /**
     * Menghapus kunjungan sales.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->visitService->delete((int) $id);

        return response()->json([
            'message' => 'Sales visit deleted successfully'
        ]);
    }
}
