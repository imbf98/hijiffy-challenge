<?php

namespace Tests\Unit\Controllers\API;

use App\Http\Controllers\API\AvailabilityController;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\SyncAvailabilityRequest;
use App\Interfaces\AvailabilityServiceInterface;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class AvailabilityControllerTest extends TestCase
{
    protected AvailabilityServiceInterface $availabilityService;
    protected AvailabilityController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->availabilityService = $this->mock(AvailabilityServiceInterface::class);
        $this->controller = new AvailabilityController($this->availabilityService);
    }

    public function test_check_returns_available_rooms(): void
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

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->with('1234', '2025-06-01', '2025-06-05', 2)
            ->andReturn($mockServiceResponse);

        $request = $this->mock(CheckAvailabilityRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'property_id' => '1234',
            'check_in' => '2025-06-01',
            'check_out' => '2025-06-05',
            'guests' => 2
        ]);

        $response = $this->controller->check($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals('There is available rooms for you!', $responseData['message']);
        $this->assertEquals($mockServiceResponse, $responseData['data']);
    }

    public function test_check_returns_no_rooms(): void
    {
        $mockServiceResponse = [
            'property_id' => '1234',
            'rooms' => []
        ];

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->with('1234', '2025-06-01', '2025-06-05', 2)
            ->andReturn($mockServiceResponse);

        $request = $this->mock(CheckAvailabilityRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'property_id' => '1234',
            'check_in' => '2025-06-01',
            'check_out' => '2025-06-05',
            'guests' => 2
        ]);

        $response = $this->controller->check($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals('No rooms available for the selected property, dates and guests.', $responseData['message']);
        $this->assertEquals([], $responseData['data']);
    }

    public function test_sync_returns_success_response(): void
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

        $this->availabilityService
            ->shouldReceive('syncAvailability')
            ->once()
            ->with($syncData)
            ->andReturn($mockServiceResponse);

        $request = $this->mock(SyncAvailabilityRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($syncData);

        $response = $this->controller->sync($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Availability synced successfully.', $responseData['message']);
        $this->assertEquals($mockServiceResponse, $responseData['data']);
    }
}