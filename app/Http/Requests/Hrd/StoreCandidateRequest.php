<?php

namespace App\Http\Requests\Hrd;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('pendidikan') && !$this->has('education_histories')) {
            $educationHistories = collect($this->input('pendidikan', []))
                ->values()
                ->map(function ($item) {
                    return [
                        'level' => $item['level'] ?? $item['jenjang'] ?? null,
                        'school_name' => $item['school_name'] ?? $item['nama_sekolah'] ?? null,
                        'major' => $item['major'] ?? $item['jurusan'] ?? null,
                        'degree' => $item['degree'] ?? $item['gelar'] ?? null,
                        'last_school_location' => $item['last_school_location'] ?? $item['lokasi'] ?? null,
                        'start_year' => $item['start_year'] ?? $item['tahun_masuk'] ?? null,
                        'graduation_year' => $item['graduation_year'] ?? $item['tahun_lulus'] ?? null,
                        'score' => $item['score'] ?? $item['ipk_atau_nilai'] ?? null,
                    ];
                })
                ->all();

            $this->merge(['education_histories' => $educationHistories]);
        }

        if ($this->has('education_histories') && is_array($this->input('education_histories'))) {
            $filtered = array_values(array_filter($this->input('education_histories'), function ($item) {
                if (!is_array($item)) return false;
                $filled = array_filter($item, fn($val) => $val !== null && trim((string)$val) !== '');
                return !empty($filled);
            }));

            $this->merge(['education_histories' => $filtered]);
        }
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'phone_number' => ['required', 'string', 'max:15'],
            'division' => ['required', 'string', 'max:100'],
            'application_date' => ['required', 'date'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'no_bpjs' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'integer', 'in:1,2,3'],
            'foto' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'scan_ktp' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],

            'education_histories' => ['nullable', 'array'],
            'education_histories.*.level' => ['required', 'string', 'max:20'],
            'education_histories.*.school_name' => ['required', 'string', 'max:255'],
            'education_histories.*.major' => ['nullable', 'string', 'max:255'],
            'education_histories.*.degree' => ['nullable', 'string', 'max:100'],
            'education_histories.*.score' => ['nullable', 'string', 'max:15'],
            'education_histories.*.last_school_location' => ['nullable', 'string', 'max:255'],
            'education_histories.*.start_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'education_histories.*.graduation_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],

            'families' => ['nullable', 'array'],
            'families.*.id' => ['nullable', 'integer', 'exists:family_members,id'],
            'families.*.name' => ['required', 'string', 'max:255'],
            'families.*.birth_place' => ['required', 'string', 'max:255'],
            'families.*.birth_date' => ['required', 'date', 'before_or_equal:today'],
            'families.*.status' => ['required', 'integer', 'in:1,2,3'],
        ];
    }
}
