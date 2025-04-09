<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendFriendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Already authenticated through middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'friend_id' => 'required|integer|exists:users,id|not_in:' . $this->user()->id,
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
            'friend_id.not_in' => 'You cannot send a friend request to yourself',
        ];
    }
}