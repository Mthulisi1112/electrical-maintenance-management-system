<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'maintenance_schedule_id' => ['nullable', 'exists:maintenance_schedules,id'],
            'technician_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['preventive', 'corrective', 'emergency', 'inspection'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'checklist' => ['nullable', 'array'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'critical'])],
        ];
    }
}