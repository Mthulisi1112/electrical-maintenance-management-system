<?php

namespace App\Jobs;

use App\Models\Asset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAssetQrCodes implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Asset $asset)
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->asset->generateQrCode();
    }
}
