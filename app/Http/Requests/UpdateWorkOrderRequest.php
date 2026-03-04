<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['sometimes', 'exists:assets,id'],
            'technician_id' => ['sometimes', 'exists:users,id'],
            'type' => ['sometimes', Rule::in(['preventive', 'corrective', 'emergency', 'inspection'])],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'checklist' => ['nullable', 'array'],
            'scheduled_date' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'verified', 'cancelled'])],
        ];
    }
}