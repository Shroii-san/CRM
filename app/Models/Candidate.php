<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'nik',
        'phone_number',
        'division',
        'application_date',
        'npwp',
        'no_bpjs',
        'status',
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


}
