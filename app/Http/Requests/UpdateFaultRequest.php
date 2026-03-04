<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['sometimes', 'exists:assets,id'],
            'fault_type' => ['sometimes', Rule::in(['trip', 'overload', 'short_circuit', 'earth_fault', 'overheating', 'mechanical', 'other'])],
            'severity' => ['sometimes', Rule::in(['low', 'medium', 'high', 'critical'])],
            'description' => ['sometimes', 'string'],
            'symptoms' => ['nullable', 'array'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['sometimes', Rule::in(['reported', 'investigating', 'in_progress', 'resolved', 'closed'])],
        ];
    }
}