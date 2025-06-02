<?php

namespace App\Interfaces;

interface AvailabilityServiceInterface
{
    /**
     * Check if a property is available.
     *
     * @param string $propertyId
     * @param string $checkIn
     * @param string $checkOut
     * @param int $guests
     * @return array{
     *     property_id: string,
     *     rooms: array<array{
     *         room_id: string,
     *         max_guests: int,
     *         total_price: float
     *     }>
     * }
     */
    public function checkAvailability(string $propertyId, string $checkIn, string $checkOut, int $guests): array;

    /**
     * Availability ingestion handler.
     *
     * @param array{
     *     property_id: string,
     *     rooms: array<array{
     *         room_id: string,
     *         date: string,
     *         max_guests: int,
     *         price: float
     *     }>
     * } $data
     * @return array{
     *     property_id: string,
     *     rooms: array<array{
     *         room_id: string,
     *         max_guests: int,
     *         price: float,
     *         date?: string
     *     }>
     * }
     */
    public function syncAvailability(array $data): array;

    /**
     * Clear the cache for a specific property.
     *
     * @param string $propertyId
     * @return void
     */
    public function clearCacheForProperty(string $propertyId): void;
}
