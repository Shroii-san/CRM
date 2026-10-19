<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $casts = [
        'join_date' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'candidate_id',
        'department_id',
        'full_name',
        'nik',
        'phone_number',
        'join_date',
        'npwp',
        'no_bpjs',
        'status',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }

    public function educationHistories()
    {
        return $this->hasMany(EducationHistory::class, 'employee_id');
    }

    public function families()
    {
        return $this->hasMany(FamilyMember::class, 'employee_id', 'id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
