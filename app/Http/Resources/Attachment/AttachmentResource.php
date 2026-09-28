<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'file_name'         => $this->file_name,
            'description'       => $this->description,
            'mime_type'         => $this->mime_type,
            'file_size'         => (int) $this->file_size,
            'storage_reference' => $this->storage_reference,
            'file_url'          => $this->storage_reference ? Storage::disk('public')->url($this->storage_reference) : null,
            'uploaded_by'       => $this->uploaded_by,
            'attachable_type'   => $this->attachable_type,
            'attachable_id'     => $this->attachable_id,
            'uploader'          => $this->whenLoaded('uploader', function () {
                return [
                    'id'    => $this->uploader->id,
                    'name'  => $this->uploader->name,
                    'email' => $this->uploader->email,
                ];
            }),
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
