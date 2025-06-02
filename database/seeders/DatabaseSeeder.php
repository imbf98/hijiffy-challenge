<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Property::factory(10)->create();
        Room::factory(10)->create();
        Availability::factory(10)->create();   
    }
}
