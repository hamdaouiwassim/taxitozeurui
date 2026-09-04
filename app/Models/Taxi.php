<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Taxi extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id', 'name', 'plate_number', 'image', 'year',
        'color', 'capacity', 'luggage', 'type', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        $image = $this->getAttribute('image');

        if (! $image) {
            return null;
        }

        if (preg_match('#^https?://#', $image) || str_starts_with($image, '/')) {
            return $image;
        }

        return Storage::url($image);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
