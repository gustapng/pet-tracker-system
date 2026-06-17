<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Models\Pet;
use App\Models\SafeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    public function store(StorePetRequest $request) {
        DB::beginTransaction();

        try {
            $pet = Pet::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'breed' => $request->breed,
                'age' => $request->age,
            ]);

            SafeZone::create([
                'pet_id' => $pet->id,
                'latitude' => $request->safe_zone['latitude'],
                'longitude' => $request->safe_zone['longitude'],
                'radius_meters' => $request->safe_zone['radius_meters'] ?? 50,
            ]);

            DB::commit();

            return response()->json($pet, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao cadastrar o pet'], 500);
        }
    }

    public function index(Request $request) {
        $pets = Pet::where('user_id', $request->user()->id)->get();

        return response()->json($pets, 200);
    }
}
