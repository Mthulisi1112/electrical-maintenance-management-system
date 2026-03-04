<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'fault_type' => ['required', Rule::in(['trip', 'overload', 'short_circuit', 'earth_fault', 'overheating', 'mechanical', 'other'])],
            'severity' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'description' => ['required', 'string'],
            'symptoms' => ['nullable', 'array'],
            'symptoms.*' => ['string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'requires_followup' => ['boolean'],
        ];
    }
}