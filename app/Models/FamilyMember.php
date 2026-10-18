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

    // Relationship ke Kandidat
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->tanggal_lahir?->age;
    }
}
