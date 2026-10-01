<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pipeline\StorePipelineRequest;
use App\Http\Requests\Pipeline\StorePipelineStageRequest;
use App\Http\Requests\Pipeline\UpdatePipelineRequest;
use App\Http\Requests\Pipeline\UpdatePipelineStageRequest;
use App\Http\Resources\Pipeline\PipelineResource;
use App\Http\Resources\Pipeline\PipelineStageResource;
use App\Services\PipelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function __construct(private PipelineService $pipelineService)
    {
    }

    /**
     * Mendapatkan daftar pipeline beserta stage-nya.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $activeOnly = $request->boolean('active_only');

        $pipelines = $this->pipelineService->list(search: $search, activeOnly: $activeOnly);

        return $this->successResponse(PipelineResource::collection($pipelines), 'Pipelines retrieved successfully');
    }

    /**
     * Membuat pipeline baru.
     */
    public function store(StorePipelineRequest $request): JsonResponse
    {
        $pipeline = $this->pipelineService->create($request->validated());

        return $this->successResponse(PipelineResource::make($pipeline), 'Pipeline created successfully', 201);
    }

    /**
     * Detail pipeline.
     */
    public function show(int|string $id): JsonResponse
    {
        $pipeline = $this->pipelineService->find((int) $id);

        return $this->successResponse(PipelineResource::make($pipeline), 'Pipeline retrieved successfully');
    }

    /**
     * Memperbarui pipeline.
     */
    public function update(UpdatePipelineRequest $request, int|string $id): JsonResponse
    {
        $pipeline = $this->pipelineService->update((int) $id, $request->validated());

        return $this->successResponse(PipelineResource::make($pipeline), 'Pipeline updated successfully');
    }

    /**
     * Menghapus pipeline.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->pipelineService->delete((int) $id);

        return $this->successResponse(null, 'Pipeline deleted successfully');
    }

    /**
     * Menambahkan stage baru ke pipeline.
     */
    public function storeStage(StorePipelineStageRequest $request, int|string $pipelineId): JsonResponse
    {
        $stage = $this->pipelineService->createStage((int) $pipelineId, $request->validated());

        return $this->successResponse(PipelineStageResource::make($stage), 'Pipeline stage created successfully', 201);
    }

    /**
     * Memperbarui stage.
     */
    public function updateStage(UpdatePipelineStageRequest $request, int|string $stageId): JsonResponse
    {
        $stage = $this->pipelineService->updateStage((int) $stageId, $request->validated());

        return $this->successResponse(PipelineStageResource::make($stage), 'Pipeline stage updated successfully');
    }

    /**
     * Menghapus stage.
     */
    public function destroyStage(int|string $stageId): JsonResponse
    {
        $this->pipelineService->deleteStage((int) $stageId);

        return $this->successResponse(null, 'Pipeline stage deleted successfully');
    }

    /**
     * Mengatur ulang posisi stage.
     */
    public function reorderStages(Request $request, int|string $pipelineId): JsonResponse
    {
        $request->validate([
            'stages'            => 'required|array',
            'stages.*.id'       => 'required|exists:pipeline_stages,id',
            'stages.*.position' => 'required|integer',
        ]);

        $this->pipelineService->reorderStages((int) $pipelineId, $request->input('stages'));

        return $this->successResponse(null, 'Pipeline stages reordered successfully');
    }
}
