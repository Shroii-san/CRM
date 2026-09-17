<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{

    use HasActivityLogs;

    protected $table = 'pipeline_stages';
    protected $fillable = [
        'pipeline_id',
        'name',
        'slug',
        'description',
        'position',
        'is_terminal',
        'is_active',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_terminal' => 'boolean',
        'is_active' => 'boolean',
    ];

    // FK relationships untuk pipeline yang memiliki stage ini
    public function pipelines()
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id', 'id');
    }

    // FK relationships untuk deals yang terkait dengan stage ini
    public function deals()
    {
        return $this->hasMany(Deal::class, 'pipeline_stage_id', 'id');
    }

    // FK relationships untuk task template yang terkait dengan stage ini
    public function taskTemplates()
    {
        return $this->hasMany(StageTaskTemplate::class, 'stage_id', 'id');
    }

    // FK relationships untuk deal stage history yang terkait dengan stage ini
    public function dealStageHistoriesFrom()
    {
        return $this->hasMany(DealStageHistory::class, 'from_stage_id', 'id');
    }

    public function dealStageHistoriesTo()
    {
        return $this->hasMany(DealStageHistory::class, 'to_stage_id', 'id');
    }

    public function getStageColor(): string
    {
        return match ($this->slug) {
            'leads' => 'info',
            'visit' => 'warning',
            'penawaran' => 'secondary',
            'follow_up' => 'success',
            default => 'light',
        };
    }

    public function getStageIcon(): string
    {
        return match ($this->slug) {
            'leads' => 'fas fa-bullseye',
            'visit' => 'fas fa-user-check',
            'penawaran' => 'fas fa-file-contract',
            'follow_up' => 'fas fa-check-double',
            default => 'fas fa-circle',
        };
    }
}
