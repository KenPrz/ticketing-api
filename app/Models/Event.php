<?php

namespace App\Models;

use App\Enums\EventCategory;
use App\Enums\EventImageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'organizer_id',
        'date',
        'time',
        'description',
        'venue',
        'longitude',
        'latitude',
        'city',
        'category',
        'is_published',
        'is_cancelled',
        'cancelled_reason',
        'cancelled_at',
        'published_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime',
        'published_at' => 'datetime',
        'organizer_id' => 'integer',
        'city' => 'string',
        'name' => 'string',
        'description' => 'string',
        'venue' => 'string',
        'time' => 'string',
        'longitude' => 'float',
        'latitude' => 'float',
        'category' => EventCategory::class,
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = ['is_bookmarked'];

    /**
     * Get the category of the event.
     *
     * @return bool check if the event is bookmarked by the authenticated user.
     */
    public function getIsBookmarkedAttribute()
    {
        if (!Auth::check()) {
            return false;
        }
        
        return $this->eventBookmarks()
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * Get the user that organizes the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the tickets for the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketTiers()
    {
        return $this->hasMany(EventTicketTier::class, 'event_id');
    }

    /**
     * Get all tickets for the event through ticket tiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tickets()
    {
        return $this->hasManyThrough(
            Ticket::class,
            EventTicketTier::class,
            'event_id',
            'ticket_tier_id',
            'id',
            'id',
        );
    }

    /**
     * Get all purchases for this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'event_id');
    }

    /**
     * Get all seats for this event through tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function seats()
    {
        return $this->hasManyThrough(
            Seat::class,
            Ticket::class,
            'event_id',
            'ticket_id',
            'id',
            'id'
        );
    }

    /**
     * Get all seats for this event through ticket tiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seatsById()
    {
        return $this->hasMany(
            Seat::class,
            'event_id',
            'id'
        );
    }

    /**
     * Get the images for the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(EventImage::class, 'imageable');
    }

    /**
     * Get the banner image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function banner()
    {
        return $this->morphOne(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::BANNER);
    }

    /**
     * Get the thumbnail image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function thumbnail()
    {
        return $this->morphOne(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::THUMBNAIL);
    }

    /**
     * Get the venue image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function venueImage()
    {
        return $this->morphOne(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::VENUE);
    }

    /**
     * Get the gallery images for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function gallery()
    {
        return $this->morphMany(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::GALLERY);
    }

    /**
     * Get the merchandise items for the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function merchandise()
    {
        return $this->hasMany(Merchandise::class, 'event_id');
    }

    /**
     * Get the merchandise image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function seatPlanImage()
    {
        return $this->morphOne(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::SEAT_PLAN);
    }

    /**
     * Get the users who have bookmarked this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventBookmarks()
    {
        return $this->belongsToMany(
            User::class,
            'event_bookmarks',
            'event_id',
            'user_id',
        );
    }

    /**
     * Get the event reviews for this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventReviews()
    {
        return $this->hasMany(Post::class, 'event_id');
    }
}