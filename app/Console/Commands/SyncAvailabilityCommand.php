<?php

namespace App\Console\Commands;

use App\Jobs\SyncAvailabilityJob;
use App\Parsers\AvailabilitySyncParser;
use Illuminate\Console\Command;

class SyncAvailabilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:availability';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest availability data for properties and rooms';

    /**
     * Execute the console command.
     */
    public function handle(AvailabilitySyncParser $parser): void
    {
        $this->info('Starting availability ingestion...');

        //Assuming the json file have the same structure as the one used in the SyncAvailabilityRequest but for multiple properties
        $json = storage_path('app/availability.json');
        if (!file_exists($json)) {
            $this->error('Availability JSON file not found at ' . $json);
            return;
        }

        $this->info('Reading availability data from ' . $json);

        $data = json_decode(file_get_contents($json), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format in ' . $json);
            return;
        }

        if (empty($data)) {
            $this->info('No availability data found in ' . $json);
            return;
        }

        $this->info('Found ' . count($data) . ' properties to ingest.');


        foreach ($data as $propertyData) {
            $parsedData = $parser->parse($propertyData);
            SyncAvailabilityJob::dispatch($parsedData);
            $this->info('Successfully ingested availability for property: ' . $propertyData['property_id']);
        }


        $this->info('Availability ingestion completed successfully.');
    }
}
