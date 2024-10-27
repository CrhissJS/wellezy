<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Reserve",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="passenger_count", type="integer"),
 *     @OA\Property(property="adult_count", type="integer"),
 *     @OA\Property(property="child_count", type="integer"),
 *     @OA\Property(property="baby_count", type="integer"),
 *     @OA\Property(property="total_amount", type="number", format="float"),
 *     @OA\Property(property="currency", type="string")
 * )
 */
class Reserve extends Model
{
    protected $fillable = [
        'name',
        'email',
        'passenger_count',
        'adult_count',
        'child_count',
        'baby_count',
        'total_amount',
        'currency'
    ];

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }
}   