<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Industry\StoreIndustryRequest;
use App\Http\Requests\Industry\UpdateIndustryRequest;
use App\Http\Resources\Organization\IndustryResource;
use App\Services\IndustryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function __construct(private IndustryService $industryService)
    {
    }

    /**
     * Mendapatkan daftar industri.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $activeOnly = $request->boolean('active_only');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $industries = $this->industryService->list($search, $activeOnly, $perPage, $page);

        return IndustryResource::collection($industries)
            ->additional([
                'meta' => [
                    'total' => $industries->total(),
                    'per_page' => $industries->perPage(),
                    'current_page' => $industries->currentPage(),
                    'last_page' => $industries->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat data industri baru (Khusus Superadmin).
     */
    public function store(StoreIndustryRequest $request): JsonResponse
    {
        $industry = $this->industryService->create($request->validated());

        return response()->json([
            'message' => 'Industry created successfully',
            'data' => IndustryResource::make($industry)
        ], 201);
    }

    /**
     * Memperbarui data industri (Khusus Superadmin).
     */
    public function update(UpdateIndustryRequest $request, int|string $id): JsonResponse
    {
        $industry = $this->industryService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Industry updated successfully',
            'data' => IndustryResource::make($industry)
        ]);
    }

    /**
     * Menghapus industri (Khusus Superadmin).
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->industryService->delete((int) $id);

        return response()->json([
            'message' => 'Industry deleted successfully'
        ]);
    }
}
