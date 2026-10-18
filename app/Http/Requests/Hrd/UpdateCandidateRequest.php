<?php

namespace App\Http\Requests\Hrd;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'status' => ['required', 'integer', 'in:1,2,3'],

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
