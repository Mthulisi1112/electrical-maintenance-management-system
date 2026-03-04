<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;

class GenerateAssetQrCodes extends Command
{
    protected $signature = 'assets:generate-qrcodes';
    protected $description = 'Generate QR codes for all assets';

    public function handle()
    {
        $this->info('Starting QR code generation...');
        
        // Get assets without QR codes using the MODEL
        $assets = Asset::whereNull('qr_code')->get();
        
        if ($assets->isEmpty()) {
            $this->info('All assets already have QR codes!');
            return 0;
        }

        $this->info("Found {$assets->count()} assets without QR codes.");
        
        $bar = $this->output->createProgressBar($assets->count());
        $bar->start();

        $generated = 0;
        foreach ($assets as $asset) {
            try {
                $asset->generateQrCode();
                $generated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed for asset ID {$asset->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated QR codes for {$generated} assets!");

        return 0;
    }
}