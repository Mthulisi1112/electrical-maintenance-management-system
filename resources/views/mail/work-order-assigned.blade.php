<x-mail::message>
# New Work Order Assigned

Hello {{ $workOrder->technician->name }},

You have been assigned a new work order:

** Order #: ** {{ $workOrder->work_order_number }}
** Title:** {{ $workOrder->asset->name ?? 'N/A' }}
** Type:** {{ ucfirst($workOrder->type) }}
** Scheduled Date:** {{ $workOrder->scheduled_date->format('M d, Y') }}
** Priority:** {{ ucfirst($workOrder->priority ?? 'medium') }}

<x-mail::button :url="route('work-orders.show', $workOrder)">
View Work Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
