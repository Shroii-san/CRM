<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasActivityLogs;

    protected $table = 'organizations';

    protected $fillable = [
        'industry_id',
        'tier',
        'name',
        'email',
        'phone',
        'website',
        'description',
        'sp_number',
        'kemkes_link',
        'is_active',
        'address',
        'latitude',
        'longitude',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'industry_id' => 'integer',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
    ];

    /**
     * Kode numerik organisasi yang dipad 5 digit berdasarkan ID auto-increment.
     * Contoh: "00001", "00042"
     */
    public function getOrganizationCodeAttribute(): string
    {
        return str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Kode organisasi lengkap berformat [Kode Industri]>[Kode Organisasi].
     * Contoh: "00004>00001"
     */
    public function getOrganizationFullCodeAttribute(): string
    {
        $industryPart = str_pad((string) ($this->industry_id ?? 0), 5, '0', STR_PAD_LEFT);
        $orgPart      = str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);

        return "{$industryPart}>{$orgPart}";
    }

    /**
     * URL publik berkas logo organisasi (dari polymorphic attachment).
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->relationLoaded('logo')) {
            return $this->logo?->file_url;
        }

        if ($this->relationLoaded('attachments')) {
            return $this->attachments->firstWhere('attachment_type', 'logo')?->file_url;
        }

        if (!$this->exists) {
            return null;
        }

        return $this->logo?->file_url;
    }

    /**
     * Relasi polimorfik untuk semua berkas lampiran organisasi.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Relasi polimorfik spesifik untuk berkas logo organisasi.
     */
    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('attachment_type', 'logo');
    }

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

    public function client()
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
