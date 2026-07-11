<?php

namespace App\Jobs;

use App\Models\Fault;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
//use Intervention\Image\Facades\Image;  Bash composer require intervention /image-laravel


class ProcessFaultImages implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Fault $fault, protected array $images)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processedImages = [];

        foreach ($this->images as $image) {
            // Optimize and resize image
            $img = Image::make($image);
            $img->resize(800, 800, function($constraint) {  // image->scale(width:300)
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save optimized image
            $path = 'faults/' . $this->fault->id . '/' . uniqid() . 'jpg';
            Storage::put($path, $img->encode('jpg', 80));
            $processedImages[] = $path;

            //Delete original
            Storage::delete($image);
        }

        // Update fault with processed image paths
        $existing = json_decode($this->fault->images ?? '[]' , true) ?:[];

        $this->fault->update(['images' => json_encode(array_merge($existing, $processedImages))]);
    }
}
