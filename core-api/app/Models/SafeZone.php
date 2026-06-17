<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafeZone extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'latitude', 'longitude', 'radius_meters'];
}
