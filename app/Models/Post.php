<?php

namespace App\Models;

use App\Enums\PostContext;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_id',
        'content',
        'post_context',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'event_id' => 'integer',
        'ticket_id' => 'integer',
        'post_context' => PostContext::class,
        'content' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = [
        'upvotes',
        'downvotes',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public function isUpvotedBy(User $user)
    {
        return $this->upvotes()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public function isDownvotedBy(User $user)
    {
        return $this->downvotes()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the upvotes count for the post.
     * 
     * @return int
     */
    public function getUpvotesAttribute()
    {
        return $this->upvotes()->count();
    }

    /**
     * Get the downvotes count for the post.
     * 
     * @return int
     */
    public function getDownvotesAttribute()
    {
        return $this->downvotes()->count();
    }

    /**
     * Get the upvotes for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PostVote, Post>
     */
    public function upvotes()
    {
        return $this->hasMany(PostVote::class)
            ->where('vote_type', 'upvote');
    }

    /**
     * Get the downvotes for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PostVote, Post>
     */
    public function downvotes()
    {
        return $this->hasMany(PostVote::class)
            ->where('vote_type', 'downvote');
    }

    /**
     * The user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Post>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The event that the post is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Event, Post>
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * The ticket that the post is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Ticket, Post>
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function votes()
    {
        return $this->hasMany(PostVote::class);
    }
}
