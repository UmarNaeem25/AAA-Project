<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Head Office', 'address' => '123 Main St'],
            ['name' => 'Branch A', 'address' => '456 Elm St'],
            ['name' => 'Branch B', 'address' => '789 Pine St'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(['name' => $loc['name']], $loc);
        }
    }
}
