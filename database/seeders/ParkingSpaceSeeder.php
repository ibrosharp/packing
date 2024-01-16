<?php

namespace Database\Seeders;

use App\Models\PackingSpace;
use App\Models\PackingSpacePricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParkingSpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $spaces = [];

        for($id = 1; $id <= 10; $id++) {
           array_push($spaces, ["name" => "Spaces{$id}"]);
        }

        PackingSpace::insert($spaces);

        foreach(PackingSpace::all() as $packingSpace) {
            PackingSpacePricing::create([
                'parking_space_id' => $packingSpace->id,
                "weekday_price" => 100.00,
                "weekend_price" => 120.00,
                "summer_price_multiplier" => 1.2

            ]);
        }

       
    }
}
