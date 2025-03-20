<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusStopScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bus_id' => $this->bus_id,
            'bus_stop_id' => $this->bus_stop_id,
            'expected_arrival_time' => $this->expected_arrival_time,
            'route_stop_order' => $this->route_stop_order,
            // 'bus' => new BusResource($this->whenLoaded('bus')), 
            // 'bus_stop' => new BusStopResource($this->whenLoaded('busStop')),
        ];
    }
}
