<?php

namespace App\Http\Requests;

use App\Enums\EventImageType;
use Illuminate\Foundation\Http\FormRequest;

class EventAddImagesRequest extends FormRequest
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
            'banner' => 'required|file|image|max:5120',
            'thumbnail' => 'required|file|image|max:2048',
            'venueImage' => 'required|file|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'file|image|max:5120',
        ];
    }
}