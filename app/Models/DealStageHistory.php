<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealStageHistory extends Model
{
    protected $table = 'deal_stage_histories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'deal_id',
        'from_stage_id',
        'to_stage_id',
        'changed_by',
    ];

    // FK relationships untuk deal yang terkait dengan stage history ini
    public function deal()
    {
        return $this->belongsTo(Deals::class, 'deal_id', 'id');
    }

    // FK relationships untuk stage yang terkait dengan stage history ini
    public function fromStage()
    {
        return $this->belongsTo(PipelineStage::class, 'from_stage_id', 'id');
    }

    public function toStage()
    {
        return $this->belongsTo(PipelineStage::class, 'to_stage_id', 'id');
    }

    // FK relationships untuk user yang mengubah stage history ini
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
}
