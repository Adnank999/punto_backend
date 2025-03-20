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

        for ($i = 0; $i < 100; $i++) {
            BusStop::create([
                'name' => $faker->streetName,
                'latitude' => $faker->latitude(40.4774, 40.9176),
                'longitude' => $faker->longitude(-74.2591, -73.7004), 
            ]);
        }
    }
}
