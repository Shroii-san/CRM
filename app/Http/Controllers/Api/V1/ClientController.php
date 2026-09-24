<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\Client\ClientResource;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService)
    {
    }

    /**
     * Mendapatkan daftar client prospect (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $search = $request->input('search');
        $sourceId = $request->input('source_id') ? (int) $request->input('source_id') : null;
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $clients = $this->clientService->list(
            user: $user,
            search: $search,
            sourceId: $sourceId,
            isActive: $isActive,
            perPage: $perPage,
            page: $page
        );

        return ClientResource::collection($clients)
            ->additional([
                'meta' => [
                    'total' => $clients->total(),
                    'per_page' => $clients->perPage(),
                    'current_page' => $clients->currentPage(),
                    'last_page' => $clients->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat data client baru.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->clientService->create($request->validated());

        return response()->json([
            'message' => 'Client created successfully',
            'data' => ClientResource::make($client)
        ], 201);
    }

    /**
     * Memperbarui data client.
     */
    public function update(UpdateClientRequest $request, int|string $id): JsonResponse
    {
        $client = $this->clientService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => ClientResource::make($client)
        ]);
    }

    /**
     * Menghapus data client.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->clientService->delete((int) $id);

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }

    /**
     * Menghapus banyak data client sekaligus (Bulk Delete).
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:clients,id',
        ]);

        $this->clientService->bulkDelete($request->input('ids'));

        return response()->json([
            'message' => 'Clients bulk deleted successfully'
        ]);
    }
}
