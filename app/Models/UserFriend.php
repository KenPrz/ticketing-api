<?php

namespace App\Models;

use App\Enums\FriendStatus;
use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_friends';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'status' => FriendStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
