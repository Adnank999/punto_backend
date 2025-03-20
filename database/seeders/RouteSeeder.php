<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

       
        for ($i = 0; $i < 20; $i++) {
            Route::create([
                'name' => $faker->word . ' Route ' . $faker->numberBetween(1, 100), // Example: "Broadway Route 12"
            ]);
        }
    }
}
