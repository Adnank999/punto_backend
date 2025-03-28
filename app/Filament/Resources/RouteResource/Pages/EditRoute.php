<?php

namespace App\Filament\Resources\RouteResource\Pages;

use App\Filament\Resources\RouteResource;
use App\Models\RouteStop;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoute extends EditRecord
{
    protected static string $resource = RouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function saved(): void
    {
        parent::saved(); // You can call this to maintain the parent behavior

        $busStops = $this->data['busStops'] ?? [];
        $predefinedTime = $this->data['predefined_time'] ?? null;
        $predefinedDirection = $this->data['predefined_direction'] ?? null;

        // Clear existing RouteStop records if the route is updated
        RouteStop::where('route_id', $this->record->id)->delete();

        // Create RouteStop records for the selected bus stops
        foreach ($busStops as $index => $busStopId) {
            RouteStop::create([
                'bus_stop_id' => $busStopId,
                'route_id' => $this->record->id,
                'predefined_time' => $predefinedTime, // Set predefined_time
                'predefined_direction' => $predefinedDirection, // Set predefined_direction
                'route_order' => $index + 1, // Set route_order based on the order of selection
                'recent_bus_stop_match' => 0, // Assuming a default value
            ]);
        }
    }
}
