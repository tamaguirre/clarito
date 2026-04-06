<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteCompanyRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'company_type_id' => ['required', Rule::exists('company_types', 'id')],
            'phone' => ['required', 'string', 'max:50'],
            'short_description' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'dictionary' => ['required', 'array', 'min:1'],
            'dictionary.*.word' => ['required', 'string', 'max:120'],
            'dictionary.*.definition' => ['required', 'string', 'max:1000'],
        ];
    }
}
