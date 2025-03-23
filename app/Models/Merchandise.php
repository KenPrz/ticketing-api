<?php

namespace App\Models;

use App\Enums\EventImageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merchandise extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'merchandises';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'stock',
        'status',
    ];

    /**
     * Get the event that owns the merchandise.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the merchandise's image.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(EventImage::class, 'imageable')
            ->where('image_type', EventImageType::MERCHANDISE);
    }
}