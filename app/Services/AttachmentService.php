<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    /**
     * Mendapatkan daftar lampiran berkas dengan filter dan pagination.
     */
    public function list(
        ?string $attachableType = null,
        ?int $attachableId = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Attachment::query()
            ->with(['uploader', 'attachable'])
            ->orderBy('created_at', 'desc');

        if ($attachableType) {
            $query->where('attachable_type', $attachableType);
        }

        if ($attachableId) {
            $query->where('attachable_id', $attachableId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail lampiran berkas berdasarkan ID.
     */
    public function find(int $id): Attachment
    {
        return Attachment::with(['uploader', 'attachable'])->findOrFail($id);
    }

    /**
     * Upload dan simpan berkas lampiran baru.
     */
    public function create(array $data, UploadedFile $file, User $uploader): Attachment
    {
        $path = $file->store('attachments', 'public');

        return Attachment::create([
            'file_name'         => $file->getClientOriginalName(),
            'description'       => $data['description'] ?? null,
            'mime_type'         => $file->getClientMimeType() ?? 'application/octet-stream',
            'file_size'         => $file->getSize(),
            'storage_reference' => $path,
            'uploaded_by'       => $uploader->id,
            'attachable_type'   => $data['attachable_type'],
            'attachable_id'     => $data['attachable_id'],
        ]);
    }

    /**
     * Memperbarui deskripsi lampiran berkas.
     */
    public function update(int $id, array $data): Attachment
    {
        $attachment = Attachment::findOrFail($id);

        $updateData = [];
        if (array_key_exists('description', $data)) $updateData['description'] = $data['description'];

        if (!empty($updateData)) {
            $attachment->update($updateData);
        }

        return $attachment->refresh();
    }

    /**
     * Menghapus lampiran berkas dari storage dan basis data.
     */
    public function delete(int $id): void
    {
        $attachment = Attachment::findOrFail($id);

        if (Storage::disk('public')->exists($attachment->storage_reference)) {
            Storage::disk('public')->delete($attachment->storage_reference);
        }

        $attachment->delete();
    }
}
