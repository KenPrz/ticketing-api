<?php

namespace App\Models;

use App\Enums\EventCategory;
use App\Enums\EventImageType;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'date' => 'date',
        'longitude' => 'float',
        'latitude' => 'float',
        'category' => EventCategory::class,
    ];

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
     * Get the images for the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(EventImage::class, 'event_id');
    }

    /**
     * Get the banner image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function banner()
    {
        return $this->hasOne(EventImage::class, 'event_id')
            ->where('image_type', EventImageType::BANNER);
    }

    /**
     * Get the thumbnail image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thumbnail()
    {
        return $this->hasOne(EventImage::class, 'event_id')
            ->where('image_type', EventImageType::THUMBNAIL);
    }

    /**
     * Get the venue image for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function venueImage()
    {
        return $this->hasOne(EventImage::class, 'event_id')
            ->where('image_type', EventImageType::VENUE);
    }

    /**
     * Get the gallery images for the event.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gallery()
    {
        return $this->hasMany(EventImage::class, 'event_id')
            ->where('image_type', EventImageType::GALLERY);
    }
}