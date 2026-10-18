<?php

namespace App\Http\Requests\ExternalConversation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExternalConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform'                 => 'nullable|string|max:50',
            'external_conversation_id' => 'nullable|string|max:255',
            'client_id'                => 'nullable|exists:clients,id',
            'last_interaction_at'      => 'nullable|date',
            'metadata'                 => 'nullable|array',
        ];
    }
}
