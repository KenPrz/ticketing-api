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
        
        // Add conditional validation for payment details based on payment method
        if ($this->input('payment_method') === PaymentMethod::DEBIT_CARD->value) {
            $rules['payment_details'] = 'required|array';
            $rules['payment_details.card_holder'] = 'required|string|max:255';
            $rules['payment_details.card_number'] = 'required|string|min:13|max:19';
            $rules['payment_details.expiry_month'] = 'required|string|size:2|in:01,02,03,04,05,06,07,08,09,10,11,12';
            $rules['payment_details.expiry_year'] = 'required|string|size:2|min:23';
            $rules['payment_details.cvv'] = 'required|string|size:3';
        } elseif ($this->input('payment_method') === PaymentMethod::GCASH->value || 
                  $this->input('payment_method') === PaymentMethod::MAYA->value) {
            // For digital wallets, you might want to validate some other fields
            // This is just an example - adjust according to your actual requirements
            $rules['payment_details'] = 'nullable|array';
            $rules['payment_details.phone_number'] = 'sometimes|string|regex:/^[+]?[0-9]{10,15}$/';
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
            // The custom validation message for occupied seats is defined in the closure
            
            'payment_method.required' => 'Payment method is required.',
            'payment_method.enum' => 'The selected payment method is not supported.',

            'payment_details.required' => 'Payment details are required for card payments.',
            'payment_details.card_holder.required' => 'Cardholder name is required.',
            'payment_details.card_number.required' => 'Card number is required.',
            'payment_details.card_number.min' => 'Card number must be at least 13 digits.',
            'payment_details.card_number.max' => 'Card number cannot exceed 19 digits.',
            'payment_details.expiry_month.required' => 'Expiration month is required.',
            'payment_details.expiry_month.in' => 'Expiration month must be a valid month (01-12).',
            'payment_details.expiry_year.required' => 'Expiration year is required.',
            'payment_details.cvv.required' => 'CVV code is required.',
            'payment_details.cvv.size' => 'CVV code must be 3 digits.',
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
        if ($this->input('payment_method') !== PaymentMethod::DEBIT_CARD->value && !$this->has('payment_details')) {
            $this->merge(['payment_details' => []]);
        }
    }
}