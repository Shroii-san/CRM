<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use Filterable;

    const UPDATED_AT = null; // Cuma ada created_at di tabel

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'metadata',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Target aktivitas yang diaudit
    public function target()
    {
        return $this->morphTo();
    }
}
