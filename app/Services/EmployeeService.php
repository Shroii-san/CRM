<?php

namespace App\Services;

use App\Models\Attachment;
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
            }

            $this->duplicateAttachments($candidate, $employee);

            return $employee;
        });
    }

    protected function duplicateAttachments(Candidate $candidate, Employee $employee): void
    {
        $candidate->attachments()->get()->each(function (Attachment $attachment) use ($employee): void {
            $employee->attachments()->create([
                'file_name' => $attachment->file_name,
                'description' => $attachment->description,
                'attachment_type' => $attachment->attachment_type,
                'mime_type' => $attachment->mime_type,
                'file_size' => $attachment->file_size,
                'storage_reference' => $attachment->storage_reference,
                'uploaded_by' => $attachment->uploaded_by,
            ]);
        });
    }
}
