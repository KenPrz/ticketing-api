<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    /**
     * Get the messages for the chat.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Validation rules.
     */
    public static function rules()
    {
        return [
            'user_one_id' => 'required|exists:users,id',
            'user_two_id' => 'required|exists:users,id|different:user_one_id',
        ];
    }

    /**
     * Get the first user of the chat.
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Get the second user of the chat.
     */
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Get the other user in the conversation relative to the given user.
     */
    public function getOtherUser($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        if ($this->user_one_id == $userId) {
            return $this->userTwo;
        }
        
        return $this->userOne;
    }

    /**
     * Check if the user is part of this chat.
     */
    public function hasUser($userId)
    {
        return $this->user_one_id == $userId || $this->user_two_id == $userId;
    }

    /**
     * Get unread message count for a specific user.
     */
    public function unreadCount($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return $this->messages()
            ->where('read_status', \App\Enums\ReadStatus::UNREAD)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    /**
     * Get the last message in the chat.
     */
    public function lastMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Scope a query to retrieve chats that include a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId);
    }
}