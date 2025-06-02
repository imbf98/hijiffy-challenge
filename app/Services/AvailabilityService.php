<?php 

namespace App\Services;

use App\Interfaces\AvailabilityServiceInterface;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AvailabilityService implements AvailabilityServiceInterface
{
    public function checkAvailability(string $propertyId, string $checkIn, string $checkOut, int $guests = 1): array
    {
        $checkIn = CarbonImmutable::parse($checkIn);
        $checkOut = CarbonImmutable::parse($checkOut);

        $cacheKey = sprintf(
        'availability:%s:%s:%s:%d',
        $propertyId,
        $checkIn->format('Y-m-d'),
        $checkOut->format('Y-m-d'),
        $guests
        );

        $nights = $checkIn->diffInDays($checkOut);

        return Cache::tags(['property:' . $propertyId, 'availability'])
        ->remember($cacheKey, now()->addMinutes(15), function () use ($propertyId, $checkIn, $checkOut, $guests, $nights) {
            $property = Property::select('id', 'external_id')->where('external_id', $propertyId)->first();

            if (!$property) {
                return [];
            }

            $rooms = Room::select(
                'rooms.external_id as room_id',
                'rooms.max_guests',
                DB::raw('SUM(room_availabilities.price) as total_price')
            )
            ->join('room_availabilities', 'room_availabilities.room_id', '=', 'rooms.id')
            ->where('rooms.property_id', '=', $property->id)
            ->where('rooms.max_guests', '>=', $guests)
            ->whereBetween('room_availabilities.date', [
                $checkIn->format('Y-m-d'),
                $checkOut->subDay()->format('Y-m-d'),
            ])
            ->where('room_availabilities.is_available', true)
            ->groupBy('rooms.id', 'rooms.external_id', 'rooms.max_guests')
            ->havingRaw('COUNT(room_availabilities.date) = ?', [$nights])
            ->get();

            return [
                'property_id' => $property->external_id,
                'rooms' => $rooms->map(function ($room) {
                    return [
                        'room_id' => $room->room_id,
                        'max_guests' => $room->max_guests,
                        'total_price' => $room->total_price,
                    ];
                })->toArray()
            ];
        });
    }

    public function syncAvailability(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $property = Property::updateOrCreate(
                ['external_id' => $data['property_id']],
                [
                    'name' => "Property {$data['property_id']}"]
            );

            $roomsData = [];
            foreach ($data['rooms'] as $roomData) {
                $room = Room::updateOrCreate([
                    'property_id' => $property->id,
                    'external_id' => $roomData['room_id']
                ], [
                    'max_guests' => $roomData['max_guests'] ?? 1,
                    'name' => "Room {$roomData['room_id']}"
                ]);

                $availability = RoomAvailability::updateOrCreate([
                    'room_id' => $room->id,
                    'date' => $roomData['date']
                ], [
                    'price' => $roomData['price'],
                    'is_available' => true
                ]);

                $roomsData[] = [
                    'room_id' => $room->external_id,
                    'date' => Carbon::parse($availability->date)->format('Y-m-d'),
                    'max_guests' => $room->max_guests,
                    'price' => $availability->price,
                ];
            }

            $this->clearCacheForProperty($property->external_id);

            return [
                'property_id' => $property->external_id,
                'rooms' => $roomsData ?? []
            ];
        });
    }

    /**
     * Clear the cache for a specific property.
     * @param string $propertyId
     * @return void
     */
    public function clearCacheForProperty(string $propertyId): void
    {
        Cache::tags(['property:' . $propertyId, 'availability'])->flush();
    }
}