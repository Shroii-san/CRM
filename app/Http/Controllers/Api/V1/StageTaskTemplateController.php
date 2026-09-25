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

        return StageTaskTemplateResource::collection($templates)->response();
    }

    /**
     * Membuat template tugas stage baru.
     */
    public function store(StoreStageTaskTemplateRequest $request): JsonResponse
    {
        $template = $this->templateService->create($request->validated());

        return response()->json([
            'message' => 'Stage task template created successfully',
            'data'    => StageTaskTemplateResource::make($template)
        ], 201);
    }

    /**
     * Detail template tugas.
     */
    public function show(int|string $id): JsonResponse
    {
        $template = $this->templateService->find((int) $id);

        return response()->json([
            'data' => StageTaskTemplateResource::make($template)
        ]);
    }

    /**
     * Memperbarui template tugas.
     */
    public function update(UpdateStageTaskTemplateRequest $request, int|string $id): JsonResponse
    {
        $template = $this->templateService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Stage task template updated successfully',
            'data'    => StageTaskTemplateResource::make($template)
        ]);
    }

    /**
     * Menghapus template tugas.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->templateService->delete((int) $id);

        return response()->json([
            'message' => 'Stage task template deleted successfully'
        ]);
    }
}
