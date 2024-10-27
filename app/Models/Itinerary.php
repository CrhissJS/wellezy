<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Itinerary",
 *     @OA\Property(property="departure_city", type="string"),
 *     @OA\Property(property="arrival_city", type="string"),
 *     @OA\Property(property="departure_date", type="string", format="date"),
 *     @OA\Property(property="arrival_date", type="string", format="date"),
 *     @OA\Property(property="departure_time", type="string", format="time"),
 *     @OA\Property(property="arrival_time", type="string", format="time"),
 *     @OA\Property(property="flight_number", type="string"),
 *     @OA\Property(property="marketing_carrier", type="string")
 * )
 */

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