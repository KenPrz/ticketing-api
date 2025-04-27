<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'content' => 'required|string|max:1000',
            'post_context' => 'required|string',
            'event_id' => 'nullable|exists:events,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Post content is required',
            'content.max' => 'Post content cannot exceed 1000 characters',
            'post_context.required' => 'Post context is required',
            'event_id.exists' => 'The selected event does not exist',
            'ticket_id.exists' => 'The selected ticket does not exist',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',
        ];
    }
}