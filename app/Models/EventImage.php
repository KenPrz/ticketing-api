<?php

namespace App\Models;

use App\Enums\EventImageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

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
        'image_url',
        'image_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image_type' => EventImageType::class,
    ];

    /**
     * Get the image URL attribute.
     *
     * @param string $value
     * @return string
     */
    public function getImageUrlAttribute($value)
    {
        // If it's null, return null
        if (empty($value)) {
            return null;
        }

        // If it's already a URL, return as is
        if (filter_var($value, FILTER_VALIDATE_URL) || str_starts_with($value, 'http')) {
            return $value;
        }

        // Otherwise, generate URL from the stored path
        return Storage::disk('public')->url($value);
    }

    /**
     * Get the parent imageable model.
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}