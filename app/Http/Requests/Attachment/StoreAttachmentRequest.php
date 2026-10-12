<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'            => 'required|file|max:10240', // Maks 10MB
            'description'     => 'nullable|string',
            'attachable_type' => 'required|string|max:255',
            'attachable_id'   => 'required|integer',
        ];
    }
}
