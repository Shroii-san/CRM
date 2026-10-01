<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Deal\MoveDealStageRequest;
use App\Http\Requests\Deal\StoreDealRequest;
use App\Http\Requests\Deal\UpdateDealRequest;
use App\Http\Resources\Deal\DealResource;
use App\Services\DealService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function __construct(private DealService $dealService)
    {
    }

    /**
     * Mendapatkan daftar deal/transaksi (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $pipelineId = $request->input('pipeline_id') ? (int) $request->input('pipeline_id') : null;
        $stageId = $request->input('stage_id') ? (int) $request->input('stage_id') : null;
        $clientId = $request->input('client_id') ? (int) $request->input('client_id') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $deals = $this->dealService->list(
            user: $user,
            pipelineId: $pipelineId,
            stageId: $stageId,
            clientId: $clientId,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return $this->paginatedResponse($deals, DealResource::class, 'Deal list retrieved successfully');
    }

    /**
     * Membuat data deal baru.
     */
    public function store(StoreDealRequest $request): JsonResponse
    {
        $deal = $this->dealService->create($request->validated(), $request->user());

        return $this->successResponse(
            DealResource::make($deal),
            'Deal created successfully',
            201
        );
    }

    /**
     * Detail deal.
     */
    public function show(int|string $id): JsonResponse
    {
        $deal = $this->dealService->find((int) $id);

        return $this->successResponse(
            DealResource::make($deal),
            'Deal detail retrieved successfully'
        );
    }

    /**
     * Memperbarui data deal.
     */
    public function update(UpdateDealRequest $request, int|string $id): JsonResponse
    {
        $deal = $this->dealService->update((int) $id, $request->validated());

        return $this->successResponse(
            DealResource::make($deal),
            'Deal updated successfully'
        );
    }

    /**
     * Memindahkan stage deal.
     */
    public function moveStage(MoveDealStageRequest $request, int|string $id): JsonResponse
    {
        $deal = $this->dealService->moveStage(
            dealId: (int) $id,
            newStageId: (int) $request->input('current_stage_id'),
            changedBy: $request->user()
        );

        return $this->successResponse(
            DealResource::make($deal),
            'Deal stage moved successfully'
        );
    }

    /**
     * Menghapus deal.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->dealService->delete((int) $id);

        return $this->successResponse(null, 'Deal deleted successfully');
    }
}
