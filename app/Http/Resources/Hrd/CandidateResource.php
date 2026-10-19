<?php

namespace App\Http\Resources\Hrd;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'nik' => $this->nik,
            'phone_number' => $this->phone_number,
            'division' => $this->division,
            'application_date' => $this->application_date,
            'npwp' => $this->npwp,
            'no_bpjs' => $this->no_bpjs,
            'status' => (int) $this->status,
            'status_label' => match ((int) $this->status) {
                1 => 'Pending',
                2 => 'Accepted',
                3 => 'Rejected',
                default => 'Unknown',
            },
            'foto' => $this->relationLoaded('attachments')
                ? $this->attachments->firstWhere('attachment_type', 'foto')?->file_url
                : $this->foto_url,
            'scan_ktp' => $this->relationLoaded('attachments')
                ? $this->attachments->firstWhere('attachment_type', 'scan_ktp')?->file_url
                : $this->scan_ktp_url,
            'cv' => $this->relationLoaded('attachments')
                ? $this->attachments->firstWhere('attachment_type', 'cv')?->file_url
                : $this->cv_url,
            'education_histories' => $this->whenLoaded('educationHistories', function () {
                return $this->educationHistories->map(function ($education) {
                    return [
                        'id' => $education->id,
                        'level' => $education->level,
                        'jenjang' => $education->level,
                        'school_name' => $education->school_name,
                        'nama_sekolah' => $education->school_name,
                        'major' => $education->major,
                        'jurusan' => $education->major,
                        'degree' => $education->degree,
                        'gelar' => $education->degree,
                        'score' => $education->score,
                        'nilai' => $education->score,
                        'last_school_location' => $education->last_school_location,
                        'lokasi' => $education->last_school_location,
                        'start_year' => $education->start_year,
                        'tahun_masuk' => $education->start_year,
                        'graduation_year' => $education->graduation_year,
                        'tahun_lulus' => $education->graduation_year,
                    ];
                })->values();
            }),
            'families' => $this->whenLoaded('families', function () {
                return $this->families->map(function ($family) {
                    return [
                        'id' => $family->id,
                        'name' => $family->name,
                        'nama' => $family->name,
                        'birth_place' => $family->birth_place,
                        'tempat_lahir' => $family->birth_place,
                        'birth_date' => $family->birth_date ? \Carbon\Carbon::parse($family->birth_date)->format('Y-m-d') : null,
                        'tanggal_lahir' => $family->birth_date ? \Carbon\Carbon::parse($family->birth_date)->format('Y-m-d') : null,
                        'status' => $family->status,
                    ];
                })->values();
            }),
            'employee' => $this->whenLoaded('employee', function () {
                if (!$this->employee) {
                    return null;
                }

                $families = $this->employee->relationLoaded('families')
                    ? $this->employee->families->map(function ($family) {
                        return [
                            'id' => $family->id,
                            'name' => $family->name,
                            'nama' => $family->name,
                            'birth_place' => $family->birth_place,
                            'tempat_lahir' => $family->birth_place,
                            'birth_date' => $family->birth_date ? \Carbon\Carbon::parse($family->birth_date)->format('Y-m-d') : null,
                            'tanggal_lahir' => $family->birth_date ? \Carbon\Carbon::parse($family->birth_date)->format('Y-m-d') : null,
                            'status' => $family->status,
                        ];
                    })->values()
                    : [];

                return [
                    'id' => $this->employee->id,
                    'full_name' => $this->employee->full_name,
                    'nik' => $this->employee->nik,
                    'join_date' => $this->employee->join_date
                        ? (is_string($this->employee->join_date)
                            ? \Carbon\Carbon::parse($this->employee->join_date)->toDateString()
                            : $this->employee->join_date->toDateString())
                        : null,
                    'status' => $this->employee->status,
                    'families' => $families,
                    'families_count' => count($families),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
