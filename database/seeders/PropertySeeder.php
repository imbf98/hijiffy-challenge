<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder can be used to populate the properties table with initial data.
        // For now, we can create a few sample properties.

        \App\Models\Property::create([
            'external_id' => 'prop_001',
            'name' => 'Sample Property 1',
            'address' => '123 Sample St, Sample City, SC 12345',
        ]);

        \App\Models\Property::create([
            'external_id' => 'prop_002',
            'name' => 'Sample Property 2',
            'address' => '456 Example Ave, Example City, EC 67890',
        ]);
    }
}
