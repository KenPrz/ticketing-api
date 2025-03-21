<?php

namespace App\Models;

use App\Enums\EventImageType;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'image_url',
        'image_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_id' => 'integer',
        'image_url' => 'string',
        'image_type' => EventImageType::class,
    ];

    /**
     * Get the event that the image belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
