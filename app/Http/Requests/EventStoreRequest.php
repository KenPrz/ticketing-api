<?php

namespace App\Http\Requests;

use App\Enums\EventCategory;
use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'organizer_id' => $this->user()->id,
        ]);
    }

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
            'name' => 'required|string|max:255',
            'organizer_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string|max:1000',
            'venue' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'city' => 'required|string|max:255',
            'category' => 'required|in:'
                . implode(
                    ',',
                    array_map(
                        fn($case) => $case->value, EventCategory::cases()
                    )
                ),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'event name',
            'organizer_id' => 'organizer ID',
            'date' => 'event date',
            'time' => 'event time',
            'description' => 'event description',
            'venue' => 'event venue',
            'longitude' => 'event longitude',
            'latitude' => 'event latitude',
            'city' => 'event city',
            'category' => 'event category',
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
            'name.required' => 'The :attribute is required.',
            'organizer_id.required' => 'The :attribute is required.',
            'date.required' => 'The :attribute is required.',
            'time.required' => 'The :attribute is required.',
            'description.required' => 'The :attribute is required.',
            'venue.required' => 'The :attribute is required.',
            'longitude.required' => 'The :attribute is required.',
            'latitude.required' => 'The :attribute is required.',
            'city.required' => 'The :attribute is required.',
            'category.required' => 'The :attribute is required.',
        ];
    }
}
