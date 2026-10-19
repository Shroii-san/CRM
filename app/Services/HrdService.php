<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HrdService extends BaseService
{
    public function __construct(protected AttachmentService $attachmentService)
    {
    }

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

    public function create(array $data, ?User $uploader = null): Candidate
    {
        $uploader = $uploader ?? auth()->user() ?? User::first();

        return DB::transaction(function () use ($data, $uploader) {
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

            $this->saveAttachments($candidate, $data, $uploader, false);

            return $candidate->fresh(['educationHistories', 'families', 'employee.families', 'attachments']);
        });
    }

    public function store(array $data, ?User $uploader = null): Candidate
    {
        return $this->create($data, $uploader);
    }

    public function update(Candidate|int $candidate, array $data, ?User $uploader = null): Candidate
    {
        $candidate = is_int($candidate) ? Candidate::findOrFail($candidate) : $candidate;
        $uploader = $uploader ?? auth()->user() ?? User::first();

        return DB::transaction(function () use ($candidate, $data, $uploader) {
            $candidate->update([
                'full_name' => $data['full_name'] ?? $candidate->full_name,
                'nik' => $data['nik'] ?? $candidate->nik,
                'phone_number' => $data['phone_number'] ?? $candidate->phone_number,
                'division' => $data['division'] ?? $candidate->division,
                'application_date' => $data['application_date'] ?? $candidate->application_date,
                'npwp' => array_key_exists('npwp', $data) ? $data['npwp'] : $candidate->npwp,
                'no_bpjs' => array_key_exists('no_bpjs', $data) ? $data['no_bpjs'] : $candidate->no_bpjs,
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

            $this->saveAttachments($candidate, $data, $uploader, true);

            return $candidate->fresh(['educationHistories', 'families', 'employee.families', 'attachments']);
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
            $this->attachmentService->deleteForAttachable(Candidate::class, $candidate->id);
            $candidate->delete();
        });
    }

    public function destroy(Candidate|int $candidate): void
    {
        $this->delete($candidate);
    }

    protected function saveAttachments(Candidate $candidate, array $data, ?User $uploader = null, bool $isUpdate = false): void
    {
        $uploader = $uploader ?? auth()->user() ?? User::first();
        if (!$uploader || !$candidate->id) {
            return;
        }

        $fields = [
            'foto' => [
                'type' => 'foto',
                'description' => 'Foto ' . $candidate->full_name,
            ],
            'scan_ktp' => [
                'type' => 'scan_ktp',
                'description' => 'Scan KTP ' . $candidate->full_name,
            ],
            'cv' => [
                'type' => 'cv',
                'description' => 'CV ' . $candidate->full_name,
            ],
        ];

        foreach ($fields as $field => $meta) {
            $file = $data[$field] ?? null;
            if (!$file instanceof UploadedFile && request()->hasFile($field)) {
                $file = request()->file($field);
            }

            if ($file instanceof UploadedFile && $file->isValid()) {
                if ($isUpdate) {
                    $this->attachmentService->deleteForAttachable(Candidate::class, (int) $candidate->id, $meta['type']);
                }

                $this->attachmentService->create([
                    'attachable_type' => Candidate::class,
                    'attachable_id'   => (int) $candidate->id,
                    'attachment_type' => $meta['type'],
                    'description'     => $meta['description'],
                ], $file, $uploader);
            }
        }
    }

    public function promoteCandidateToEmployee(Candidate $candidate, array $data = []): Employee
    {
        return app(EmployeeService::class)->promote($candidate, $data);
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
