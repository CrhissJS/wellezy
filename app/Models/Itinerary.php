<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itinerary extends Model
{
    protected $fillable = [
        'departure_city',
        'arrival_city',
        'departure_date',
        'arrival_date',
        'departure_time',
        'arrival_time',
        'flight_number',
        'marketing_carrier'
    ];

    public function reserve(): BelongsTo
    {
        return $this->belongsTo(Reserve::class);
    }
}