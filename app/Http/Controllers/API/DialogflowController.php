<?php 

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\AvailabilityServiceInterface;
use Illuminate\Http\Request;

class DialogflowController extends Controller
{

    public function __construct(private readonly AvailabilityServiceInterface $availabilityService)
    {
    }

    /**
     * Handle the incoming request.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $intent = $request->input('queryResult.intent.displayName');

        $response = match ($intent) {
            'CheckAvailability' => $this->checkAvailability($request),
            'CheckInTime' => 'Check-in time is from 3 PM onwards.',
            'CheckOutTime' => 'Check-out time is before 11 AM.',
            'ParkingAvailability' => 'Yes, free parking is available at the property.',
            default => 'I am not sure how to help with that.',
        };

        return response()->json(['fulfillmentText' => $response], 200);
    }

    /**
     * Check availability of rooms based on the request parameters.
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function checkAvailability(Request $request): string
    {
        $propertyId = $request->input('queryResult.parameters.property_id');
        $checkIn = $request->input('queryResult.parameters.check_in');
        $checkOut = $request->input('queryResult.parameters.check_out');
        $guests = $request->input('queryResult.parameters.guests');

        if (!$propertyId) {
            return 'Which property are you interested in?';
        } 
        
        if (!$checkIn) {
            return 'When do you plan to check in?';
        }
        
        if (!$checkOut) {
            return 'When do you plan to check out?';
        } 
        
        if (!$guests || $guests < 1) {
            return 'How many guests will be staying?';
        }

        $result = $this->availabilityService->checkAvailability(
            propertyId: $propertyId,
            checkIn: $checkIn,
            checkOut: $checkOut,
            guests: $guests
        );

        if (empty($result['rooms'])) {
            return 'No rooms available for the selected property, dates, and guests.';
        }

        $rooms = count($result['rooms']);
        $minPrice = min(array_column($result['rooms'], 'total_price'));

        return "Yes! We have {$rooms} room(s) available from {$checkIn} to {$checkOut}, starting at \${$minPrice}. Want to reserve now?";
    }
}