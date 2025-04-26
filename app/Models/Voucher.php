<?php

namespace App\Models;

use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    /**
     * The fields that are mass assignable.
     *
     * @var array
     */
    protected $fillable =[
        'name',
        'code', 
        'discount',
        'start_date',
        'end_date',
        'organizer_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'code' => 'string',
        'discount' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'organizer_id' => 'integer',
    ];

    /**
     * The Organizer that owns the Voucher.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Voucher>
     */
    public function organizer()
    {
        return $this->belongsTo(User::class)
            ->where('user_type', UserTypes::ORGANIZER->value);
    }

    /**
     * Returns the state of the voucher if it is active or not.
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        if (
            is_null($this->start_date) &&
            is_null($this->end_date)
        ) {
            return true;
        }

        return now()->between(
            $this->start_date,
            $this->end_date
        );
    }
}
