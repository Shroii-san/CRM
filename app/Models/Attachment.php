<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'file_name',
        'description',
        'mime_type',
        'file_size',
        'storage_reference',
        'uploaded_by',
        'attachable_type',
        'attachable_id',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
