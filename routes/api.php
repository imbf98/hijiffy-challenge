<?php

use App\Http\Controllers\API\AvailabilityController;
use App\Http\Controllers\API\DialogflowController;
use App\Http\Requests\ApiGetTokenRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum','throttle:availability'])->group(function () {
    Route::get('/availability', [AvailabilityController::class, 'check']);
    Route::post('/availability', [AvailabilityController::class, 'sync']);
});

Route::post('/dialogflow/webhook', [DialogflowController::class, 'handle']);

Route::post('/token', function (ApiGetTokenRequest $request) {
    $validated = $request->validated();

    $user = User::where('email', $validated['email'])->first();
    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], status: 401);
    }

    $token = config('api.api_name') . ' - ' . now()->toDateTimeString();
    $token = $user->createToken($token, ['*'])->plainTextToken;

    return response()->json([
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => $user->only(['name', 'email']),
    ]);
});
