<?php

namespace App\Http\Requests\ExternalConversation;

use Illuminate\Foundation\Http\FormRequest;

class StoreExternalConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform'                 => 'required|string|max:50',
            'external_conversation_id' => 'required|string|max:255',
            'client_id'                => 'required|exists:clients,id',
            'last_interaction_at'      => 'nullable|date',
            'metadata'                 => 'nullable|array',
        ];
    }
}
