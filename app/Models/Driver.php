<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'avatar', 'phone', 'whatsapp', 'location',
        'rating', 'reviews_count', 'trips', 'years_experience',
        'email', 'password', 'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = $this->getAttribute('avatar');

        if (! $avatar) {
            return null;
        }

        if (preg_match('#^https?://#', $avatar) || str_starts_with($avatar, '/')) {
            return $avatar;
        }

        return Storage::url($avatar);
    }

    public function taxi(): HasOne
    {
        return $this->hasOne(Taxi::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
