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

        return $this->paginatedResponse($sources, ClientSourceResource::class, 'Client sources retrieved successfully');
    }

    /**
     * Membuat sumber client baru (Khusus Superadmin).
     */
    public function store(StoreClientSourceRequest $request): JsonResponse
    {
        $source = $this->sourceService->create($request->validated());

        return $this->successResponse(ClientSourceResource::make($source), 'Client source created successfully', 201);
    }

    /**
     * Memperbarui sumber client (Khusus Superadmin).
     */
    public function update(UpdateClientSourceRequest $request, int|string $id): JsonResponse
    {
        $source = $this->sourceService->update((int) $id, $request->validated());

        return $this->successResponse(ClientSourceResource::make($source), 'Client source updated successfully');
    }

    /**
     * Menghapus sumber client (Khusus Superadmin).
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->sourceService->delete((int) $id);

        return $this->successResponse(null, 'Client source deleted successfully');
    }
}
