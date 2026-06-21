<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PetLocation;
use Illuminate\Http\Request;

class LocationHistoryController extends Controller
{
    public function getHistory($petId)
    {
        $locations = PetLocation::where('pet_id', $petId)
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get([
                'latitude', 
                'longitude', 
                'speed', 
                'battery_level', 
                'recorded_at'
            ]);

        if ($locations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum histórico encontrado para este pet.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $locations
        ]);
    }
}
