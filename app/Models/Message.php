<?php

namespace App\Models;

use App\Enums\ReadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'read_status',
        'chat_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'read_status' => ReadStatus::class,
        'content' => 'string',
    ];

    /**
     * Get the chat that owns the message.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the user who sent this message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this message was sent by the given user.
     */
    public function isSentBy($userId)
    {
        return $this->user_id === $userId;
    }

    /**
     * Validation rules.
     */
    public static function rules()
    {
        return [
            'content' => 'required|string',
            'read_status' => 'required|in:' . implode(',', ReadStatus::getValues()),
            'chat_id' => 'required|exists:chats,id',
            'user_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Mark this message as read.
     */
    public function markAsRead()
    {
        if ($this->read_status === ReadStatus::UNREAD) {
            $this->update(['read_status' => ReadStatus::READ]);
        }
        
        return $this;
    }
}