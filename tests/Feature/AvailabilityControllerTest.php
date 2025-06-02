<?php

namespace Tests\Feature;

use App\Interfaces\AvailabilityServiceInterface;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AvailabilityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_check_availability_endpoint_returns_available_rooms(): void
    {
        $mockServiceResponse = [
            'property_id' => '1234',
            'rooms' => [
                [
                    'room_id' => 'r1',
                    'max_guests' => 3,
                    'total_price' => '129.99'
                ],
                [
                    'room_id' => 'r2',
                    'max_guests' => 2,
                    'total_price' => '89.99'
                ]
            ]
        ];

        Property::factory()->create(['external_id' => '1234']);
        $mockService = $this->mock(AvailabilityServiceInterface::class);
        $mockService->shouldReceive('checkAvailability')->once()->andReturn($mockServiceResponse);

        $response = $this->getJson('/api/availability?property_id=1234&check_in=2025-06-02&check_out=2025-06-05&guests=2');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'There is available rooms for you!',
                'data' => $mockServiceResponse
            ]);
    }

    public function test_check_availability_endpoint_returns_no_rooms(): void
    {
        Property::factory()->create(['external_id' => '1234']);
        
        $mockServiceResponse = [
            'property_id' => '1234',
            'rooms' => []
        ];

        $mockService = $this->mock(AvailabilityServiceInterface::class);
        $mockService->shouldReceive('checkAvailability')->once()->andReturn($mockServiceResponse);

        $response = $this->getJson('/api/availability?property_id=1234&check_in=2025-06-02&check_out=2025-06-05&guests=2');


        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'No rooms available for the selected property, dates and guests.',
                'data' => []
            ]);
    }

    public function test_sync_availability_endpoint(): void
    {        
        $syncData = [
            'property_id' => '1234',
            'rooms' => [
                [
                    'room_id' => 'r1',
                    'date' => '2025-06-01',
                    'max_guests' => 3,
                    'price' => 129.99
                ],
                [
                    'room_id' => 'r2',
                    'date' => '2025-06-01',
                    'max_guests' => 2,
                    'price' => 89.99
                ]
            ]
        ];

        $mockServiceResponse = [
            'property_id' => '1234',
            'rooms' => $syncData['rooms']
        ];

        $mockService = $this->mock(AvailabilityServiceInterface::class);
        $mockService->shouldReceive('syncAvailability')->once()->andReturn($mockServiceResponse);

        $response = $this->postJson('/api/availability', $syncData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Availability synced successfully.',
                'data' => $mockServiceResponse
            ]);
    }
}