<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    /**
     * Mendapatkan daftar lampiran berkas dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?string $attachableType = null,
        ?int $attachableId = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'attachable_type' => $attachableType,
            'attachable_id'   => $attachableId,
            'search'          => $search,
            'sort_by'         => $sortBy,
            'sort_dir'        => $sortDir,
        ], fn ($val) => $val !== null);

        return Attachment::query()
            ->with(['uploader', 'attachable'])
            ->applyFilters(
                params: $params,
                searchable: ['file_name', 'description', 'mime_type'],
                filterable: ['attachable_type', 'attachable_id', 'uploaded_by'],
                sortable: ['id', 'file_name', 'file_size', 'created_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail lampiran berkas berdasarkan ID.
     */
    public function find(int $id): Attachment
    {
        return Attachment::with(['uploader', 'attachable'])->findOrFail($id);
    }

    /**
     * Upload dan simpan berkas lampiran baru dengan kompresi otomatis untuk gambar.
     */
    public function create(array $data, UploadedFile $file, User $uploader): Attachment
    {
        $mime = $file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream';
        $isImage = in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);

        // Kompresi otomatis jika berkas merupakan gambar
        $storedData = $isImage
            ? $this->compressAndStoreImage($file, $mime)
            : $this->storeOriginalFile($file);

        // Tentukan attachment_type: gunakan input data jika ada, atau default 'image'/'file'
        $attachmentType = $data['attachment_type'] ?? ($isImage ? 'image' : 'file');

        return Attachment::create([
            'file_name'         => $file->getClientOriginalName(),
            'description'       => $data['description'] ?? null,
            'attachment_type'   => $attachmentType,
            'mime_type'         => $storedData['mime_type'],
            'file_size'         => $storedData['file_size'],
            'storage_reference' => $storedData['path'],
            'uploaded_by'       => $uploader->id,
            'attachable_type'   => $data['attachable_type'],
            'attachable_id'     => $data['attachable_id'],
        ]);
    }

    /**
     * Kompresi gambar in-memory menggunakan GD dan simpan ke public storage.
     */
    protected function compressAndStoreImage(UploadedFile $file, string $mime): array
    {
        $originalSize = $file->getSize();
        $realPath = $file->getRealPath();

        if ($realPath && file_exists($realPath)) {
            $content = file_get_contents($realPath);
            $image = @imagecreatefromstring($content);

            if ($image !== false) {
                $ext = match ($mime) {
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif',
                    default      => 'jpg',
                };

                ob_start();
                if ($ext === 'png') {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, null, 6);
                } elseif ($ext === 'webp' && function_exists('imagewebp')) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagewebp($image, null, 80);
                } elseif ($ext === 'gif') {
                    imagegif($image);
                } else {
                    imagejpeg($image, null, 80);
                }
                $compressedContent = ob_get_clean();
                imagedestroy($image);

                if ($compressedContent !== false && strlen($compressedContent) > 0) {
                    $compressedSize = strlen($compressedContent);

                    // Simpan hasil kompresi jika tidak lebih besar dari file asli
                    if ($compressedSize <= $originalSize || $originalSize === 0) {
                        $filename = \Illuminate\Support\Str::random(40) . '.' . $ext;
                        $path = 'attachments/' . $filename;
                        Storage::disk('public')->put($path, $compressedContent);

                        return [
                            'path'      => $path,
                            'file_size' => $compressedSize,
                            'mime_type' => $ext === 'jpg' ? 'image/jpeg' : "image/{$ext}",
                        ];
                    }
                }
            }
        }

        // Fallback simpan original jika kompresi gagal atau hasil lebih besar
        return $this->storeOriginalFile($file);
    }

    /**
     * Simpan berkas original tanpa kompresi.
     */
    protected function storeOriginalFile(UploadedFile $file): array
    {
        $path = $file->store('attachments', 'public');

        return [
            'path'      => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
        ];
    }

    /**
     * Hapus semua lampiran dari suatu entitas polimorfik (beserta berkas fisik di storage).
     */
    public function deleteForAttachable(string $attachableType, int $attachableId, ?string $attachmentType = null): void
    {
        $query = Attachment::where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId);

        if ($attachmentType !== null) {
            $query->where('attachment_type', $attachmentType);
        }

        $attachments = $query->get();

        foreach ($attachments as $attachment) {
            if ($attachment->storage_reference && Storage::disk('public')->exists($attachment->storage_reference)) {
                Storage::disk('public')->delete($attachment->storage_reference);
            }
            $attachment->delete();
        }
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
