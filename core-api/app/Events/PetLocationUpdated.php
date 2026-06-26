<?php

namespace App\Events;

use App\Models\PetLocation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PetLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location;

    public function __construct(PetLocation $location)
    {
        $this->location = $location;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pet.' . $this->location->pet_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
            'speed' => $this->location->speed,
            'battery_level' => $this->location->battery_level,
            'recorded_at' => $this->location->recorded_at,
        ];
    }
}