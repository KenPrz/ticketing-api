<?php

namespace App\Models;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Model;

class EventTicketTier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_ticket_tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'tier_name',
        'price',
        'quantity',
        'ticket_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'ticket_type' => TicketType::class,
    ];

    /**
     * Get the event that the ticket tier belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the tickets for this ticket tier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_tier_id');
    }

    /**
     * Get all seats through tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function seats()
    {
        return $this->hasManyThrough(
            Seat::class,
            Ticket::class,
            'ticket_tier_id',
            'ticket_id',
            'id',
            'id',
        );
    }
}