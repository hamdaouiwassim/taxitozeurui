<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'avatar', 'phone', 'whatsapp', 'location',
        'work_start', 'work_end', 'work_days',
        'rating', 'reviews_count', 'trips', 'years_experience',
        'email', 'password', 'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
        'work_days' => 'array',
    ];

    public function getWorkingHoursAttribute(): ?string
    {
        $start = $this->getAttribute('work_start');
        $end = $this->getAttribute('work_end');

        if (! $start || ! $end) {
            return null;
        }

        $fmt = fn (string $t): string => date('h:i A', strtotime($t));

        return $fmt($start).' - '.$fmt($end);
    }

    public function getWorkDaysLabelsAttribute(): ?string
    {
        $days = $this->getAttribute('work_days');

        if (empty($days)) {
            return null;
        }

        $labels = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

        return collect($days)
            ->sort()
            ->map(fn ($d) => $labels[$d] ?? $d)
            ->implode(', ');
    }

    public function isWorkingNow(?Carbon $at = null): bool
    {
        $at ??= Carbon::now();

        $day = (int) $at->isoWeekday();
        $time = $at->format('H:i');

        $days = $this->getAttribute('work_days');
        $start = $this->getAttribute('work_start');
        $end = $this->getAttribute('work_end');

        if (is_array($days) && count($days) > 0 && ! in_array($day, $days, true)) {
            return false;
        }

        if ($start && $end) {
            if ($start <= $end) {
                return $time >= $start && $time < $end;
            }

            return $time >= $start || $time < $end;
        }

        return true;
    }

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
