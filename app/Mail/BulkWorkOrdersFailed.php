<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkWorkOrdersFailed extends Mailable
{
    use Queueable, SerializesModels;

    public $errorMessage;
    public $scheduleIds;
    public $trace;

    /**
     * Create a new message instance.
     */
    public function __construct(string $errorMessage, array $scheduleIds, ?string $trace = null)
    {
        $this->errorMessage = $errorMessage;
        $this->scheduleIds = $scheduleIds;
        $this->trace = $trace;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'URGENT: Bulk Work Orders Generation Failed',
            tags: ['bulk-work-orders', 'failed', 'urgent'],
            metadata: [
                'schedule_ids' => implode(', ', $this->scheduleIds),
                'failed_at' => now()->toDateTimeString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.bulk-work-orders-failed',
            with: [
                'errorMessage' => $this->errorMessage,
                'scheduleIds' => $this->scheduleIds,
                'scheduleCount' => count($this->scheduleIds),
                'failedAt' => now()->format('F d, Y H:i:s'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach error log if trace is available
        if ($this->trace) {
            $attachments[] = Attachment::fromData(
                fn () => $this->trace,
                'error-trace.log'
            )->withMime('text/plain');
        }

        // Attach schedule IDs list
        if (count($this->scheduleIds) > 0) {
            $scheduleList = implode("\n", $this->scheduleIds);
            
            $attachments[] = Attachment::fromData(
                fn () => "Schedule IDs that failed:\n\n" . $scheduleList,
                'failed-schedule-ids.txt'
            )->withMime('text/plain');
        }

        return $attachments;
    }
}
