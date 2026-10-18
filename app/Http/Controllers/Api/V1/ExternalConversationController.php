<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalConversation\StoreExternalConversationRequest;
use App\Http\Requests\ExternalConversation\UpdateExternalConversationRequest;
use App\Http\Resources\ExternalConversation\ExternalConversationResource;
use App\Services\ExternalConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalConversationController extends Controller
{
    public function __construct(private ExternalConversationService $conversationService)
    {
    }

    /**
     * Daftar percakapan eksternal (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $clientId = $request->input('client_id') ? (int) $request->input('client_id') : null;
        $platform = $request->input('platform');
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $conversations = $this->conversationService->list(
            clientId: $clientId,
            platform: $platform,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return $this->paginatedResponse($conversations, ExternalConversationResource::class, 'External conversation list retrieved successfully');
    }

    /**
     * Membuat percakapan eksternal baru.
     */
    public function store(StoreExternalConversationRequest $request): JsonResponse
    {
        $conversation = $this->conversationService->create($request->validated());

        return $this->successResponse(
            ExternalConversationResource::make($conversation),
            'External conversation created successfully',
            201
        );
    }

    /**
     * Detail percakapan eksternal.
     */
    public function show(int|string $id): JsonResponse
    {
        $conversation = $this->conversationService->find((int) $id);

        return $this->successResponse(
            ExternalConversationResource::make($conversation),
            'External conversation detail retrieved successfully'
        );
    }

    /**
     * Memperbarui percakapan eksternal.
     */
    public function update(UpdateExternalConversationRequest $request, int|string $id): JsonResponse
    {
        $conversation = $this->conversationService->update((int) $id, $request->validated());

        return $this->successResponse(
            ExternalConversationResource::make($conversation),
            'External conversation updated successfully'
        );
    }

    /**
     * Menghapus percakapan eksternal.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->conversationService->delete((int) $id);

        return $this->successResponse(null, 'External conversation deleted successfully');
    }
}
