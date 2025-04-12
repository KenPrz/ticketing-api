<?php

namespace App\Http\Requests;

use App\Enums\EventCategory;
use App\Enums\TicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        
        // If tickets is an empty JSON array, mark it as valid but empty
        if ($this->has('tickets') && $this->input('tickets') === '[]') {
            $this->merge(['tickets_data' => []]);
        }
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
            // Event attributes
            'name' => 'required|string|max:255',
            'organizer_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string|max:1000',
            'venue' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'city' => 'required|string|max:255',
            'isPublished' => 'required|boolean',
            'category' => 'required|in:'
                . implode(
                    ',',
                    array_map(
                        fn($case) => $case->value, EventCategory::cases()
                    )
                ),
                
            // Image attributes
            'banner' => 'required|file|image|max:5120',
            'thumbnail' => 'required|file|image|max:2048',
            'venueImage' => 'required|file|image|max:5120',
            'seatPlanImage' => 'nullable|file|image|max:5120', // Added seat plan image validation
            'gallery' => 'nullable|array',
            'gallery.*' => 'file|image|max:5120',

            // Ticket attributes
            'tickets' => 'required|json',
            'capacity' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Skip this validation if tickets JSON is invalid
            if ($validator->errors()->has('tickets')) {
                return;
            }
            
            // Skip if we've already determined it's an empty array
            if ($this->has('tickets_data') && empty($this->input('tickets_data'))) {
                return;
            }
            
            // Parse the tickets JSON to an array
            $ticketsData = json_decode($this->input('tickets'), true);
            
            // Skip validation if there are no tickets
            if (empty($ticketsData)) {
                return;
            }
            
            // Add the parsed tickets to the request for validation
            $this->merge(['tickets_data' => $ticketsData]);
            
            // Validate each ticket
            foreach ($ticketsData as $index => $ticket) {
                // Validate required fields
                foreach (['tier_name', 'price', 'quantity', 'ticket_type'] as $field) {
                    if (!isset($ticket[$field]) || empty($ticket[$field])) {
                        $validator->errors()->add(
                            "tickets_data.$index.$field", 
                            "The $field field is required for ticket tier #" . ($index + 1)
                        );
                    }
                }
                
                // Validate numeric fields
                if (isset($ticket['price']) && !is_numeric($ticket['price'])) {
                    $validator->errors()->add(
                        "tickets_data.$index.price", 
                        "The price must be a number for ticket tier #" . ($index + 1)
                    );
                }
                
                if (isset($ticket['quantity'])) {
                    if (!is_numeric($ticket['quantity'])) {
                        $validator->errors()->add(
                            "tickets_data.$index.quantity", 
                            "The quantity must be a number for ticket tier #" . ($index + 1)
                        );
                    } elseif ((int)$ticket['quantity'] < 1) {
                        $validator->errors()->add(
                            "tickets_data.$index.quantity", 
                            "The quantity must be at least 1 for ticket tier #" . ($index + 1)
                        );
                    }
                }
                
                // Validate ticket type
                if (isset($ticket['ticket_type'])) {
                    $validTypes = array_map(fn($case) => $case->value, TicketType::cases());
                    if (!in_array($ticket['ticket_type'], $validTypes)) {
                        $validator->errors()->add(
                            "tickets_data.$index.ticket_type", 
                            "The selected ticket type is invalid for ticket tier #" . ($index + 1)
                        );
                    }
                }
            }
            
            // Check for duplicate ticket types
            if (!empty($ticketsData)) {
                $ticketTypes = collect($ticketsData)->pluck('ticket_type')->toArray();
                $uniqueTicketTypes = array_unique($ticketTypes);
                
                if (count($ticketTypes) !== count($uniqueTicketTypes)) {
                    $validator->errors()->add(
                        'tickets', 
                        'Duplicate ticket types are not allowed. Each ticket type can only be used once.'
                    );
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            // Event attributes
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
            'isPublished' => 'event publication status',

            // Image attributes
            'banner' => 'banner image',
            'thumbnail' => 'thumbnail image',
            'venueImage' => 'venue image',
            'seatPlanImage' => 'seat plan image', // Added seat plan image attribute
            'gallery' => 'gallery images',
            'gallery.*' => 'gallery image',
            
            // Ticket attributes
            'tickets' => 'ticket tiers',
            'capacity' => 'event capacity',
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
            // Event validation messages
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
            'isPublished.required' => 'The :attribute is required.',

            // Image validation messages
            'banner.required' => 'The :attribute is required.',
            'thumbnail.required' => 'The :attribute is required.',
            'venueImage.required' => 'The :attribute is required.',
            'seatPlanImage.image' => 'The :attribute must be an image.', // Added seat plan message
            'seatPlanImage.max' => 'The :attribute may not be greater than 5120 kilobytes.', // Added seat plan message
            'gallery.required' => 'The :attribute is required.',
            'gallery.*.required' => 'The :attribute is required.',
            'banner.file' => 'The :attribute must be a file.',
            'thumbnail.file' => 'The :attribute must be a file.',
            'venueImage.file' => 'The :attribute must be a file.',
            'seatPlanImage.file' => 'The :attribute must be a file.', // Added seat plan message
            'gallery.*.file' => 'The :attribute must be a file.',
            'banner.image' => 'The :attribute must be an image.',
            'thumbnail.image' => 'The :attribute must be an image.',
            'venueImage.image' => 'The :attribute must be an image.',
            'gallery.*.image' => 'The :attribute must be an image.',
            'banner.max' => 'The :attribute may not be greater than 5120 kilobytes.',
            'thumbnail.max' => 'The :attribute may not be greater than 2048 kilobytes.',
            'venueImage.max' => 'The :attribute may not be greater than 5120 kilobytes.',
            'gallery.*.max' => 'The :attribute may not be greater than 5120 kilobytes.',
            
            // Ticket validation messages
            'tickets.required' => 'Ticket information is required.',
            'tickets.json' => 'Ticket information must be in valid JSON format.',
            'capacity.integer' => 'The event capacity must be a number.',
            'capacity.min' => 'The event capacity must be at least 1.',
        ];
    }
}