<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait HasActivityLogs
{
    /**
     * Relasi polimorfik ke ActivityLog
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'target');
    }
}
