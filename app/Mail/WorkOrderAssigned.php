<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Markdown;
use Illuminate\Queue\SerializesModels;

class WorkOrderAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public function __construct(public WorkOrder $workOrder)
    {
       
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Work Order Assigned:'. $this->workOrder->work_order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-order-assigned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
