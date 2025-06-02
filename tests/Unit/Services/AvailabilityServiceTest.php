<?php

namespace Tests\Unit\Services;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AvailabilityService();
    }

    public function test_check_availability_returns_correct_structure(): void
    {
        $property = Property::factory()->create(['external_id' => '1234']);
        $room = Room::factory()->create([
            'property_id' => $property->id,
            'external_id' => 'r1',
            'max_guests' => 3,
        ]);
        RoomAvailability::factory()->create([
            'room_id' => $room->id,
            'date' => '2025-06-01',
            'price' => 129.99,
            'is_available' => true,
        ]);
        RoomAvailability::factory()->create([
            'room_id' => $room->id,
            'date' => '2025-06-02',
            'price' => 129.99,
            'is_available' => true,
        ]);
    
        $result = $this->service->checkAvailability('1234', '2025-06-01', '2025-06-02', 1);

        $this->assertEquals('1234', $result['property_id']);
        $this->assertCount(1, $result['rooms']);
        $this->assertEquals('r1', $result['rooms'][0]['room_id']);
        $this->assertEquals(3, $result['rooms'][0]['max_guests']);
        $this->assertEquals(129.99, $result['rooms'][0]['total_price']);
    }

    public function test_sync_availability_creates_records(): void
    {
        $data = [
            'property_id' => '1234',
            'rooms' => [
                [
                    'room_id' => 'r1',
                    'date' => '2025-06-01',
                    'max_guests' => 3,
                    'price' => 129.99
                ]
            ]
        ];

        $result = $this->service->syncAvailability($data);

        $this->assertEquals('1234', $result['property_id']);
        $this->assertCount(1, $result['rooms']);
        $this->assertEquals('r1', $result['rooms'][0]['room_id']);
        $this->assertEquals('2025-06-01', $result['rooms'][0]['date']);
        $this->assertEquals(3, $result['rooms'][0]['max_guests']);
        $this->assertEquals(129.99, $result['rooms'][0]['price']);

        //assert database has the created records
        $this->assertDatabaseHas('properties', ['external_id' => '1234']);
        $this->assertDatabaseHas('rooms', ['external_id' => 'r1', 'property_id' => Property::where('external_id', '1234')->first()->id]);
        $this->assertDatabaseHas('room_availabilities', [
            'room_id' => Room::where('external_id', 'r1')->first()->id,
            'date' => '2025-06-01 00:00:00',
            'price' => 129.99,
            'is_available' => 1
        ]);
    }
}