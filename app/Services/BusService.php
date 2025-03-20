<?php

namespace App\Services;

use App\Models\BusStop;
use App\Models\BusStopSchedule;
use Carbon\Carbon;

class BusService
{
    // Predefined travel times in seconds between bus stations

    // Geofence radius in meters
    private $geofenceRadius = 200;
    private $inboundDirectionThreshold = 180;

    // Bus station coordinates
    public function getStation($busLat, $busLon)
    {
        // Fetch all bus stops from the database
        $busStops = BusStop::all();  // Assuming BusStop model exists and has lat/lon columns

        foreach ($busStops as $busStop) {
            // Get the coordinates of the bus stop
            $busStopLat = $busStop->latitude;  // Assuming 'lat' column stores latitude
            $busStopLon = $busStop->longitude;  // Assuming 'lon' column stores longitude

            // Calculate the distance between the bus and the bus stop using Haversine formula
            $distance = $this->haversine($busLat, $busLon, $busStopLat, $busStopLon);


            if ($distance <= $this->geofenceRadius) {
                return $busStop->id;
            }
        }

        // If no bus stop is within the geofence, return null
        return null;
    }


    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000;  // Radius of Earth in meters
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $delta_phi = deg2rad($lat2 - $lat1);
        $delta_lambda = deg2rad($lon2 - $lon1);

        $a = sin($delta_phi / 2) * sin($delta_phi / 2) +
            cos($phi1) * cos($phi2) *
            sin($delta_lambda / 2) * sin($delta_lambda / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $R * $c;  // Distance in meters
        return $distance;
    }



    public function determineInitialDirection($routeStopOrder)
    {
        $routeStopOrderArray = explode(',', $routeStopOrder);  // Convert to array

        // Check if the route_stop_order is in increasing or decreasing order
        $isOutbound = true;
        for ($i = 0; $i < count($routeStopOrderArray) - 1; $i++) {
            if ($routeStopOrderArray[$i] > $routeStopOrderArray[$i + 1]) {
                $isOutbound = false; // It's inbound if the order decreases
                break;
            }
        }

        return $isOutbound ? 'outbound' : 'inbound'; // Return outbound if the order is increasing
    }

    // Determine the current direction based on live API data (dir)
    public function determineLiveDirection($dir)
    {
        // Check the live direction (within the threshold for inbound and outbound)
        if ($dir >= 0 && $dir <= $this->inboundDirectionThreshold) {
            return 'inbound';
        } elseif ($dir > $this->inboundDirectionThreshold && $dir <= 360) {
            return 'outbound';
        }

        return 'unknown'; // Direction is outside expected range
    }

    // Main function to handle the request and determine the direction
    public function handleRequest($requestData)
    {
        $routeId = $requestData['route_id'];  // Route ID
        $routeStopOrder = $requestData['route_stop_order'];  // Route stop order (for initial direction)
        $dir = $requestData['dir'];  // Direction from live update API

        // Determine the initial direction based on route_stop_order
        $initialDirection = $this->determineInitialDirection($routeStopOrder);

        // If the route_stop_order suggests an outbound direction, force it to outbound
        // and prevent a conflicting inbound direction from the live `dir`.
        if ($initialDirection === 'outbound' && $dir <= $this->inboundDirectionThreshold) {
            // If the live direction (`dir`) is inconsistent with the expected direction, force outbound.
            return "Bus is currently outbound.";
        }

        // If the route_stop_order suggests an inbound direction, force it to inbound
        // and prevent a conflicting outbound direction from the live `dir`.
        if ($initialDirection === 'inbound' && $dir > $this->inboundDirectionThreshold) {
            // If the live direction (`dir`) is inconsistent with the expected direction, force inbound.
            return "Bus is currently inbound.";
        }

        // Otherwise, determine the direction based on the live data `dir`
        $liveDirection = $this->determineLiveDirection($dir);

        // Return the direction based on the live data or fallback to the initial direction
        return "Bus is currently " . ($liveDirection !== 'unknown' ? $liveDirection : $initialDirection) . ".";
    }


    public function calculateActualTravelTime($busId, $busLat, $busLon, $routeId)
    {
        // Fetch the bus stop schedules for the given route_id and bus_id, ordered by stop order
        $schedule = BusStopSchedule::where('route_id', $routeId)  // Using dynamic route_id
            ->where('bus_id', $busId)
            ->orderBy('route_stop_order')  // Ensure bus stops are ordered by stop order
            ->get();
    
        // Ensure there are at least two bus stops to calculate travel time
        if ($schedule->count() < 2) {
            return "Error: Not enough bus stops to calculate travel time.";
        }
    
        // Retrieve the first and second bus stops dynamically
        $firstStop = $schedule->first(); // First bus stop (A)
        $secondStop = $schedule->skip(1)->first(); // Second bus stop (B)
    
        // Fetch the expected arrival time between the two bus stops
        $expectedArrivalTime = $firstStop->expected_arrival_time;
    
        // If we don't have an expected arrival time, return an error message
        if ($expectedArrivalTime === null) {
            return "Error: Predefined travel time not found for this route.";
        }
    
        // Step 1: Check if the bus is within the geofence radius of the first bus stop (A)
        $distanceToFirstStop = $this->haversine(
            $busLat,
            $busLon,  // Bus coordinates
            $firstStop->latitude,
            $firstStop->longitude
        );  // Coordinates of bus stop A
    
        // Step 2: Check if the bus is within the geofence radius of the second bus stop (B)
        $distanceToSecondStop = $this->haversine(
            $busLat,
            $busLon,  // Bus coordinates
            $secondStop->latitude,
            $secondStop->longitude
        );  // Coordinates of bus stop B
    
        // Start tracking time when the bus reaches the first bus stop (A)
        if ($distanceToFirstStop <= $this->geofenceRadius) {
            $startTime = time();  // Time when bus reaches station A (in seconds)
        }
    
        // Track when the bus reaches the second bus stop (B) and calculate travel time
        if ($distanceToSecondStop <= $this->geofenceRadius) {
            $endTime = time();  // Time when bus reaches station B (in seconds)
    
            // Calculate actual travel time in seconds
            $actualTravelTime = $endTime - $startTime;  // Time in seconds
    
            // Compare actual time with expected arrival time
            $timeDifference = $actualTravelTime - $expectedArrivalTime;  // Positive if late, negative if early
    
            return [
                'actual_travel_time' => $actualTravelTime,
                'expected_travel_time' => $expectedArrivalTime,
                'time_difference' => $timeDifference,  // Positive if late, negative if early
            ];
        }
    
        // If the bus hasn't reached the second bus stop (B) yet, return a message
        return "Bus has not yet reached station B.";
    }
    
}
