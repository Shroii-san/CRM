<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesVisit extends Model
{
    use HasFactory, HasActivityLogs;

    protected $table = 'sales_visits';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'organization_id',
        'organization_contact_id',
        'attachment_id',
        'visit_date',
        'visit_purpose',
        'is_follow_up',
        'latitude',
        'longitude',
        'address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'is_follow_up' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============= RELATIONSHIPS =============

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function contactPerson()
    {
        return $this->belongsTo(OrganizationContact::class, 'organization_contact_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getSalesName()
    {
        if ($this->user_id) {
            return $this->user?->name ?? 'N/A';
        }
        return 'N/A';
    }

    public function getOrganizationName()
    {
        return $this->organization?->name ?? $this->name ?? 'N/A';
    }

    public function getSalesId()
    {
        return $this->user_id ?? null;
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
        return $salesId ? $query->Where('user_id', $salesId) : $query;
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

    // public function scopeSearch($query, $search)
    // {
    //     if (!$search)
    //         return $query;

    //     $searchLower = strtolower($search);

    //     return $query->where(function ($q) use ($searchLower) {
    //         $q->whereRaw('LOWER(organization_contact_id.name) LIKE ?', ["%{$searchLower}%"])
    //             ->orWhereRaw('LOWER(organization.name) LIKE ?', ["%{$searchLower}%"])
    //             ->orWhereRaw('LOWER(visit_purpose) LIKE ?', ["%{$searchLower}%"])
    //             ->orWhereRaw('LOWER(address) LIKE ?', ["%{$searchLower}%"])
    //             ->orWhereHas('user_id', function ($qt) use ($searchLower) {
    //                 $qt->whereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"])
    //                     ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"]);
    //             })
    //             ->orWhereHas('user', function ($qt) use ($searchLower) {
    //                 $qt->whereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"]);
    //             })
    //             ->orWhereHas('province', fn($qt) => $qt->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]))
    //             ->orWhereHas('regency', fn($qt) => $qt->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]));
    //     });
    // }
}
