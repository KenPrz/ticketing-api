<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Seat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
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
        $rules = [
            // Validate seat IDs array
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => [
                'required',
                'integer',
                'exists:seats,id',
                function ($attribute, $value, $fail) {
                    // Check if the seat is already purchased/occupied
                    $seatStatus = Seat::where('id', $value)
                        ->value('is_occupied');
                    
                    if ($seatStatus) {
                        $fail("The seat has already been purchased by another user.");
                    }
                },
            ],
            
            // Validate payment method using the enum
            'payment_method' => ['required', 'string', Rule::enum(PaymentMethod::class)],
        ];
        
        // Simplified payment details validation
        if ($this->input('payment_method') === PaymentMethod::DEBIT_CARD->value) {
            $rules['payment_details'] = 'nullable|array';
        } elseif ($this->input('payment_method') === PaymentMethod::GCASH->value || 
                  $this->input('payment_method') === PaymentMethod::MAYA->value) {
            $rules['payment_details'] = 'nullable|array';
        }
        
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'seat_ids.required' => 'You must select at least one seat.',
            'seat_ids.array' => 'The seat IDs must be provided as an array.',
            'seat_ids.min' => 'You must select at least one seat.',
            'seat_ids.*.required' => 'Each seat ID is required.',
            'seat_ids.*.integer' => 'Each seat ID must be an integer.',
            'seat_ids.*.exists' => 'One or more selected seats do not exist.',
            
            'payment_method.required' => 'Payment method is required.',
            'payment_method.enum' => 'The selected payment method is not supported.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Ensure payment details is an array when it's submitted as null
        if (!$this->has('payment_details')) {
            $this->merge(['payment_details' => []]);
        }
    }
}