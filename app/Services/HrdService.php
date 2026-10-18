<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HrdService
{
    public function index()
    {
        return Candidate::query()
            ->with(['educationHistories', 'families', 'employee'])
            ->latest()
            ->get();
    }

    public function store(array $data): Candidate
    {
        return DB::transaction(function () use ($data) {
            $candidate = Candidate::query()->create([
                'full_name' => $data['full_name'],
                'nik' => $data['nik'],
                'phone_number' => $data['phone_number'],
                'division' => $data['division'],
                'application_date' => $data['application_date'],
                'npwp' => $data['npwp'] ?? null,
                'no_bpjs' => $data['no_bpjs'] ?? null,
                'status' => $data['status'] ?? 1,
            ]);

            if (!empty($data['education_histories'] ?? [])) {
                $candidate->educationHistories()->createMany($data['education_histories']);
            }

            if (!empty($data['families'] ?? [])) {
                $candidate->families()->createMany($data['families']);
            }

            return $candidate;
        });
    }

    public function update(Candidate $candidate, array $data): Candidate
    {
        return DB::transaction(function () use ($candidate, $data) {
            $candidate->update([
                'full_name' => $data['full_name'],
                'nik' => $data['nik'],
                'phone_number' => $data['phone_number'],
                'division' => $data['division'],
                'application_date' => $data['application_date'],
                'npwp' => $data['npwp'] ?? null,
                'no_bpjs' => $data['no_bpjs'] ?? null,
                'status' => (int) $data['status'],
            ]);

            if (array_key_exists('education_histories', $data)) {
                $candidate->educationHistories()->delete();

                if (!empty($data['education_histories'])) {
                    $candidate->educationHistories()->createMany($data['education_histories']);
                }
            }

            if (array_key_exists('families', $data)) {
                $candidate->families()->delete();

                if (!empty($data['families'])) {
                    $candidate->families()->createMany($data['families']);
                }
            }

            return $candidate->fresh();
        });
    }

    public function destroy(Candidate $candidate): void
    {
        if ($candidate->employee()->exists()) {
            throw new \RuntimeException('Tidak dapat menghapus kandidat yang sudah menjadi karyawan.');
        }

        DB::transaction(function () use ($candidate) {
            $candidate->educationHistories()->delete();
            $candidate->families()->delete();
            $candidate->attachments()->delete();
            $candidate->delete();
        });
    }

    public function promoteCandidateToEmployee(Candidate $candidate): Employee
    {
        return app(EmployeeService::class)->promote($candidate);
    }
}
