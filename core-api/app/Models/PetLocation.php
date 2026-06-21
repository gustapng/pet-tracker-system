<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetLocation extends Model
{
    protected $fillable = [ 
        'pet_id',
        'latitude',
        'longitude',
        'speed',
        'battery_level',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
