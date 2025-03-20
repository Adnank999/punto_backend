<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CalculationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // function calculateAverageSpeed($startLat, $startLon, $endLat, $endLon, $startTime, $endTime, $currentSpeed) {
    //     // Calculate distance between Point A and Point B
    //     $distance = calculateDistance($startLat, $startLon, $endLat, $endLon); // Distance in km
        
    //     // Calculate the total time it took to travel (in hours)
    //     $timeElapsed = ($endTime - $startTime) / 3600; // Convert time from seconds to hours
        
    //     // Calculate average speed
    //     $averageSpeed = $distance / $timeElapsed; // Average Speed in km/h
        
    //     // Optionally, add the current speed to the calculation if it's an instantaneous reading
    //     // This can give a rough estimate if you're interested in how fast the car is moving right now
    //     $averageSpeed = ($averageSpeed + $currentSpeed) / 2;
        
    //     return $averageSpeed; // Return average speed in km/h
    // }


     // Function to calculate the distance between two points using the Haversine formula
     public function calculateDistance($lat1, $lon1, $lat2, $lon2)
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
 
     // Function to calculate the travel time based on distance and average speed
    //  public function calculateTravelTime($lat1, $lon1, $lat2, $lon2, $averageSpeed = 15)
    //  {
    //      // Calculate the distance between two stops in meters
    //      $distance = $this->calculateDistance($lat1, $lon1, $lat2, $lon2);
 
    //      // Convert average speed to meters per second
    //      $speedInMetersPerSecond = $averageSpeed * 1000 / 3600;
 
    //      // Calculate the travel time (in seconds)
    //      $travelTimeInSeconds = $distance / $speedInMetersPerSecond;
 
    //      return $travelTimeInSeconds;
    //  }


     function checkIfBusDeparted($busLat, $busLon, $stationLat, $stationLon, $geofenceRadius = 200) {
        $distance = $this->calculateDistance($stationLat, $stationLon, $busLat, $busLon); 
        
        if ($distance > $geofenceRadius) {
            return true; 
        } else {
            return false; 
        }
    }

    public function isWithinGeofence($busLat, $busLon, $stationLat, $stationLon, $geofenceRadius = 200)
    {
        $distance = $this->calculateDistance($busLat, $busLon, $stationLat, $stationLon);
        return $distance <= $geofenceRadius; // returns true if within the 200 meters
    }


   






     
}
