<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // You can initialize any common properties or services here.
    }

    /**
     * Json response skeleton for API responses.
     * @param bool $success
     * @param string|null $message
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(bool $success = true, ?string $message = null, array $data, int $status = 200): \Illuminate\Http\JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $status);
    }
}
