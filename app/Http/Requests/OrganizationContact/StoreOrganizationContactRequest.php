<?php

namespace App\Http\Requests\OrganizationContact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrganizationContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'person_id'       => 'nullable|exists:persons,id',
            'name'            => 'required_without:person_id|nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'job_title'       => 'nullable|string|max:100',
            'is_primary'      => 'nullable|boolean',
            'started_at'      => 'nullable|date',
            'ended_at'        => 'nullable|date|after:started_at',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('person_id') && $this->filled('name')) {
                $validator->errors()->add(
                    'person_id',
                    'Tidak dapat mengirimkan person_id dan name secara bersamaan. Pilih person_id yang sudah ada ATAU isi name untuk membuat person baru.'
                );
            }
        });
    }
}
