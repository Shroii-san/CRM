<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HrdService extends BaseService
{
    protected function getModel(): string
    {
        return Candidate::class;
    }

    protected function getSearchableColumns(): array
    {
        return ['full_name', 'nik', 'division', 'phone_number'];
    }

    protected function getFilterDefinitions(): array
    {
        return [
            'status' => 'equal',
            'division' => 'equal',
        ];
    }

    protected function getWithRelations(): array
    {
        return ['educationHistories', 'families', 'employee.families', 'attachments'];
    }

    protected function getSortableColumns(): array
    {
        return ['id', 'full_name', 'nik', 'division', 'status', 'application_date', 'created_at'];
    }

    public function index()
    {
        return Candidate::query()
            ->with(['educationHistories', 'families', 'employee.families', 'attachments'])
            ->latest()
            ->get();
    }

    public function findWithDetails(int $id): Candidate
    {
        return Candidate::with(['educationHistories', 'families', 'employee.families', 'attachments'])
            ->findOrFail($id);
    }

    public function create(array $data): Candidate
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

            return $candidate->fresh(['educationHistories', 'families', 'employee']);
        });
    }

    public function store(array $data): Candidate
    {
        return $this->create($data);
    }

    public function update(Candidate|int $candidate, array $data): Candidate
    {
        $candidate = is_int($candidate) ? Candidate::findOrFail($candidate) : $candidate;

        return DB::transaction(function () use ($candidate, $data) {
            $candidate->update([
                'full_name' => $data['full_name'],
                'nik' => $data['nik'],
                'phone_number' => $data['phone_number'],
                'division' => $data['division'],
                'application_date' => $data['application_date'],
                'npwp' => $data['npwp'] ?? null,
                'no_bpjs' => $data['no_bpjs'] ?? null,
                'status' => (int) ($data['status'] ?? $candidate->status),
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

            return $candidate->fresh(['educationHistories', 'families', 'employee']);
        });
    }

    public function delete(Candidate|int $candidate): void
    {
        $candidate = is_int($candidate) ? Candidate::findOrFail($candidate) : $candidate;

        if ($candidate->employee()->exists()) {
            throw new RuntimeException('Tidak dapat menghapus kandidat yang sudah menjadi karyawan.');
        }

        DB::transaction(function () use ($candidate) {
            $candidate->educationHistories()->delete();
            $candidate->families()->delete();
            $candidate->attachments()->delete();
            $candidate->delete();
        });
    }

    public function destroy(Candidate|int $candidate): void
    {
        $this->delete($candidate);
    }

    public function promoteCandidateToEmployee(Candidate $candidate): Employee
    {
        return app(EmployeeService::class)->promote($candidate);
    }

    public function getFilterOptions(): array
    {
        return [
            'status' => [
                1 => 'Pending',
                2 => 'Accepted',
                3 => 'Rejected',
            ],
            'division' => Candidate::query()
                ->whereNotNull('division')
                ->where('division', '!=', '')
                ->distinct()
                ->orderBy('division')
                ->pluck('division')
                ->all(),
        ];
    }
}
