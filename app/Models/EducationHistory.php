<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    protected $table = 'education_histories';

    protected $fillable = [
        'candidate_id',
        'employee_id',
        'level', // SD, SMP, SMA, SMK, MAN, D3, S1, dll
        'school_name',
        'major',
        'degree', // Contoh: S.Kom., S.E.
        'score',  // IPK atau nilai rata-rata Ijazah
        'last_school_location',
        'start_year',
        'graduation_year',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
