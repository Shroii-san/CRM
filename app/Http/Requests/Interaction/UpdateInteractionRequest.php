<?php

namespace App\Http\Requests\Interaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInteractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'               => 'nullable|exists:clients,id',
            'deal_id'                => 'nullable|exists:deals,id',
            'organization_contact_id' => 'nullable|exists:organization_contacts,id',
            'type'                    => 'nullable|integer',
            'subject'                 => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'summary'                 => 'nullable|string',
            'status'                  => 'nullable|integer',
            'start_at'                => 'nullable|date',
            'end_at'                  => 'nullable|date',
            'performed_by'            => 'nullable|exists:users,id',
            'external_reference'      => 'nullable|string|max:255',
        ];
    }
}
