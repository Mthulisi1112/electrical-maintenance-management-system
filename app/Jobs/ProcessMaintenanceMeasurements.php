<?php

namespace App\Jobs;

use App\Models\MaintenanceLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMaintenanceMeasurements implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected MaintenanceLog $maintenanceLog)
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $measurements = $this->maintenanceLog->measurements;

        if (!$measurements) {
            return;
        }

        // Process measurements for trends analysis
        foreach ($measurements as $measurement) {
           // store in a separate analytics table
           // App\models\MeasurementHistory::create([
           //                                    'asset_id' => $this->maintenanceLog->asset_id,
           //                                    'name' => $measurement['name],
           //                                    'value => $measurement['value'],
           //                                    'unit' => $measurement['unit] ?? null,
           //                                    'recorded_at' => $this->maintenanceLog->created_at, 
           //                                     ]);
        }

        // Check for anomalies (e.g, temperature spike)
        //$this->checkForAnomalies($measurements);
    }
}
