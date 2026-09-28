<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Requests\Attachment\UpdateAttachmentRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
    }

    /**
     * Daftar lampiran berkas (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $attachableType = $request->input('attachable_type');
        $attachableId = $request->input('attachable_id') ? (int) $request->input('attachable_id') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $attachments = $this->attachmentService->list(
            attachableType: $attachableType,
            attachableId: $attachableId,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return AttachmentResource::collection($attachments)
            ->additional([
                'meta' => [
                    'total'        => $attachments->total(),
                    'per_page'     => $attachments->perPage(),
                    'current_page' => $attachments->currentPage(),
                    'last_page'    => $attachments->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Upload berkas lampiran baru.
     */
    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $attachment = $this->attachmentService->create(
            data: $request->validated(),
            file: $request->file('file'),
            uploader: $request->user()
        );

        return response()->json([
            'message' => 'Attachment uploaded successfully',
            'data'    => AttachmentResource::make($attachment)
        ], 201);
    }

    /**
     * Detail berkas lampiran.
     */
    public function show(int|string $id): JsonResponse
    {
        $attachment = $this->attachmentService->find((int) $id);

        return response()->json([
            'data' => AttachmentResource::make($attachment)
        ]);
    }

    /**
     * Memperbarui deskripsi lampiran.
     */
    public function update(UpdateAttachmentRequest $request, int|string $id): JsonResponse
    {
        $attachment = $this->attachmentService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Attachment updated successfully',
            'data'    => AttachmentResource::make($attachment)
        ]);
    }

    /**
     * Menghapus lampiran berkas dari storage & database.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->attachmentService->delete((int) $id);

        return response()->json([
            'message' => 'Attachment deleted successfully'
        ]);
    }
}
