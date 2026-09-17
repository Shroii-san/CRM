<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{

    use HasActivityLogs;

    protected $table = 'pipelines';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    // FK relationships untuk stage yang terkait dengan pipeline ini
    public function stages()
    {
        return $this->hasMany(PipelineStage::class, 'pipeline_id', 'id');
    }

    // FK relationships untuk deals yang terkait dengan pipeline ini
    public function deals()
    {
        return $this->hasMany(Deal::class, 'pipeline_id', 'id');
    }

}
