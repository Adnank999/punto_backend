<?php

namespace App\Services;

use App\Models\BusStop;
use App\Models\BusStopSchedule;
use Carbon\Carbon;

class BusService2
{
    // Calculate if bus is inside geofence and its expected time
    public function calculateBusDetails($busStopId, $routeId, $busLat, $busLong, $direction, $currentTime)
    {
        // Get the bus stop schedules for the given route
        $busStopSchedules = BusStopSchedule::where('route_id', $routeId)
            ->where('bus_stop_id', $busStopId)
            ->orderBy('route_stop_order')
            ->get();

        // Initialize response data
        $response = [
            'route_direction' => 'outbound',
            'next_stop' => null,
            'next_stop_arriving_time' => 0,
            'is_bus_in_station_radius' => false
        ];

        // Determine route direction (outbound or inbound)
        $response['route_direction'] = $this->determineRouteDirection($routeId, $direction);

        // Iterate over each bus stop schedule
        foreach ($busStopSchedules as $schedule) {
            // Get the next stop schedule based on the route order
            $nextStop = $this->getNextStop($busStopSchedules, $schedule->route_stop_order, $response['route_direction']);

            if ($nextStop) {
                $response['next_stop'] = $nextStop->busStop->id; // next stop ID
                $response['next_stop_arriving_time'] = $this->calculateArrivalTime($schedule, $currentTime); // Time to next stop
            }
        }

        return $response;
    }

    // Calculate arrival time to next stop
    private function calculateArrivalTime($currentSchedule, $currentTime)
    {
        $timeInStop = Carbon::parse($currentSchedule->expected_arrival_time);
        $timeLeftInSeconds = Carbon::parse($currentTime)->diffInSeconds($timeInStop);
        $remainingTimeInSeconds = $currentSchedule->expected_arrival_time - $timeLeftInSeconds;
        
        return round($remainingTimeInSeconds / 60, 2); // Convert to minutes
    }

    // Get the next stop based on the route direction and order
    private function getNextStop($busStopSchedules, $currentRouteStopOrder, $routeDirection)
    {
        if ($routeDirection === 'outbound') {
            // If outbound, get the next stop in the sequence
            return $busStopSchedules->firstWhere('route_stop_order', $currentRouteStopOrder + 1);
        } else {
            // If inbound, get the previous stop in the sequence
            return $busStopSchedules->firstWhere('route_stop_order', $currentRouteStopOrder - 1);
        }
    }

    // Determine the route direction (inbound or outbound)
    public function determineRouteDirection($routeId, $currentDirection)
    {
        // Get all bus stop schedules for the given route
        $busStopSchedules = BusStopSchedule::where('route_id', $routeId)
            ->orderBy('route_stop_order')
            ->get();

        // Compare the first and last stop of the route to determine the direction
        $routeDirection = 'outbound'; // default assumption

        // Check if the bus is going inbound (if direction is greater than 180 degrees)
        if ($currentDirection >= 180) {
            $routeDirection = 'inbound';
        }

        return $routeDirection;
    }
}