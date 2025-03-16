<?php

namespace App\Models;

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
        'city',
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
        return $this->hasManyThrough(Ticket::class, EventTicketTier::class);
    }
}