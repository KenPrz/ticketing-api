<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => [
                'sometimes',
                'string',
                Rule::unique('vouchers', 'code')->ignore($this->route('id')),
            ],
            'discount' => 'sometimes|integer|min:0',
            'organizer_id' => 'sometimes|integer|exists:organizers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The voucher name is required.',
            'code.unique' => 'This voucher code is already in use.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'discount.min' => 'The discount value cannot be negative.',
        ];
    }
}