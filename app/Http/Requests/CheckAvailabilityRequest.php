<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
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
            'property_id' => 'required|exists:properties,external_id',
            'check_in' => 'required|date|after_or_equal:today|date_format:Y-m-d',
            'check_out' => 'required|date|after:check_in|date_format:Y-m-d',
            'guests' => 'required|integer|min:1',
        ];
    }
}
