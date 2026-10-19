<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    public const STATUS_ACCEPTED = 2;

    protected $fillable = [
        'full_name',
        'nik',
        'phone_number',
        'division',
        'application_date',
        'npwp',
        'no_bpjs',
        'status',  // 1 = pending, 2 = accepted, 3 = rejected
    ];

    public function educationHistories()
    {
        return $this->hasMany(EducationHistory::class, 'candidate_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'candidate_id', 'id');
    }

    public function families()
    {
        return $this->hasMany(FamilyMember::class, 'candidate_id', 'id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function foto()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('attachment_type', 'foto');
    }

    public function scanKtp()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('attachment_type', 'scan_ktp');
    }

    public function cv()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('attachment_type', 'cv');
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto?->file_url;
    }

    public function getScanKtpUrlAttribute(): ?string
    {
        return $this->scanKtp?->file_url;
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv?->file_url;
    }
}
