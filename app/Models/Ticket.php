<?php

namespace App\Models;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qr_code',
        'ticket_name',
        'event_id',
        'owner_id',
        'ticket_tier_id',
        'purchase_id',
        'ticket_type',
        'ticket_desc',
        'is_used',
        'used_on',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'used_on' => 'datetime',
        'ticket_type' => TicketType::class,
        'is_used' => 'boolean',
    ];

    /**
     * Get the event that the ticket belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user that owns the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the ticket tier that the ticket belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticketTier()
    {
        return $this->belongsTo(EventTicketTier::class, 'ticket_tier_id');
    }

    /**
     * Get the purchase that this ticket belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Get the seat associated with this ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function seat()
    {
        return $this->hasOne(Seat::class, 'ticket_id');
    }

    /**
     * The transfer history for the ticket.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transferHistory()
    {
        return $this->hasMany(TicketTransferHistory::class, 'ticket_id');
    }

    /**
     * Get the latest transfer status for the ticket.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transferStatus()
    {
        return $this->hasOne(TicketTransferHistory::class, 'ticket_id')
            ->latestOfMany();
    }
}
