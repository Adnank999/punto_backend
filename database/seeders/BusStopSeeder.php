<?php

namespace Database\Seeders;

use App\Models\BusStop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BusStopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

       
        $directions = $this->generateDirections(20);

        for ($i = 0; $i < 20; $i++) {
            BusStop::create([
                'name' => $faker->streetName,
                'latitude' => $faker->latitude(40.4774, 40.9176),
                'longitude' => $faker->longitude(-74.2591, -73.7004),
                'predefined_time' => rand(100, 200),  
                'predefined_radius' => 100,
                'predefined_direction' => $directions[$i],  
            ]);
        }
    }

    private function generateDirections(int $count): array
    {
        $directions = [];

        // Generate random directions for each bus stop (random values between 0 and 360)
        for ($i = 0; $i < $count; $i++) {
            $directions[] = rand(0, 360);
        }

        return $directions;
    }
}
