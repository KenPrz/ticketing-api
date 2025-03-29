<?php

namespace App\Models;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'event_id',
        'row',
        'number',
        'section',
        'is_occupied',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ticket_id' => 'integer', // This will handle null values automatically
        'event_id' => 'integer', // Add event_id cast
        'is_occupied' => 'boolean',
        'section' => TicketType::class,
    ];

    /**
     * Get the ticket this seat belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Get the event this seat belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the event through the ticket.
     * This uses a dynamic property to access the relationship through ticket.
     * Now serves as a fallback if using the direct event relationship doesn't work
     */
    public function getEventAttribute()
    {
        return $this->ticket ? $this->ticket->event : $this->event;
    }

    /**
     * Get the owner through the ticket.
     * This uses a dynamic property to access the relationship through ticket.
     */
    public function getOwnerAttribute()
    {
        return $this->ticket ? $this->ticket->owner : null;
    }

    /**
     * Get the purchase through the ticket.
     * This uses a dynamic property to access the relationship through ticket.
     */
    public function getPurchaseAttribute()
    {
        return $this->ticket ? $this->ticket->purchase : null;
    }

    /**
     * Get the ticket tier through the ticket.
     * This uses a dynamic property to access the relationship through ticket.
     */
    public function getTicketTierAttribute()
    {
        return $this->ticket ? $this->ticket->ticketTier : null;
    }

    /**
     * Check if the seat is available for assignment.
     * 
     * @return bool
     */
    public function isAvailable()
    {
        return !$this->is_occupied && $this->ticket_id === null;
    }
}