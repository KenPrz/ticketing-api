<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'placeholder_for_transaction_handler',
        'event_id',
        'purchased_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    /**
     * Get the event that the purchase belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user that purchases the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    /**
     * Get the tickets associated with this purchase.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'purchase_id');
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
            'purchase_id',
            'ticket_id',
            'id',   
            'id',
        );
    }
}