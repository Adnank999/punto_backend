<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $routes = Route::all(); 

        for ($i = 0; $i < 100; $i++) {
            Bus::create([
                'gps_device_id' => $faker->uuid,
                'route_id' => $routes->random()->id,
                'current_latitude' => $faker->latitude(40.4774, 40.9176), 
                'current_longitude' => $faker->longitude(-74.2591, -73.7004), 
                'active' => $faker->boolean,
            ]);
        }
    }
}
