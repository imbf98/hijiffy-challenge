<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|string',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_id' => 'required|string',
            'rooms.*.date' => 'required|date|date_format:Y-m-d',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.max_guests' => 'sometimes|integer|min:1',
        ];
    }
}
