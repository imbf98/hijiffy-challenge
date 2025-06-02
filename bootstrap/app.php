<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('availability:sync')->daily();
    })
    ->booting(function () {
        RateLimiter::for('availability', function (Request $request) {
            $user = $request->user();
            $propertyId = $request->input('property_id') ?? null;
            
            $key = $user ? "user:{$user->id}" : "ip:{$request->ip()}";
            $propertyKey = $propertyId ? "{$key}:property:{$propertyId}" : $key;
            
            return [
                Limit::perHour(100)->by($key)->response(function () {
                    return response()->json([
                        'message' => 'You have exceeded the availability request limit. Please wait and try again later.',
                    ], 429);
                }),
                Limit::perHour(50)->by($propertyKey)->response(function () {
                    return response()->json([
                        'message' => 'You have exceeded the availability request limit for this property. Please wait before trying again for this specific property.',
                    ], 429);
                }),
            ];
        });
    })
    ->create();
