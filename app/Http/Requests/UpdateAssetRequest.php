<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:255', Rule::unique('assets')->ignore($this->asset)],
            'type' => ['required', Rule::in(['motor', 'transformer', 'mcc', 'distribution_board', 'vfd', 'switchgear', 'cable', 'other'])],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'voltage_rating' => ['nullable', 'numeric', 'min:0'],
            'current_rating' => ['nullable', 'numeric', 'min:0'],
            'power_rating' => ['nullable', 'numeric', 'min:0'],
            'installation_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['operational', 'maintenance', 'faulty', 'decommissioned'])],
            'technical_specs' => ['nullable', 'json'],
        ];
    }
}