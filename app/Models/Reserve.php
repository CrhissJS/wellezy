<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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