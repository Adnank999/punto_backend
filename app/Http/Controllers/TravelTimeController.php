function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    // Radius of the Earth in meters
    $earthRadius = 6371000;

    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Differences between the coordinates
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;

    // Haversine formula
    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($deltaLon / 2) * sin($deltaLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in meters
    $distance = $earthRadius * $c;

    return $distance; // Distance in meters
}



function calculateTravelTime($lat1, $lon1, $lat2, $lon2, $averageSpeed = 15)
{
    // Calculate the distance between two stops in meters
    $distance = calculateDistance($lat1, $lon1, $lat2, $lon2);

    // Convert average speed to meters per second
    $speedInMetersPerSecond = $averageSpeed * 1000 / 3600;

    // Calculate the travel time (in seconds)
    $travelTimeInSeconds = $distance / $speedInMetersPerSecond;

    return $travelTimeInSeconds;
}




function calculateExpectedArrivalTime($currentLatitude, $currentLongitude, $nextStopLatitude, $nextStopLongitude)
{
    // Get the current time (you can replace this with the bus's last recorded time)
    $currentTime = Carbon::now();

    // Calculate the travel time from current location to next stop (in seconds)
    $travelTimeInSeconds = calculateTravelTime($currentLatitude, $currentLongitude, $nextStopLatitude, $nextStopLongitude);

    // Add the travel time to the current time
    $expectedArrivalTime = $currentTime->addSeconds($travelTimeInSeconds);

    return $expectedArrivalTime->format('H:i:s');
}


$currentLatitude = 40.7128;
$currentLongitude = -74.0060;
$nextStopLatitude = 40.7306;
$nextStopLongitude = -73.9352;

$expectedArrivalTime = calculateExpectedArrivalTime($currentLatitude, $currentLongitude, $nextStopLatitude, $nextStopLongitude);

echo "Expected Arrival Time: $expectedArrivalTime";



function calculateRouteArrivalTimes($bus, $routeStops)
{
    $currentLatitude = $bus->current_latitude;
    $currentLongitude = $bus->current_longitude;
    $currentTime = Carbon::now(); // Or use the last known time of the bus

    foreach ($routeStops as $stop) {
        $nextStopLatitude = $stop->latitude;
        $nextStopLongitude = $stop->longitude;

        // Calculate the travel time to the next stop
        $travelTimeInSeconds = calculateTravelTime($currentLatitude, $currentLongitude, $nextStopLatitude, $nextStopLongitude);

        // Add travel time to the current time to get the expected arrival time at the next stop
        $currentTime = $currentTime->addSeconds($travelTimeInSeconds);

        // Save or update the expected arrival time for the current stop
        $stop->expected_arrival_time = $currentTime->format('H:i:s');
        $stop->save();

        // Update current position to the next stop
        $currentLatitude = $nextStopLatitude;
        $currentLongitude = $nextStopLongitude;
    }

    return $routeStops;
}

