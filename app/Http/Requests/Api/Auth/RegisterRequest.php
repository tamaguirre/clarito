<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'education_level_id' => ['required', 'integer', Rule::exists('education_levels', 'id')->whereNull('deleted_at')],
            'conditions' => ['required', 'array', 'min:1'],
            'conditions.*' => ['required', 'integer', 'distinct', Rule::exists('conditions', 'id')->whereNull('deleted_at')],
        ];
    }
}
