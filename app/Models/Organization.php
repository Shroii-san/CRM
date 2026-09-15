<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'industry_id',
        'tier',
        'name',
        'email',
        'phone',
        'website',
        'description',
        'is_active',
        'address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
    ];

    // FK relationships untuk jenis industri
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }

    // FK relationships untuk PIC/Contact Person organisasi
    public function contacts()
    {
        return $this->hasMany(OrganizationContact::class, 'organization_id', 'id');
    }

    // FK relationships untuk social media profiles organisasi
    public function socialProfiles()
    {
        return $this->hasMany(OrganizationSocialProfiles::class, 'organization_id', 'id');
    }

    public function clients()
    {
        return $this->hasOne(Client::class, 'organization_id', 'id');
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
}
