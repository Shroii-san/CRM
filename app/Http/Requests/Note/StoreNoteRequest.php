<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable|exists:clients,id',
            'deal_id'   => 'nullable|exists:deals,id',
            'content'   => 'required|string',
            'note_type' => 'nullable|string|max:50',
        ];
    }
}
