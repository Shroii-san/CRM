<?php

namespace App\Http\Requests\Deal;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'client_id'          => 'required|exists:clients,id',
            'pipeline_id'        => 'required|exists:pipelines,id',
            'current_stage_id'   => 'required|exists:pipeline_stages,id',
            'assigned_user_id'   => 'nullable|exists:users,id',
            'description'        => 'nullable|string',
            'currency'           => 'nullable|string|size:3',
            'value'              => 'nullable|numeric|min:0',
            'status'             => 'nullable|integer',
            'priority'           => 'nullable|integer',
            'expected_closed_at' => 'required|date',
            'actual_closed_at'   => 'nullable|date',
            'is_active'          => 'nullable|boolean',
        ];
    }
}
