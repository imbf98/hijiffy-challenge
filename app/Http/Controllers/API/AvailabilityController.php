<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\SyncAvailabilityRequest;
use App\Interfaces\AvailabilityServiceInterface;

class AvailabilityController extends Controller
{
    public function __construct(private readonly AvailabilityServiceInterface $availabilityService)
    {
    }
    
    /**
     * Room availability check for a property
     * @param CheckAvailabilityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(CheckAvailabilityRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();
        $availability = $this->availabilityService->checkAvailability(
            propertyId: $validatedData['property_id'],
            checkIn: $validatedData['check_in'],
            checkOut: $validatedData['check_out'],
            guests: $validatedData['guests']
        );

        if (empty($availability['rooms'])) {
            return $this->jsonResponse(
                false,
                'No rooms available for the selected property, dates and guests.',
                [],
                200
            );
        }

        return $this->jsonResponse(
            true,
            'There is available rooms for you!',
            $availability,
            200
        );

    }

    /**
     * Room availability ingestion
     * @param SyncAvailabilityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(SyncAvailabilityRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->availabilityService->syncAvailability($request->validated());
        return $this->jsonResponse(
            true,
            'Availability synced successfully.',
            $data,
            200
        );
    }
}