<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class TaskReminder extends Model
{

    use HasActivityLogs;

    protected $table = 'task_reminders';

    protected $fillable = [
        'task_id',
        'remind_at',
        'is_active',
        'sent_at',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_active' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
