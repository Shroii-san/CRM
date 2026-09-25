<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interaction\StoreInteractionRequest;
use App\Http\Requests\Interaction\UpdateInteractionRequest;
use App\Http\Resources\Interaction\InteractionResource;
use App\Services\InteractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function __construct(private InteractionService $interactionService)
    {
    }

    /**
     * Daftar interaksi (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $clientId = $request->input('client_id') ? (int) $request->input('client_id') : null;
        $dealId = $request->input('deal_id') ? (int) $request->input('deal_id') : null;
        $contactId = $request->input('organization_contact_id') ? (int) $request->input('organization_contact_id') : null;
        $type = $request->has('type') ? (int) $request->input('type') : null;
        $status = $request->has('status') ? (int) $request->input('status') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $interactions = $this->interactionService->list(
            user: $user,
            clientId: $clientId,
            dealId: $dealId,
            contactId: $contactId,
            type: $type,
            status: $status,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return InteractionResource::collection($interactions)
            ->additional([
                'meta' => [
                    'total'        => $interactions->total(),
                    'per_page'     => $interactions->perPage(),
                    'current_page' => $interactions->currentPage(),
                    'last_page'    => $interactions->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat interaksi baru.
     */
    public function store(StoreInteractionRequest $request): JsonResponse
    {
        $interaction = $this->interactionService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Interaction created successfully',
            'data'    => InteractionResource::make($interaction)
        ], 201);
    }

    /**
     * Detail interaksi.
     */
    public function show(int|string $id): JsonResponse
    {
        $interaction = $this->interactionService->find((int) $id);

        return response()->json([
            'data' => InteractionResource::make($interaction)
        ]);
    }

    /**
     * Memperbarui interaksi.
     */
    public function update(UpdateInteractionRequest $request, int|string $id): JsonResponse
    {
        $interaction = $this->interactionService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Interaction updated successfully',
            'data'    => InteractionResource::make($interaction)
        ]);
    }

    /**
     * Menghapus interaksi.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->interactionService->delete((int) $id);

        return response()->json([
            'message' => 'Interaction deleted successfully'
        ]);
    }
}
