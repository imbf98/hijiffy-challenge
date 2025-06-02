<?php

namespace App\Providers;

use App\Interfaces\AvailabilityServiceInterface;
use App\Services\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AvailabilityServiceInterface::class,
            AvailabilityService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Date::use(CarbonImmutable::class);
    }
}
