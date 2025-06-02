<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Room::create([
            'external_id' => 'room_001',
            'property_id' => 1, // Assuming property with ID 1 exists
            'max_guests' => 2,
        ]);

        \App\Models\Room::create([
            'external_id' => 'room_002',
            'property_id' => 1, // Assuming property with ID 1 exists
            'max_guests' => 4,
        ]);

        \App\Models\Room::create([
            'external_id' => 'room_003',
            'property_id' => 2, // Assuming property with ID 2 exists
            'max_guests' => 3,
        ]);
    }
}
