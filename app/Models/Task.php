<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'client_id',
        'deal_id',
        'stage_task_template_id',
        'assigned_user_id',
        'name',
        'description',
        'due_at',
        'completed_at',
        'status',
        'priority',
    ];

    // FK todo task untuk client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    // FK todo task untuk deal
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id');
    }

    public function taskTemplate()
    {
        return $this->belongsTo(StageTaskTemplate::class, 'stage_task_template_id', 'id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id');
    }

    public function reminder()
    {
        return $this->hasMany(Task::class, 'task_id', 'id');
    }
}
