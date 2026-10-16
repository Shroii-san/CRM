<?php

namespace App\Http\Requests\SalesVisit;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'                 => 'nullable|exists:users,id',
            'organization_id'         => 'required|exists:organizations,id',
            'organization_contact_id' => 'nullable|exists:organization_contacts,id',
            'attachment_id'           => 'nullable|integer',
            'visit_date'              => 'required|date',
            'visit_purpose'           => 'required|string',
            'is_follow_up'            => 'nullable|boolean',
            'latitude'                => 'nullable|numeric',
            'longitude'               => 'nullable|numeric',
            'address'                 => 'nullable|string',
            'province_id'             => 'nullable|integer',
            'regency_id'              => 'nullable|integer',
            'district_id'             => 'nullable|integer',
            'village_id'              => 'nullable|integer',
        ];
    }
}
