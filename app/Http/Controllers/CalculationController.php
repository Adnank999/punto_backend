<?php

namespace App\Http\Controllers;

use App\Models\BusStop;
use App\Models\RouteStop;
use App\Services\BusService;
use App\Services\BusService2;
use App\Services\CalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculationController extends Controller
{
    protected $calculationService;
    protected $busService;


    // Inject the CalculationService into the controller
    public function __construct(CalculationService $calculationService, BusService $busService, BusService2 $busService2)
    {
        $this->calculationService = $calculationService;
        // $this->busService = $busService;
        $this->busService = $busService2;
    }

    // API endpoint to calculate distance
    public function calculateDistance(Request $request)
    {
        // Validate the input coordinates
        $request->validate([
            'lat1' => 'required|numeric',
            'lon1' => 'required|numeric',
            'lat2' => 'required|numeric',
            'lon2' => 'required|numeric',
        ]);

        $lat1 = $request->input('lat1');
        $lon1 = $request->input('lon1');
        $lat2 = $request->input('lat2');
        $lon2 = $request->input('lon2');

        // Call the service method to calculate distance
        $distance = $this->calculationService->calculateDistance($lat1, $lon1, $lat2, $lon2);

        return response()->json([
            'distance' => $distance,
            'unit' => 'meters'
        ]);
    }

    // API endpoint to calculate travel time
    // public function calculateTravelTime(Request $request)
    // {

    //     // Validate the input coordinates and speed
    //     $request->validate([
    //         'lat1' => 'required|numeric',
    //         'lon1' => 'required|numeric',
    //         'lat2' => 'required|numeric',
    //         'lon2' => 'required|numeric',
    //         'average_speed' => 'required|numeric|min:1', // speed in km/h
    //     ]);

    //     $lat1 = $request->input('lat1');
    //     $lon1 = $request->input('lon1');
    //     $lat2 = $request->input('lat2');
    //     $lon2 = $request->input('lon2');
    //     $averageSpeed = $request->input('average_speed');

    //     // Call the service method to calculate travel time
    //     $travelTime = $this->calculationService->calculateTravelTime($lat1, $lon1, $lat2, $lon2, $averageSpeed);

    //     return response()->json([
    //         'travel_time' => $travelTime,
    //         'unit' => 'seconds'
    //     ]);
    // }

    // API endpoint to check if bus has departed
    public function checkIfBusDeparted(Request $request)
    {
        $validated = $request->validate([
            'busLat' => 'required|numeric',
            'busLon' => 'required|numeric',
            'stationLat' => 'required|numeric',
            'stationLon' => 'required|numeric',
            'geofenceRadius' => 'nullable|numeric',
        ]);

        $geofenceRadius = $validated['geofenceRadius'] ?? 500; // Default geofence radius is 500 meters

        $hasDeparted = $this->calculationService->checkIfBusDeparted(
            $validated['busLat'],
            $validated['busLon'],
            $validated['stationLat'],
            $validated['stationLon'],
            $geofenceRadius
        );

        return response()->json(['hasDeparted' => $hasDeparted]);
    }


    public function getNearestBusStop(Request $request)
    {
        // Validate the input data (ensure lat and lon are provided)
        $request->validate([
            'bus_lat' => 'required|numeric',
            'bus_lon' => 'required|numeric',
        ]);

        // Get the bus's current latitude and longitude from the request
        $busLat = $request->input('bus_lat');
        $busLon = $request->input('bus_lon');

        // Call the getStation method from the BusService
        $nearestBusStopId = $this->busService->getStation($busLat, $busLon);

        // Return the result as a JSON response
        if ($nearestBusStopId) {
            return response()->json([
                'success' => true,
                'nearest_bus_stop_id' => $nearestBusStopId,
                'message' => 'Nearest bus stop found.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No bus stop found within the geofence.',
            ], 404);
        }
    }

    public function determineDirection(Request $request)
    {
        $response = $this->busService->handleRequest($request);

        return response()->json([
            'response' => $response,

        ]);
    }

    public function calculateTravelTime(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'bus_id' => 'required|integer',
            'bus_lat' => 'required|numeric',
            'bus_lon' => 'required|numeric',
            'route_id' => 'required|integer',
        ]);

        // Call the method from BusService
        $busId = $request->input('bus_id');
        $busLat = $request->input('bus_lat');
        $busLon = $request->input('bus_lon');
        $routeId = $request->input('route_id');

        $result = $this->busService->calculateActualTravelTime($busId, $busLat, $busLon, $routeId);

        // Return the result as a JSON response
        return response()->json($result);
    }


    public function getBusDetails(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'bus_stop_id' => 'required|integer',
            'route_id' => 'required|integer',
            'bus_lat' => 'required|numeric',
            'bus_long' => 'required|numeric',
            'direction' => 'required|numeric',
        ]);

        try {
            // Get current time using Carbon
            $currentTime = Carbon::now()->format('Y-m-d H:i:s');

            // Call the service to calculate the bus details
            $busDetails = $this->busService->calculateBusDetails(
                $request->bus_stop_id,
                $request->route_id,
                $request->bus_lat,
                $request->bus_long,
                $request->direction,
                $currentTime
            );

            return response()->json([
                'success' => true,
                'data' => $busDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bus details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // public function getBusStopDetails(Request $request, $busStopId)
    // {
    //     // Fetch the bus stop with related routes and buses
    //     $busStop = BusStop::with('routes.buses')->find($busStopId);

    //     // Check if the bus stop exists
    //     if (!$busStop) {
    //         return response()->json(['error' => 'Bus stop not found'], 404);
    //     }

    //     // Earth radius (in meters)
    //     $earthRadius = 6371000; // in meters

    //     // Helper function to calculate distance between two coordinates using the Haversine formula
    //     function calculateDistance($lat1, $lon1, $lat2, $lon2, $earthRadius)
    //     {
    //         $lat1 = deg2rad($lat1);
    //         $lon1 = deg2rad($lon1);
    //         $lat2 = deg2rad($lat2);
    //         $lon2 = deg2rad($lon2);

    //         $dlat = $lat2 - $lat1;
    //         $dlon = $lon2 - $lon1;

    //         $a = sin($dlat / 2) * sin($dlat / 2) +
    //             cos($lat1) * cos($lat2) *
    //             sin($dlon / 2) * sin($dlon / 2);

    //         $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    //         return $earthRadius * $c; // Distance in meters
    //     }

    //     // Get bus data from POST request
    //     $busData = $request->input('buses');  // Assuming buses data is an array of bus objects

    //     // Initialize an array to store bus IDs, their distances, and next bus stop IDs for each route
    //     $routesWithBuses = [];

    //     // Define the geofence radius (in meters)
    //     $geofenceRadius = 200;

    //     // Iterate over each route to gather the bus IDs and their next bus stops
    //     foreach ($busStop->routes as $route) {
    //         Log::debug('Route found:', ['route_id' => $route->id, 'route_name' => $route->name]);

    //         // Get the route stops for the current route, ordered by route_order, including bus stop details
    //         $routeStops = DB::table('route_stops')
    //             ->join('bus_stops', 'route_stops.bus_stop_id', '=', 'bus_stops.id') // Join bus_stops to get latitude and longitude
    //             ->where('route_stops.route_id', $route->id)
    //             ->orderBy('route_stops.route_order')
    //             ->select('route_stops.*', 'bus_stops.latitude', 'bus_stops.longitude') // Select latitude and longitude from bus_stops
    //             ->get();
    //         Log::debug('Route Stops:', ['route_stops' => $routeStops]);

    //         // Iterate over each bus to get its movement through the route stops
    //         $busDetails = [];
    //         foreach ($busData as $bus) {
    //             Log::debug('Processing bus:', ['bus_id' => $bus['bus_id']]);

    //             $busId = $bus['bus_id'];
    //             $busLatitude = $bus['lat'];
    //             $busLongitude = $bus['lon'];

    //             // Check if latitude and longitude are valid
    //             if (!is_numeric($busLatitude) || !is_numeric($busLongitude)) {
    //                 Log::error('Invalid latitude or longitude for bus', ['bus_id' => $busId, 'lat' => $busLatitude, 'lon' => $busLongitude]);
    //                 continue;
    //             }

    //             // For each bus, iterate through all the stops
    //             $busMovement = [];
    //             for ($i = 0; $i < count($routeStops); $i++) {
    //                 // Get the current stop and next stop based on route_order
    //                 $currentStop = $routeStops[$i];
    //                 $nextStop = isset($routeStops[$i + 1]) ? $routeStops[$i + 1] : null;

    //                 $nextBusStopId = $nextStop ? $nextStop->bus_stop_id : null;

    //                 // Check if latitude and longitude are available for current stop
    //                 if (!isset($currentStop->latitude) || !isset($currentStop->longitude)) {
    //                     Log::error('Missing latitude/longitude for current stop', ['bus_stop_id' => $currentStop->bus_stop_id]);
    //                     continue;
    //                 }

    //                 // Calculate the distance between the bus (using its latitude/longitude) and the current stop
    //                 $distanceToCurrentStop = calculateDistance($busLatitude, $busLongitude, $currentStop->latitude, $currentStop->longitude, $earthRadius);
    //                 Log::debug('Distance to Current Stop:', ['bus_id' => $busId, 'distance' => $distanceToCurrentStop]);

    //                 // Calculate the distance to the geofence radius (200 meters)
    //                 $distanceToGeofenceRadius = ($distanceToCurrentStop <= $geofenceRadius) ? $distanceToCurrentStop : null;
    //                 Log::debug('Distance to Geofence Radius:', ['bus_id' => $busId, 'distance_to_geofence_radius' => $distanceToGeofenceRadius]);

    //                 // Check if the bus is within the geofence radius
    //                 $inGeofenceRadius = $distanceToCurrentStop <= $geofenceRadius;

    //                 // Calculate predefined_time between current stop and next stop
    //                 $predefinedTime = null;
    //                 if ($nextStop) {
    //                     $currentPredefinedTime = $currentStop->predefined_time;
    //                     $nextPredefinedTime = $nextStop->predefined_time;

    //                     // Calculate predefined_time difference (next - current)
    //                     $predefinedTime = $nextPredefinedTime - $currentPredefinedTime;
    //                 }

    //                 // Store the bus movement for each bus stop
    //                 $busMovement[] = [
    //                     'bus_stop_id' => $currentStop->bus_stop_id,
    //                     'next_bus_stop_id' => $nextBusStopId,
    //                     'distance_to_current_stop' => $distanceToCurrentStop, // Actual distance to current stop in meters
    //                     'distance_to_geofence_radius' => $distanceToGeofenceRadius, // Distance to geofence radius (if within 200m)
    //                     'in_geofence_radius' => $inGeofenceRadius, // True or False based on the geofence check
    //                     'predefined_time' => $predefinedTime, // Predefined time difference
    //                 ];
    //             }

    //             $busDetails[] = [
    //                 'bus_id' => $busId,
    //                 'bus_movement' => $busMovement,
    //             ];
    //         }

    //         // Add the bus details to the routes array
    //         $routesWithBuses[] = [
    //             'route_id' => $route->id,
    //             'route_name' => $route->name,
    //             'bus_details' => $busDetails,
    //         ];
    //     }

    //     // Return bus stop details including route count and bus IDs with next bus stop IDs, distances, geofence info, and predefined_time
    //     Log::debug('Final Response:', [
    //         'bus_stop' => $busStop->name,
    //         'latitude' => $busStop->latitude,
    //         'longitude' => $busStop->longitude,
    //         'total_routes' => $busStop->routes->count(),
    //         'routes' => $routesWithBuses,
    //     ]);

    //     return response()->json([
    //         'bus_stop' => $busStop->name,
    //         'latitude' => $busStop->latitude,
    //         'longitude' => $busStop->longitude,
    //         'total_routes' => $busStop->routes->count(),
    //         'routes' => $routesWithBuses, // Include bus movement details with geofence info
    //     ]);
    // }


    // public function getBusStopDetails(Request $request, $busStopId)
    // {
    //     $busStop = BusStop::with('routes.buses')->find($busStopId);

    //     if (!$busStop) {
    //         return response()->json(['error' => 'Bus stop not found'], 404);
    //     }

    //     $earthRadius = 6371000; // in meters

    //     function calculateDistance($lat1, $lon1, $lat2, $lon2, $earthRadius)
    //     {
    //         $lat1 = deg2rad($lat1);
    //         $lon1 = deg2rad($lon1);
    //         $lat2 = deg2rad($lat2);
    //         $lon2 = deg2rad($lon2);

    //         $dlat = $lat2 - $lat1;
    //         $dlon = $lon2 - $lon1;

    //         $a = sin($dlat / 2) * sin($dlat / 2) +
    //             cos($lat1) * cos($lat2) *
    //             sin($dlon / 2) * sin($dlon / 2);

    //         $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    //         return $earthRadius * $c; // Distance in meters
    //     }

    //     $busData = $request->input('buses');

    //     $routesWithBuses = [];
    //     $geofenceRadius = 200;

    //     // Store the last matched bus stop for each bus
    //     $busLastStopId = [];

    //     foreach ($busStop->routes as $route) {
    //         Log::debug('Route found:', ['route_id' => $route->id, 'route_name' => $route->name]);

    //         $routeStops = DB::table('route_stops')
    //             ->join('bus_stops', 'route_stops.bus_stop_id', '=', 'bus_stops.id')
    //             ->where('route_stops.route_id', $route->id)
    //             ->orderBy('route_stops.route_order')
    //             ->select('route_stops.*', 'bus_stops.latitude', 'bus_stops.longitude')
    //             ->get();

    //         Log::debug('Route Stops:', ['route_stops' => $routeStops]);

    //         $busDetails = [];

    //         foreach ($busData as $bus) {
    //             Log::debug('Processing bus:', ['bus_id' => $bus['bus_id']]);

    //             $busId = $bus['bus_id'];
    //             $busLatitude = $bus['lat'];
    //             $busLongitude = $bus['lon'];

    //             if (!is_numeric($busLatitude) || !is_numeric($busLongitude)) {
    //                 Log::error('Invalid latitude or longitude for bus', ['bus_id' => $busId, 'lat' => $busLatitude, 'lon' => $busLongitude]);
    //                 continue;
    //             }

    //             // Initialize bus last stop id if it's not already set
    //             if (!isset($busLastStopId[$busId])) {
    //                 $busLastStopId[$busId] = null; // Initializing to null for the first time this bus is processed
    //             }

    //             $busMovement = [];

    //             for ($i = 0; $i < count($routeStops); $i++) {
    //                 $currentStop = $routeStops[$i];
    //                 $nextStop = isset($routeStops[$i + 1]) ? $routeStops[$i + 1] : null;

    //                 $nextBusStopId = $nextStop ? $nextStop->bus_stop_id : null;

    //                 if (!isset($currentStop->latitude) || !isset($currentStop->longitude)) {
    //                     Log::error('Missing latitude/longitude for current stop', ['bus_stop_id' => $currentStop->bus_stop_id]);
    //                     continue;
    //                 }

    //                 $distanceToCurrentStop = calculateDistance($busLatitude, $busLongitude, $currentStop->latitude, $currentStop->longitude, $earthRadius);
    //                 Log::debug('Distance to Current Stop:', ['bus_id' => $busId, 'distance' => $distanceToCurrentStop]);

    //                 $inGeofenceRadius = $distanceToCurrentStop <= $geofenceRadius;

    //                 // When the bus is in the geofence radius for the current stop
    //                 if ($inGeofenceRadius) {
    //                     // Reset all other bus stops in the route to recent_bus_stop_match = 0
    //                     DB::table('route_stops')
    //                         ->where('route_id', $currentStop->route_id)
    //                         ->update(['recent_bus_stop_match' => 0]);

    //                     Log::debug("Reset all bus stops for route_id to recent_bus_stop_match = 0", [
    //                         'route_id' => $currentStop->route_id
    //                     ]);

    //                     // Now set the current bus stop's recent_bus_stop_match to 1
    //                     DB::table('route_stops')
    //                         ->where('bus_stop_id', $currentStop->bus_stop_id)
    //                         ->update(['recent_bus_stop_match' => 1]);

    //                     Log::debug("Set recent_bus_stop_match for current stop", [
    //                         'bus_id' => $busId,
    //                         'current_stop_id' => $currentStop->bus_stop_id
    //                     ]);

    //                     // Update the last matched bus stop for this bus
    //                     $busLastStopId[$busId] = $currentStop->bus_stop_id;
    //                 }

    //                 // Get the BusStop model for current stop
    //                 $lastBusStopRecord = RouteStop::find($currentStop->bus_stop_id);


    //                 // Check the recent_bus_stop_match attribute of the BusStop model
    //                 $recentBusStopMatch = ($lastBusStopRecord && $lastBusStopRecord->recent_bus_stop_match == 1) ? true : false;

    //                 $predefinedTime = null;
    //                 if ($nextStop) {
    //                     $currentPredefinedTime = $currentStop->predefined_time;
    //                     $nextPredefinedTime = $nextStop->predefined_time;
    //                     $predefinedTime = $nextPredefinedTime - $currentPredefinedTime;
    //                 }

    //                 $busMovement[] = [
    //                     'bus_stop_id' => $currentStop->bus_stop_id,
    //                     'next_bus_stop_id' => $nextBusStopId,
    //                     'distance_to_current_stop' => $distanceToCurrentStop,
    //                     'in_geofence_radius' => $inGeofenceRadius,
    //                     'recent_bus_stop_match' => $recentBusStopMatch,  // This will be true or false based on geofence
    //                     'predefined_time' => $predefinedTime,
    //                 ];
    //             }

    //             $busDetails[] = [
    //                 'bus_id' => $busId,
    //                 'bus_movement' => $busMovement,
    //             ];
    //         }







    //         $routesWithBuses[] = [
    //             'route_id' => $route->id,
    //             'route_name' => $route->name,
    //             'bus_details' => $busDetails,
    //         ];
    //     }

    //     Log::debug('Final Response:', [
    //         'bus_stop' => $busStop->name,
    //         'latitude' => $busStop->latitude,
    //         'longitude' => $busStop->longitude,
    //         'total_routes' => $busStop->routes->count(),
    //         'routes' => $routesWithBuses,
    //     ]);

    //     return response()->json([
    //         'bus_stop' => $busStop->name,
    //         'latitude' => $busStop->latitude,
    //         'longitude' => $busStop->longitude,
    //         'total_routes' => $busStop->routes->count(),
    //         'routes' => $routesWithBuses,
    //     ]);
    // }




    



     public function getBusStopDetails(Request $request, $busStopId)
    {
        $busStop = BusStop::with('routes.buses')->find($busStopId);

        if (!$busStop) {
            return response()->json(['error' => 'Bus stop not found'], 404);
        }

        $earthRadius = 6371000; // in meters

        function calculateDistance($lat1, $lon1, $lat2, $lon2, $earthRadius)
        {
            $lat1 = deg2rad($lat1);
            $lon1 = deg2rad($lon1);
            $lat2 = deg2rad($lat2);
            $lon2 = deg2rad($lon2);

            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;

            $a = sin($dlat / 2) * sin($dlat / 2) +
                cos($lat1) * cos($lat2) *
                sin($dlon / 2) * sin($dlon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earthRadius * $c; // Distance in meters
        }

        $busData = $request->input('buses');

        $routesWithBuses = [];
        $geofenceRadius = 200;

        // Store the last matched bus stop for each bus
        $busLastStopId = [];

        foreach ($busStop->routes as $route) {
            Log::debug('Route found:', ['route_id' => $route->id, 'route_name' => $route->name]);

            $routeStops = DB::table('route_stops')
                ->join('bus_stops', 'route_stops.bus_stop_id', '=', 'bus_stops.id')
                ->where('route_stops.route_id', $route->id)
                ->orderBy('route_stops.route_order')
                ->select('route_stops.*', 'bus_stops.latitude', 'bus_stops.longitude')
                ->get();

            Log::debug('Route Stops:', ['route_stops' => $routeStops]);

            $busDetails = [];

            foreach ($busData as $bus) {
                Log::debug('Processing bus:', ['bus_id' => $bus['bus_id']]);

                $busId = $bus['bus_id'];
                $busLatitude = $bus['lat'];
                $busLongitude = $bus['lon'];

                if (!is_numeric($busLatitude) || !is_numeric($busLongitude)) {
                    Log::error('Invalid latitude or longitude for bus', ['bus_id' => $busId, 'lat' => $busLatitude, 'lon' => $busLongitude]);
                    continue;
                }

                // Initialize bus last stop id if it's not already set
                if (!isset($busLastStopId[$busId])) {
                    $busLastStopId[$busId] = null; // Initializing to null for the first time this bus is processed
                }

                $actualTimeInSeconds = 0;
                $actualTimeInMinutes = 0;

                $busMovement = [];

                for ($i = 0; $i < count($routeStops); $i++) {
                    $currentStop = $routeStops[$i];
                    $nextStop = isset($routeStops[$i + 1]) ? $routeStops[$i + 1] : null;

                    $nextBusStopId = $nextStop ? $nextStop->bus_stop_id : null;

                    if (!isset($currentStop->latitude) || !isset($currentStop->longitude)) {
                        Log::error('Missing latitude/longitude for current stop', ['bus_stop_id' => $currentStop->bus_stop_id]);
                        continue;
                    }

                    $distanceToCurrentStop = calculateDistance($busLatitude, $busLongitude, $currentStop->latitude, $currentStop->longitude, $earthRadius);
                    Log::debug('Distance to Current Stop:', ['bus_id' => $busId, 'distance' => $distanceToCurrentStop]);

                    $inGeofenceRadius = $distanceToCurrentStop <= $geofenceRadius;

                    // When the bus is in the geofence radius for the current stop
                    if ($inGeofenceRadius) {
                        // Reset all other bus stops in the route to recent_bus_stop_match = 0
                        DB::table('route_stops')
                            ->where('route_id', $currentStop->route_id)
                            ->update(['recent_bus_stop_match' => 0]);

                        Log::debug("Reset all bus stops for route_id to recent_bus_stop_match = 0", [
                            'route_id' => $currentStop->route_id
                        ]);

                        // Now set the current bus stop's recent_bus_stop_match to 1
                        DB::table('route_stops')
                            ->where('bus_stop_id', $currentStop->bus_stop_id)
                            ->update(['recent_bus_stop_match' => 1]);

                        Log::debug("Set recent_bus_stop_match for current stop", [
                            'bus_id' => $busId,
                            'current_stop_id' => $currentStop->bus_stop_id
                        ]);

                        // Update the last matched bus stop for this bus
                        $busLastStopId[$busId] = $currentStop->bus_stop_id;
                        
                    }

                    // Get the BusStop model for current stop
                    $lastBusStopRecord = RouteStop::find($currentStop->bus_stop_id);


                    // Check the recent_bus_stop_match attribute of the BusStop model
                    $recentBusStopMatch = ($lastBusStopRecord && $lastBusStopRecord->recent_bus_stop_match == 1) ? true : false;

                    $predefinedTime = null;
                    if ($nextStop) {
                        $currentPredefinedTime = $currentStop->predefined_time;
                        $nextPredefinedTime = $nextStop->predefined_time;
                        $predefinedTime = $nextPredefinedTime - $currentPredefinedTime;
                    }

                    if ($lastBusStopRecord && $lastBusStopRecord->recent_bus_stop_match == 1) {
                        // Get the time the bus stop was matched
                        $createdAt = Carbon::parse($lastBusStopRecord->created_at);
                        $formattedTime = $createdAt->format('d-m-Y h:i A');
    
                        // Calculate current time and difference from predefined time
                        $currentTime = Carbon::now();
                        $timeDiffInSeconds = $currentTime->diffInSeconds($createdAt);
    
                        // Calculate actual time based on whether the bus is in the geofence
                        if ($inGeofenceRadius) {
                           
                            $actualTimeInSeconds =  $predefinedTime;
                            
                            // dd($actualTimeInSeconds);
                        } else {
                            $actualTimeInSeconds = $currentStop->predefined_time - $timeDiffInSeconds;
                        }
    
                        // Calculate the actual time in minutes
                        $actualTimeInMinutes = ($actualTimeInSeconds / 60);
    
                        // Log the results
                        Log::debug("Calculated actual time", [
                            'bus_id' => $busId,
                            'bus_stop_id' => $currentStop->bus_stop_id,
                            'actual_time_in_seconds' => $actualTimeInSeconds,
                            'actual_time_in_minutes' => $actualTimeInMinutes
                        ]);
                    }

                    

                    $busMovement[] = [
                        'bus_stop_id' => $currentStop->bus_stop_id,
                        'next_bus_stop_id' => $nextBusStopId,
                        'distance_to_current_stop' => $distanceToCurrentStop,
                        'in_geofence_radius' => $inGeofenceRadius,
                        'recent_bus_stop_match' => $recentBusStopMatch,  // This will be true or false based on geofence
                        'predefined_time_to_next_stop' => $predefinedTime,
                        'actual_time_seconds' => $actualTimeInSeconds,  // Actual time in seconds
                        'actual_time_minutes' => $actualTimeInMinutes,  // Actual time in minutes
                    ];
                }

                $busDetails[] = [
                    'bus_id' => $busId,
                    'bus_movement' => $busMovement,
                ];
            }







            $routesWithBuses[] = [
                'route_id' => $route->id,
                'route_name' => $route->name,
                'bus_details' => $busDetails,
            ];
        }

        Log::debug('Final Response:', [
            'bus_stop' => $busStop->name,
            'latitude' => $busStop->latitude,
            'longitude' => $busStop->longitude,
            'total_routes' => $busStop->routes->count(),
            'routes' => $routesWithBuses,
        ]);

        return response()->json([
            'bus_stop' => $busStop->name,
            'latitude' => $busStop->latitude,
            'longitude' => $busStop->longitude,
            'total_routes' => $busStop->routes->count(),
            'routes' => $routesWithBuses,
        ]);
    }
    
}
