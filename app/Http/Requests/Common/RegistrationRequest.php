<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'mobile' => [
                'required',
                'string',
                'regex:/^(?:\+639\d{9}|09\d{9}|0\d{1,2}\d{7})$/',
                'unique:users,mobile',
                'max:13',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
