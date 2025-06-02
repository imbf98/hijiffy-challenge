<?php

namespace App\Jobs;

use App\Interfaces\AvailabilityServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncAvailabilityJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AvailabilityServiceInterface $availabilityService): void
    {
        try {
            $availabilityService->syncAvailability($this->data);
            Log::info('SyncAvailabilityJob completed for property ' . $this->data['property_id']);
        } catch (\Throwable $e) {
            Log::error('SyncAvailabilityJob failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'property_id' => $this->data['property_id'] ?? null,
            ]);
            throw $e;
        }
    }
}
