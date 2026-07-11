<?php

namespace App\Jobs;

use App\Mail\BulkWorkOrdersFailed;
use App\Mail\BulkWorkOrdersGenerated;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateBulkWorkOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    protected $scheduleIds;
    protected $createdWorkOrders = [];
    protected $failedSchedules = [];

    public function __construct(array $scheduleIds)
    {
        $this->scheduleIds = $scheduleIds;
    }

    public function handle()
    {
        try {
            $technician = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->first();

            if (!$technician) {
                Log::warning('No technician found for bulk work order generation', [
                    'schedule_ids' => $this->scheduleIds
                ]);
                $this->sendSummaryEmail(null, 'No technician available');
                return;
            }

            $schedules = MaintenanceSchedule::whereIn('id', $this->scheduleIds)
                ->where('is_active', true)
                ->get();

            $totalSchedules = $schedules->count();

            if ($totalSchedules === 0) {
                Log::warning('No active schedules found for bulk generation', [
                    'schedule_ids' => $this->scheduleIds
                ]);
                $this->sendSummaryEmail($technician);
                return;
            }

            foreach ($schedules as $schedule) {
                try {
                    $workOrder = WorkOrder::create([
                        'asset_id' => $schedule->asset_id,
                        'maintenance_schedule_id' => $schedule->id,
                        'technician_id' => $technician->id,
                        'type' => 'preventive',
                        'status' => 'pending',
                        'title' => 'Scheduled Maintenance: ' . $schedule->title,
                        'description' => $schedule->description,
                        'checklist' => $schedule->checklist_items,
                        'scheduled_date' => $schedule->next_due_date,
                        'supervisor_id' => auth()->id() ?? $technician->supervisor_id ?? null,
                    ]);

                    $this->createdWorkOrders[] = $workOrder;
                    
                    Log::info('Work order created from schedule', [
                        'schedule_id' => $schedule->id,
                        'work_order_id' => $workOrder->id
                    ]);

                } catch (\Exception $e) {
                    $this->failedSchedules[] = [
                        'id' => $schedule->id,
                        'title' => $schedule->title ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to create work order from schedule', [
                        'schedule_id' => $schedule->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->sendSummaryEmail($technician);

            Log::info('Bulk work order generation completed', [
                'total_schedules' => $totalSchedules,
                'created_count' => count($this->createdWorkOrders),
                'failed_count' => count($this->failedSchedules)
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk work order generation failed', [
                'schedule_ids' => $this->scheduleIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->sendFailureEmail($e);
            throw $e;
        }
    }

    /**
     * Send summary email to supervisor.
     */
    protected function sendSummaryEmail(?User $technician, string $warning = null): void
    {
        try {
            $recipients = $this->getEmailRecipients();

            if (empty($recipients)) {
                Log::warning('No recipients for bulk work order email');
                return;
            }

            $schedules = MaintenanceSchedule::whereIn('id', $this->scheduleIds)->get();

            // Create the mailable
            $mailable = new BulkWorkOrdersGenerated(
                count($this->createdWorkOrders),
                $schedules->count(),
                $this->failedSchedules,
                $this->createdWorkOrders,
                $technician,
                $schedules
            );

            // Add any additional recipients via CC or BCC
            $ccRecipients = config('mail.bulk_work_order_cc', []);
            if (!empty($ccRecipients)) {
                $mailable->cc($ccRecipients);
            }

            // Send the email
            Mail::to($recipients)->send($mailable);

            Log::info('Bulk work orders summary email sent', [
                'recipients' => $recipients,
                'work_orders_count' => count($this->createdWorkOrders)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send bulk work orders summary email', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send failure email to admin.
     */
    protected function sendFailureEmail(\Exception $e): void
    {
        try {
            $adminEmail = config('mail.admin_email');
            
            if ($adminEmail) {
                Mail::to($adminEmail)->send(
                    new BulkWorkOrdersFailed(
                        $e->getMessage(),
                        $this->scheduleIds,
                        $e->getTraceAsString()
                    )
                );
            }
        } catch (\Exception $mailError) {
            Log::error('Failed to send failure email', [
                'error' => $mailError->getMessage()
            ]);
        }
    }

    /**
     * Get email recipients for the summary.
     */
    protected function getEmailRecipients(): array
    {
        $recipients = [];

        $schedule = MaintenanceSchedule::whereIn('id', $this->scheduleIds)->first();
        
        if ($schedule && $schedule->supervisor_id) {
            $supervisor = User::find($schedule->supervisor_id);
            if ($supervisor && $supervisor->email) {
                $recipients[] = $supervisor->email;
            }
        }

        if (empty($recipients)) {
            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                $recipients[] = $adminEmail;
            }
        }

        $additionalRecipients = config('mail.bulk_work_order_recipients', []);
        $recipients = array_merge($recipients, $additionalRecipients);

        return array_unique($recipients);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateBulkWorkOrders job failed permanently', [
            'schedule_ids' => $this->scheduleIds,
            'error' => $exception->getMessage()
        ]);

        $this->sendFailureEmail($exception);
    }
}
