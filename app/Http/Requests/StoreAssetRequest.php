<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:255', 'unique:assets'],
            'type' => ['required', Rule::in(['motor', 'transformer', 'mcc', 'distribution_board', 'vfd', 'switchgear', 'cable', 'other'])],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'voltage_rating' => ['nullable', 'numeric', 'min:0'],
            'current_rating' => ['nullable', 'numeric', 'min:0'],
            'power_rating' => ['nullable', 'numeric', 'min:0'],
            'installation_date' => ['required', 'date', 'before_or_equal:today'],
            'technical_specs' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_code.required' => 'The asset code is required.',
            'asset_code.unique' => 'This asset code is already in use.',
            'type.required' => 'Please select an asset type.',
            'name.required' => 'The asset name is required.',
            'location.required' => 'The location is required.',
            'installation_date.before_or_equal' => 'Installation date cannot be in the future.',
        ];
    }
}