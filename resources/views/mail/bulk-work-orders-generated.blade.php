<x-mail::message>
# Bulk Work Orders Generated

Hello,

This email is to confirm that bulk work orders have been generated from maintenance schedules.

---

## Summary

| Metric | Value |
|--------|-------|
| **Total Schedules Processed** | {{ $totalSchedules }} |
| **Work Orders Created** | {{ $generatedCount }} |
| **Success Rate** | {{ $successRate }}% |
| **Failed Schedules** | {{ count($failedSchedules) }} |
| **Generated At** | {{ now()->format('F d, Y H:i:s') }} |

---

## Assigned Technician

@if($technician)
- **Name:** {{ $technician->name }}
- **Email:** {{ $technician->email }}
@else
 No technician was available. Please assign technicians manually.
@endif

---

## Generated Work Orders

@if($workOrders && count($workOrders) > 0)
| # | Work Order | Asset | Scheduled Date | Status |
|---|------------|-------|----------------|--------|
@foreach($workOrders as $index => $workOrder)
| {{ $index + 1 }} | {{ $workOrder->title }} | {{ $workOrder->asset->name ?? 'N/A' }} | {{ $workOrder->scheduled_date?->format('M d, Y') ?? 'TBD' }} | {{ ucfirst($workOrder->status) }} |
@endforeach

<x-mail::button :url="route('work-orders.index', ['status' => 'pending'])">
View All Work Orders
</x-mail::button>

@else
No work orders were generated.
@endif

---

## Failures

@if($hasFailures)
@foreach($failedSchedules as $failure)
-**Schedule #{{ $failure['id'] ?? 'Unknown' }}** 
  - Title: {{ $failure['title'] ?? 'N/A' }}
  - Error: {{ $failure['error'] ?? 'Unknown error' }}
@endforeach

**Recommended Action:** Review the failed schedules and retry generation manually.

@else
No failures occurred during the generation process.
@endif

---

## Attachments

This email includes the following attachments:
- **work-orders-summary.csv** - CSV export of all generated work orders
- **failed-schedules.csv** - CSV export of any failed schedules (if applicable)

---

This is an automated notification from your maintenance management system.

Regards,<br>
{{ config('app.name') }} Maintenance Team
</x-mail::message>
