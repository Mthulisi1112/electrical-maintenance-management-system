<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolveFaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'root_cause' => ['required', 'string'],
            'corrective_actions' => ['required', 'string'],
            'parts_replaced' => ['nullable', 'array'],
            'parts_replaced.*.name' => ['required_with:parts_replaced', 'string'],
            'parts_replaced.*.quantity' => ['required_with:parts_replaced', 'integer', 'min:1'],
            'parts_replaced.*.part_number' => ['nullable', 'string'],
        ];
    }
}