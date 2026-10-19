<?php

namespace App\Http\Requests\FamilyMember;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $statusMap = [
            'Ayah' => 1,
            'Ibu' => 1,
            'Orang Tua' => 1,
            'Suami' => 2,
            'Istri' => 2,
            'Pasangan' => 2,
            'Anak' => 3,
        ];

        $status = $this->input('status');
        if (is_string($status) && isset($statusMap[$status])) {
            $status = $statusMap[$status];
        } elseif (is_numeric($status)) {
            $status = (int) $status;
        }

        $merge = [];
        if ($this->filled('nama') && !$this->has('name')) {
            $merge['name'] = $this->input('nama');
        }
        if ($this->filled('tempat_lahir') && !$this->has('birth_place')) {
            $merge['birth_place'] = $this->input('tempat_lahir');
        }
        if ($this->filled('tanggal_lahir') && !$this->has('birth_date')) {
            $merge['birth_date'] = $this->input('tanggal_lahir');
        }
        if ($status !== null) {
            $merge['status'] = $status;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'integer', 'in:1,2,3'],
        ];
    }
}
