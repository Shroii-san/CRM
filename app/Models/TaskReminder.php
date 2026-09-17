<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class TaskReminder extends Model
{

    use HasActivityLogs;

    protected $table = 'task_reminders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'task_id',
        'remind_at',
        'is_active',
        'sent_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
