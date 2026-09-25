<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\Note\NoteResource;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(private NoteService $noteService)
    {
    }

    /**
     * Daftar catatan (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $clientId = $request->input('client_id') ? (int) $request->input('client_id') : null;
        $dealId = $request->input('deal_id') ? (int) $request->input('deal_id') : null;
        $noteType = $request->input('note_type');
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $notes = $this->noteService->list(
            user: $user,
            clientId: $clientId,
            dealId: $dealId,
            noteType: $noteType,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return NoteResource::collection($notes)
            ->additional([
                'meta' => [
                    'total'        => $notes->total(),
                    'per_page'     => $notes->perPage(),
                    'current_page' => $notes->currentPage(),
                    'last_page'    => $notes->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat catatan baru.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = $this->noteService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Note created successfully',
            'data'    => NoteResource::make($note)
        ], 201);
    }

    /**
     * Detail catatan.
     */
    public function show(int|string $id): JsonResponse
    {
        $note = $this->noteService->find((int) $id);

        return response()->json([
            'data' => NoteResource::make($note)
        ]);
    }

    /**
     * Memperbarui catatan.
     */
    public function update(UpdateNoteRequest $request, int|string $id): JsonResponse
    {
        $note = $this->noteService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Note updated successfully',
            'data'    => NoteResource::make($note)
        ]);
    }

    /**
     * Menghapus catatan.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->noteService->delete((int) $id);

        return response()->json([
            'message' => 'Note deleted successfully'
        ]);
    }
}
