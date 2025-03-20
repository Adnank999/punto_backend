<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\BusStopSchedule;
use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BusStopScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $buses = Bus::all();
        $busStops = BusStop::all();
        $routes = Route::all();  // Get all routes
        
        // Predefined travel times between stops in seconds (this should be dynamic or loaded from a config)
        $travelTimes = [
            1 => 600, // 1 to 2: 600 seconds (10 minutes)
            2 => 900, // 2 to 3: 900 seconds (15 minutes)
            3 => 1200, // 3 to 4: 1200 seconds (20 minutes)
            // Add more predefined travel times for additional routes as needed
        ];
        
        foreach ($buses as $bus) {
            // Get the route for this bus. For simplicity, assuming each bus has a route (you can customize logic)
            $route = $routes->random();  // Randomly assign a route or modify logic as needed
            
            $sortedBusStops = $busStops->sortBy('id'); 
            $routeStopOrder = 1;
            $totalTime = 0; // To calculate the cumulative expected arrival time

            foreach ($sortedBusStops as $busStop) {
                // Calculate expected arrival time for the bus stop
                $expectedArrivalTime = $totalTime + (isset($travelTimes[$routeStopOrder - 1]) ? $travelTimes[$routeStopOrder - 1] : 0);
                
                BusStopSchedule::create([
                    'bus_id' => $bus->id,
                    'bus_stop_id' => $busStop->id,
                    'route_id' => $route->id,  // Add route_id here
                    'expected_arrival_time' => $expectedArrivalTime, // Set arrival time in seconds
                    'route_stop_order' => $routeStopOrder,
                ]);

                // Update total time by adding the current segment's travel time
                $totalTime = $expectedArrivalTime; 

                $routeStopOrder++; 
            }
        }
    }
}
