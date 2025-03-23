<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBookmark extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_bookmarks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'event_id',
    ];

    /**
     * Get the event that was bookmarked.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user that bookmarked the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
