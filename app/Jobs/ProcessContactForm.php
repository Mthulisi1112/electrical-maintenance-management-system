<?php

namespace App\Jobs;

use App\Mail\ContactFormAdminMail;
use App\Mail\ContactFormAutoReply;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class ProcessContactForm implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
   
    public function __construct(protected Contact $contact)
    {
       
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to('admin@emms.com')
            ->send(new ContactFormAdminMail($this->contact));
        } catch (\Exception $e) {
            Log::error('Failed to send admin email: ' . $e->getMessage());
        }

        try {
            Mail::to('admin@emms.com')
            ->send(new ContactFormAutoReply($this->contact));
         } catch (\Exception $e) {
            Log::error('Failed to send auto-reply: ' . $e->getMessage());
        }

    }
}
