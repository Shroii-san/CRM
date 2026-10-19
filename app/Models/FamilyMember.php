<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';

    protected $fillable = [
        'employee_id',
        'candidate_id',
        'name',
        'birth_place',
        'birth_date',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // Relationship ke Karyawan
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function karyawan()
    {
        return $this->employee();
    }

    // Relationship ke Kandidat
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }

    public function kandidat()
    {
        return $this->candidate();
    }

    public function getNamaAttribute(): ?string
    {
        return $this->name;
    }

    public function getTempatLahirAttribute(): ?string
    {
        return $this->birth_place;
    }

    public function getTanggalLahirAttribute()
    {
        return $this->birth_date;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            1 => 'Orang Tua',
            2 => 'Pasangan',
            3 => 'Anak',
            default => (string) $this->status,
        };
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
