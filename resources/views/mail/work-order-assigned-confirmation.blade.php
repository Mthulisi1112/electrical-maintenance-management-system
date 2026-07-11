<x-mail::message>
# Work Order Assignment Confirmation

Dear {{ $supervisor->name }},

This is to confirm that a work order has been successfully assigned to a technician 
under your supervision.


## Work Order Details

** Order #:** {{ $workOrder->work_order_number }}
** Title:** {{ $workOrder->title }}
** Type:** {{ ucfirst($workOrder->type) }}
** Priority:** <span style="color:@if($workOrder->priority == 'high')#dc2626 
                                     @elseif($workOrder->priority == 'medium')#f59e0b
                                     @else #16a34a
                                     @endif">{{ ucfirst($workOrder->priority ?? 'Medium') }}
                </span>
** Scheduled Date:** {{ $workOrder->scheduled_date->format('M D, Y') }}
** Status:** {{ ucfirst($workOrder->status) }}
      
---

## Assigned Technician

**Name:** {{ $technician->name }}
**Email:** {{ $technician->email }}
**Phone:** {{ $technician->phone ?? 'N/A'}}

---

## Asset Information

**Asset:** {{ $workOrder->asset->name }}
**Location:** {{ $workOrder->asset->location ?? 'Not Specified' }}


@if ($workOrder->description)
**Description:** {{ $workOrder->description }}  
@endif

## Next Steps

1. Technician has been notified of this Assignment
2. They will confirm their availability shortly
3. You will be notified of any updates or escalations
                                    
<x-mail::button :url="route('work-orders.show', $workOrder)">
View Work Order Details
</x-mail::button>

Please review this assisgnment and reach out to the technician if you have 
any concerns or additional instructions.

Regards,<br>
{{ config('app.name') }}
Management System
</x-mail::message>
