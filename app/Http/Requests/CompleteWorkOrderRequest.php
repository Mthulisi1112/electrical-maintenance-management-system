<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'time_spent_minutes' => ['required', 'integer', 'min:1'],
            'checklist_responses' => ['nullable', 'array'],
            'checklist_responses.*' => ['boolean'],
            'parts_used' => ['nullable', 'array'],
            'parts_used.*.name' => ['required_with:parts_used', 'string'],
            'parts_used.*.quantity' => ['required_with:parts_used', 'integer', 'min:1'],
            'parts_used.*.part_number' => ['nullable', 'string'],
            'technician_remarks' => ['nullable', 'string'],
            'actions_taken' => ['required', 'string'],
            'measurements' => ['nullable', 'array'],
            'measurements.*.name' => ['required_with:measurements', 'string'],
            'measurements.*.value' => ['required_with:measurements', 'numeric'],
            'measurements.*.unit' => ['nullable', 'string'],
        ];
    }
}