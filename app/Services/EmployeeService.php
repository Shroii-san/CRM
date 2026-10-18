<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function promote(Candidate $candidate): Employee
    {
        if ($candidate->employee()->exists()) {
            return $candidate->employee;
        }

        return DB::transaction(function () use ($candidate) {
            $employee = $candidate->employee()->create([
                'department_id' => null,
                'full_name' => $candidate->full_name,
                'nik' => $candidate->nik,
                'phone_number' => $candidate->phone_number,
                'join_date' => now()->toDateString(),
                'status' => 1,
            ]);

            foreach ($candidate->educationHistories as $educationHistory) {
                $employee->educationHistories()->create($educationHistory->only([
                    'level',
                    'school_name',
                    'major',
                    'degree',
                    'score',
                    'last_school_location',
                    'start_year',
                    'graduation_year',
                ]));
            }

            foreach ($candidate->families as $familyMember) {
                $employee->families()->create($familyMember->only([
                    'name',
                    'birth_place',
                    'birth_date',
                    'status',
                ]));
            };

            return $employee;
        });
    }
}
