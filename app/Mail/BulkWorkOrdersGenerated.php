<?php

namespace App\Mail;

use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkWorkOrdersGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $generatedCount;
    public $totalSchedules;
    public $failedSchedules;
    public $workOrders;
    public $technician;
    public $schedules;
    public $successRate;
    public $hasFailures;

    /**
     * Create a new message instance.
     */
    public function __construct(
        int $generatedCount,
        int $totalSchedules,
        array $failedSchedules = [],
        $workOrders = null,
        User $technician = null,
        $schedules = null
    ) {
        $this->generatedCount = $generatedCount;
        $this->totalSchedules = $totalSchedules;
        $this->failedSchedules = $failedSchedules;
        $this->workOrders = $workOrders;
        $this->technician = $technician;
        $this->schedules = $schedules;
        $this->successRate = $totalSchedules > 0 
            ? round(($generatedCount / $totalSchedules) * 100, 2) 
            : 0;
        $this->hasFailures = count($failedSchedules) > 0;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bulk Work Orders Generated - ' . now()->format('M d, Y H:i'),
            tags: ['bulk-work-orders', 'generated'],
            metadata: [
                'generated_count' => $this->generatedCount,
                'total_schedules' => $this->totalSchedules,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.bulk-work-orders-generated',
            with: [
                'generatedCount' => $this->generatedCount,
                'totalSchedules' => $this->totalSchedules,
                'failedSchedules' => $this->failedSchedules,
                'workOrders' => $this->workOrders,
                'technician' => $this->technician,
                'schedules' => $this->schedules,
                'successRate' => $this->successRate,
                'hasFailures' => $this->hasFailures,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Example: Attach a CSV report of generated work orders
        $attachments = [];

        // If there are work orders generated, attach a CSV summary
        if ($this->workOrders && count($this->workOrders) > 0) {
            $csv = $this->generateCsvReport();
            
            if ($csv) {
                $attachments[] = Attachment::fromData(fn () => $csv, 'work-orders-summary.csv')
                    ->withMime('text/csv');
            }
        }

        // If there are failures, attach a failure report
        if ($this->hasFailures && count($this->failedSchedules) > 0) {
            $failuresCsv = $this->generateFailuresCsv();
            
            if ($failuresCsv) {
                $attachments[] = Attachment::fromData(fn () => $failuresCsv, 'failed-schedules.csv')
                    ->withMime('text/csv');
            }
        }

        return $attachments;
    }

    /**
     * Generate CSV report of generated work orders.
     */
    protected function generateCsvReport(): ?string
    {
        if (!$this->workOrders || count($this->workOrders) === 0) {
            return null;
        }

        $handle = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($handle, [
            'Work Order ID',
            'Title',
            'Asset',
            'Technician',
            'Scheduled Date',
            'Status',
            'Schedule ID'
        ]);

        // Add data
        foreach ($this->workOrders as $workOrder) {
            fputcsv($handle, [
                $workOrder->id,
                $workOrder->title,
                $workOrder->asset->name ?? 'N/A',
                $this->technician->name ?? 'N/A',
                $workOrder->scheduled_date?->format('Y-m-d') ?? 'N/A',
                $workOrder->status,
                $workOrder->maintenance_schedule_id
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Generate CSV report of failed schedules.
     */
    protected function generateFailuresCsv(): ?string
    {
        if (!$this->hasFailures || count($this->failedSchedules) === 0) {
            return null;
        }

        $handle = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($handle, [
            'Schedule ID',
            'Title',
            'Error Message'
        ]);

        // Add data
        foreach ($this->failedSchedules as $failure) {
            fputcsv($handle, [
                $failure['id'] ?? 'N/A',
                $failure['title'] ?? 'N/A',
                $failure['error'] ?? 'Unknown error'
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
