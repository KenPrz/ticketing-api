<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class FetchByContactsRequest extends FormRequest
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
        Log::info(json_encode($this->all()));
        return [
            'phoneNumbers' => 'required|array',
            'phoneNumbers.*' => 'required|string|regex:/^[0-9+\-\s()*#]+$/|max:20'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('phoneNumbers') && is_array($this->phoneNumbers)) {
            // Clean up each phone number
            $phoneNumbers = collect($this->phoneNumbers)
                ->map(function ($number) {
                    return $this->sanitizePhoneNumber($number);
                })
                ->filter() 
                ->unique() 
                ->values()
                ->all();
            
            $this->merge(['phoneNumbers' => $phoneNumbers]);
        }
    }
    
    /**
     * Sanitize a phone number for processing.
     *
     * @param string $number
     * @return string
     */
    protected function sanitizePhoneNumber($number)
    {
        // Special case for service codes like *133#
        if (preg_match('/^\*\d+#$/', $number)) {
            return $number;
        }
        
        // Remove all non-digit characters except + for international format
        $cleaned = preg_replace('/[^\d+]/', '', $number);
        
        // Return the number if it's valid
        if (!empty($cleaned)) {
            return $cleaned;
        }
        
        return $number;
    }
}