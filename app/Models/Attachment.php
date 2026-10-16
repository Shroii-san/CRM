<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasActivityLogs;

    protected $table = 'attachment';

    const UPDATED_AT = null;

    protected $fillable = [
        'file_name',
        'description',
        'attachment_type',
        'mime_type',
        'file_size',
        'storage_reference',
        'uploaded_by',
        'attachable_type',
        'attachable_id',
    ];

    /**
     * URL publik berkas lampiran.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->storage_reference ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->storage_reference) : null;
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('attachment_type', $type);
    }

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
