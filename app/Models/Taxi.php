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
        'driver_id', 'name', 'plate_number', 'image', 'image_gallery', 'year',
        'color', 'capacity', 'luggage', 'type', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'image_gallery' => 'array',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->getImages()[0] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public function getImages(): array
    {
        $gallery = $this->getAttribute('image_gallery') ?? [];
        $images = is_array($gallery) ? $gallery : (array) json_decode((string) $gallery, true);

        $single = $this->getAttribute('image');
        if ($single) {
            array_unshift($images, $single);
        }

        $resolved = [];
        foreach (array_unique(array_filter($images)) as $path) {
            if (preg_match('#^https?://#', $path) || str_starts_with($path, '/')) {
                $resolved[] = $path;
            } else {
                $resolved[] = Storage::url($path);
            }
        }

        return $resolved;
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
