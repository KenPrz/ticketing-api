<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FriendActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'friend_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'friend_id.required' => 'A friend ID is required',
            'friend_id.integer' => 'The friend ID must be an integer',
            'friend_id.exists' => 'The specified user does not exist',
        ];
    }
}