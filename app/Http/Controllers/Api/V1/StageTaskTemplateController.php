<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StageTaskTemplate\StoreStageTaskTemplateRequest;
use App\Http\Requests\StageTaskTemplate\UpdateStageTaskTemplateRequest;
use App\Http\Resources\StageTaskTemplate\StageTaskTemplateResource;
use App\Services\StageTaskTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StageTaskTemplateController extends Controller
{
    public function __construct(private StageTaskTemplateService $templateService)
    {
    }

    /**
     * Mendapatkan daftar template tugas per stage.
     */
    public function index(Request $request): JsonResponse
    {
        $stageId = $request->input('stage_id') ? (int) $request->input('stage_id') : null;
        $activeOnly = $request->boolean('active_only');

        $templates = $this->templateService->list(stageId: $stageId, activeOnly: $activeOnly);

        return $this->successResponse(StageTaskTemplateResource::collection($templates), 'Stage task templates retrieved successfully');
    }

    /**
     * Membuat template tugas stage baru.
     */
    public function store(StoreStageTaskTemplateRequest $request): JsonResponse
    {
        $template = $this->templateService->create($request->validated());

        return $this->successResponse(StageTaskTemplateResource::make($template), 'Stage task template created successfully', 201);
    }

    /**
     * Detail template tugas.
     */
    public function show(int|string $id): JsonResponse
    {
        $template = $this->templateService->find((int) $id);

        return $this->successResponse(StageTaskTemplateResource::make($template), 'Stage task template retrieved successfully');
    }

    /**
     * Memperbarui template tugas.
     */
    public function update(UpdateStageTaskTemplateRequest $request, int|string $id): JsonResponse
    {
        $template = $this->templateService->update((int) $id, $request->validated());

        return $this->successResponse(StageTaskTemplateResource::make($template), 'Stage task template updated successfully');
    }

    /**
     * Menghapus template tugas.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->templateService->delete((int) $id);

        return $this->successResponse(null, 'Stage task template deleted successfully');
    }
}
