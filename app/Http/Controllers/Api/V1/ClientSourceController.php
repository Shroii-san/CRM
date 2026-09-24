<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientSource\StoreClientSourceRequest;
use App\Http\Requests\ClientSource\UpdateClientSourceRequest;
use App\Http\Resources\Client\ClientSourceResource;
use App\Services\ClientSourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientSourceController extends Controller
{
    public function __construct(private ClientSourceService $sourceService)
    {
    }

    /**
     * Mendapatkan daftar sumber client.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $activeOnly = $request->boolean('active_only');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $sources = $this->sourceService->list($search, $activeOnly, $perPage, $page);

        return ClientSourceResource::collection($sources)
            ->additional([
                'meta' => [
                    'total' => $sources->total(),
                    'per_page' => $sources->perPage(),
                    'current_page' => $sources->currentPage(),
                    'last_page' => $sources->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat sumber client baru (Khusus Superadmin).
     */
    public function store(StoreClientSourceRequest $request): JsonResponse
    {
        $source = $this->sourceService->create($request->validated());

        return response()->json([
            'message' => 'Client source created successfully',
            'data' => ClientSourceResource::make($source)
        ], 201);
    }

    /**
     * Memperbarui sumber client (Khusus Superadmin).
     */
    public function update(UpdateClientSourceRequest $request, int|string $id): JsonResponse
    {
        $source = $this->sourceService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Client source updated successfully',
            'data' => ClientSourceResource::make($source)
        ]);
    }

    /**
     * Menghapus sumber client (Khusus Superadmin).
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->sourceService->delete((int) $id);

        return response()->json([
            'message' => 'Client source deleted successfully'
        ]);
    }
}
