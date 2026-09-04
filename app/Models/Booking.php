<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id', 'customer_name', 'customer_phone',
        'pickup_location', 'dropoff_location', 'pickup_time',
        'status', 'fare',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'fare' => 'decimal:2',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
