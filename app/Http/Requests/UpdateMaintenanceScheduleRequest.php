<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['sometimes', 'exists:assets,id'],
            'frequency' => ['sometimes', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'checklist_items' => ['sometimes', 'array'],
            'required_tools' => ['nullable', 'array'],
            'estimated_duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'start_date' => ['sometimes', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'critical'])],
        ];
    }
}