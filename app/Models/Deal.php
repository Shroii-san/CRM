<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    protected $table = 'deals';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

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
        'expected_close_at',
        'actual_close_at',
        'is_active',
    ];

    // FK relationships untuk client yang terkait dengan deals
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    // FK relationshiops untuk pipeline yang terkait deals
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



    // ============= SCOPES =============

    // public function scopeDeals($query)
    // {
    //     return $query->where('status', 'Deals');
    // }

    // public function scopeFails($query)
    // {
    //     return $query->where('status', 'Fails');
    // }

    // public function scopeBySales($query, $salesId)
    // {
    //     return $query->where('sales_id', $salesId);
    // }

    // public function scopeByCompany($query, $companyId)
    // {
    //     return $query->where('company_id', $companyId);
    // }

    // public function scopeByStatus($query, $status)
    // {
    //     return $query->where('status', $status);
    // }

    // public function scopeSearchByName($query, $search)
    // {
    //     return $query->where('nama_sales', 'like', "%{$search}%")
    //         ->orWhere('nama_perusahaan', 'like', "%{$search}%");
    // }

    // ============= ACCESSORS =============

    public function getValueFormattedAttribute()
    {
        return 'Rp' . number_format($this->value, 0, ',', '.');
    }

    // public function getStatusBadgeAttribute()
    // {
    //     if ($this->status === 'Deals') {
    //         return '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #dcfce7; color: #166534; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Deals</span>';
    //     } else {
    //         return '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #fee2e2; color: #991b1b; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-times-circle"></i> Fails</span>';
    //     }
    // }
}