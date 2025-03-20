<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BusStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $buses = Bus::all();

        foreach ($buses as $bus) {
            BusStatus::create([
                'bus_id' => $bus->id,
                'status' => $faker->boolean, 
                'reason' => $faker->optional()->text,
            ]);
        }
    }
}
