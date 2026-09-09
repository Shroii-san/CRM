<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';
    public $timestamps = false;

    protected $fillable = [
        'company_name', 
        'company_type_id', 
        'tier', 
        'description', 
        'status',
        'user_id',
        // 🔥 NEW: Address fields
        'address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        // 🔥 NEW: Contact & Media fields
        'phone',
        'email',
        'website',
        'linkedin',
        'instagram',
        'logo'
    ];

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pics()
    {
        return $this->hasMany(CompanyPic::class, 'company_id', 'company_id');
    }

    // 🔥 NEW: Address relations
    public function province()
    {
        return $this->belongsTo(\App\Models\Province::class, 'province_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(\App\Models\Regency::class, 'regency_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Models\District::class, 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class, 'village_id', 'id');
    }
}