<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageTaskTemplate extends Model
{
    protected $table = 'stage_task_templates';

    protected $primaryKey = 'id';

    protected $fillable = [
        'stage_id',
        'name',
        'description',
        'priority',
        'due_offset_days',
        'is_required',
        'is_active',
    ];

    // FK template untuk stage terkait
    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'stage_task_template_id', 'id');
    }

}
