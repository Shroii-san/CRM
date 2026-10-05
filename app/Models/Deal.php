<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasActivityLogs;

    protected $table = 'deals';

    protected $fillable = [
        'client_id',
        'pipeline_id',
        'current_stage_id',
        'assigned_user_id',
        'name',
        'description',
        'currency',
        'value',
        'status',
        'priority',
        'expected_closed_at',
        'actual_closed_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'status' => 'integer',
        'priority' => 'integer',
        'expected_closed_at' => 'date',
        'actual_closed_at' => 'date',
        'is_active' => 'boolean',
    ];

    // FK relationships untuk client yang terkait dengan deals
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    // FK relationships untuk pipeline yang terkait deals
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id', 'id');
    }

    // FK relationships untuk stage yang ditempati deals saat ini
    public function currentStage()
    {
        return $this->belongsTo(PipelineStage::class, 'current_stage_id', 'id');
    }

    // FK relationship untuk user yang ditugaskan untuk menangani deals ini
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id');
    }

    // FK relationships untuk deal stage history yang terkait dengan deals ini
    public function dealStageHistories()
    {
        return $this->hasMany(DealStageHistory::class, 'deal_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'deal_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'deal_id', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // ============= ACCESSORS =============

    public function getValueFormattedAttribute()
    {
        return 'Rp' . number_format((float) ($this->value ?? 0), 0, ',', '.');
    }
}
