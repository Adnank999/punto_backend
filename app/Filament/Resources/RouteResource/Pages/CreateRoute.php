<?php

namespace App\Filament\Resources\RouteResource\Pages;

use App\Filament\Resources\RouteResource;
use App\Models\Route;
use App\Models\RouteStop;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;




    protected function afterCreate(): void
    {
        // Get the created Route model
        $route = $this->record;

        // Access the route_id
        $routeId = $route->id;

        // Access the bus stops and route orders from the form data
        $busStops = $this->data['busStops'] ?? [];
        $routeOrders = $this->data['route_orders'] ?? [];

        // Log to ensure busStops and routeOrders are correctly populated
        \Log::debug('Bus Stops:', $busStops);
        \Log::debug('Route Orders:', $routeOrders);

        // Loop through bus stops and update RouteStop records
        foreach ($busStops as $index => $busStopId) {
            // Ensure route_order is set, default to 0 if missing
            $routeOrder = isset($routeOrders[$index]) ? $routeOrders[$index] : 0;

            // Log the update operation
            \Log::debug('Updating RouteStop:', [
                'route_id' => $routeId,
                'bus_stop_id' => $busStopId,
                'route_order' => $routeOrder,
            ]);

            // Find the existing RouteStop record based on route_id and bus_stop_id
            $routeStop = RouteStop::where('route_id', $routeId)
                ->where('bus_stop_id', $busStopId)
                ->first();

            // If the RouteStop exists, update the route_order
            if ($routeStop) {
                $routeStop->update([
                    'route_order' => $routeOrder,
                ]);
            } else {
                \Log::warning("RouteStop not found for route_id: $routeId, bus_stop_id: $busStopId");
            }
        }
    }
}
