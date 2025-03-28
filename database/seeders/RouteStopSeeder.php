<?php

namespace Database\Seeders;

use App\Models\RouteStop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// class RouteStopSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         $stops = [1, 2, 3, 4, 5];
//         $route_id = 1;
//         $time_intervals = [120, 150, 180, 200];

//         $directions = $this->generateDirections(count($stops));
        
//         // Outbound Route
//         foreach ($stops as $index => $stop) {
//             RouteStop::create([
//                 'bus_stop_id' => $stop,
//                 'route_id' => $route_id,
//                 'predefined_time' => $time_intervals[$index % count($time_intervals)],
//                 'predefined_direction' => $directions[$index],
//                 'route_order' => $index + 1,
//                 'recent_bus_stop_match' => 0,
//             ]);
//         }
        
        
//     }

//     private function generateDirections(int $count): array
//     {
//         $directions = [];

    
//         for ($i = 0; $i < $count; $i++) {
           
//             $directions[] = rand(0, 360);
//         }

//         return $directions;
//     }
// }

class RouteStopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stops = [1, 2, 3, 4, 5];
        $route_id = 1;
       
        
       
        foreach ($stops as $index => $stop) {
            RouteStop::create([
                'bus_stop_id' => $stop,
                'route_id' => $route_id,
                'route_order' => $index + 1,
                'recent_bus_stop_match' => 0,
            ]);
        }
        
        
    }

   
}

