<?php 

namespace App\Parsers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AvailabilitySyncParser
{
    /**
     * Parse the raw availability data into a structured format.
     *
     * @param array $rawData
     * @return array
     */
    public function parse( array $data): array
    {
        $validator = Validator::make($data, [
            'property_id' => 'required|string',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_id' => 'required|string',
            'rooms.*.date' => 'required|date|date_format:Y-m-d',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.max_guests' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::error('Availability JSON validation failed', [
                'errors' => $validator->errors(),
                'data' => $data,
            ]);
            throw new ValidationException($validator);
        }

        return $data;
    }
}