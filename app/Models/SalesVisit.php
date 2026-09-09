<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesVisit extends Model
{
    use HasFactory;

    protected $table = 'sales_visits';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sales_id',
        'user_id',
        'pic_name',  
        'pic_id',  
        'company_name',
        'company_id',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'address',
        'visit_date',
        'visit_purpose',
        'is_follow_up',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'is_follow_up' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============= RELATIONSHIPS =============

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function pic()
    {
        return $this->belongsTo(CompanyPic::class, 'pic_id', 'pic_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getSalesName()
    {
        if ($this->sales_id) {
            return $this->sales?->username ?? 'N/A';
        } elseif ($this->user_id) {
            return $this->user?->username ?? 'N/A';
        }
        return 'N/A';
    }

    public function getCompanyName()
    {
        return $this->company?->nama ?? $this->company_name ?? 'N/A';
    }

    public function getSalesId()
    {
        return $this->sales_id ?? $this->user_id ?? null;
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    // ============= SCOPES =============

    public function scopeFilterBySales($query, $salesId)
    {
        return $salesId ? $query->where('sales_id', $salesId)->orWhere('user_id', $salesId) : $query;
    }

    public function scopeFilterByFollowUp($query, $followUp)
    {
        if ($followUp !== null && $followUp !== '') {
            $isFollowUp = in_array(strtolower($followUp), ['ya', '1', 1, 'true', true], true);
            return $query->where('is_follow_up', $isFollowUp);
        }
        return $query;
    }

    public function scopeFilterByProvince($query, $provinceId)
    {
        return $provinceId ? $query->where('province_id', $provinceId) : $query;
    }

    public function scopeFilterByRegency($query, $regencyId)
    {
        return $regencyId ? $query->where('regency_id', $regencyId) : $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        $searchLower = strtolower($search);

        return $query->where(function ($q) use ($searchLower) {
            $q->whereRaw('LOWER(pic_name) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(visit_purpose) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(address) LIKE ?', ["%{$searchLower}%"])
              ->orWhereHas('sales', function ($qt) use ($searchLower) {
                  $qt->whereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"])
                     ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"]);
              })
              ->orWhereHas('user', function ($qt) use ($searchLower) {
                  $qt->whereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"]);
              })
              ->orWhereHas('province', fn($qt) => $qt->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]))
              ->orWhereHas('regency', fn($qt) => $qt->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]));
        });
    }
}