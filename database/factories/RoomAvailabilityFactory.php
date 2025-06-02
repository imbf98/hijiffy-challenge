<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomAvailability>
 */
class RoomAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'date' => $this->faker->unique()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'is_available' => $this->faker->boolean(80),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
