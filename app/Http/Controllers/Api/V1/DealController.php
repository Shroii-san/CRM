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

        return DealResource::collection($deals)
            ->additional([
                'meta' => [
                    'total'        => $deals->total(),
                    'per_page'     => $deals->perPage(),
                    'current_page' => $deals->currentPage(),
                    'last_page'    => $deals->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat data deal baru.
     */
    public function store(StoreDealRequest $request): JsonResponse
    {
        $deal = $this->dealService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Deal created successfully',
            'data'    => DealResource::make($deal)
        ], 201);
    }

    /**
     * Detail deal.
     */
    public function show(int|string $id): JsonResponse
    {
        $deal = $this->dealService->find((int) $id);

        return response()->json([
            'data' => DealResource::make($deal)
        ]);
    }

    /**
     * Memperbarui data deal.
     */
    public function update(UpdateDealRequest $request, int|string $id): JsonResponse
    {
        $deal = $this->dealService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Deal updated successfully',
            'data'    => DealResource::make($deal)
        ]);
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

        return response()->json([
            'message' => 'Deal stage moved successfully',
            'data'    => DealResource::make($deal)
        ]);
    }

    /**
     * Menghapus deal.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->dealService->delete((int) $id);

        return response()->json([
            'message' => 'Deal deleted successfully'
        ]);
    }
}
