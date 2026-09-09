<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    protected $table = 'pipeline_stages';
    protected $fillable = ['name', 'slug', 'description', 'order'];

    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class, 'stage_id');
    }

    public function getStageColor(): string
    {
        return match($this->slug) {
            'leads' => 'info',
            'visit' => 'warning',
            'penawaran' => 'secondary',
            'follow_up' => 'success',
            default => 'light',
        };
    }

    public function getStageIcon(): string
    {
        return match($this->slug) {
            'leads' => 'fas fa-bullseye',
            'visit' => 'fas fa-user-check',
            'penawaran' => 'fas fa-file-contract',
            'follow_up' => 'fas fa-check-double',
            default => 'fas fa-circle',
        };
    }
}