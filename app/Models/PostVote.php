<?php

namespace App\Models;

use App\Enums\PostVoteType;
use Illuminate\Database\Eloquent\Model;

class PostVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'vote_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'post_id' => 'integer',
        'vote_type' => PostVoteType::class,
    ];

    /**
     * The user that owns the post vote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, PostVote>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The post that the vote is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Post, PostVote>
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
