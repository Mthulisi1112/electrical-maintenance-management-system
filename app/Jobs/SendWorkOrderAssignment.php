<?php

namespace App\Jobs;

use App\Mail\WorkOrderAssigned;
use App\Mail\WorkOrderAssignedConfirmation;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderAssignment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WorkOrder $workOrder;

    /**
     * Create a new job instance.
     */
    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        // Get technician and supervisor from the work order
        $technician = $this->workOrder->technician;
        $supervisor = $this->workOrder->supervisor;

            
        // You can Mail to cc/bcc/etc. or even use Collections eg ->bcc(User::all())
        Mail::to($technician->email)
            ->send(new WorkOrderAssigned($this->workOrder));

        Mail::to($supervisor->email)
            ->send(new WorkOrderAssignedConfirmation($this->workOrder));
        
    }

    
      
}
