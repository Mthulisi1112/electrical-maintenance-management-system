<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'checklist_items' => ['required', 'array', 'min:1'],
            'checklist_items.*' => ['required', 'string'],
            'required_tools' => ['nullable', 'array'],
            'required_tools.*' => ['string'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
        ];
    }
}