<x-mail::message>
# Bulk Work Orders Generation Failed

Hello Admin,

The bulk work order generation job has failed.

---

## Error Details

**Error Message:**
{{ $errorMessage }}

**Schedule IDs:** {{ implode(', ', $scheduleIds) }}

**Number of Schedules:** {{ $scheduleCount }}

**Failed At:** {{ $failedAt }}

---

## Recommended Actions

1. Check the application logs for more details
2. Verify the maintenance schedules exist and are active
3. Ensure a technician is available
4. Retry the generation process

<x-mail::button :url="route('maintenance-schedules.index')">
Review Maintenance Schedules
</x-mail::button>

---

## Attachments

This email includes the following attachments:
- **error-trace.log** - Full error stack trace
- **failed-schedule-ids.txt** - List of schedule IDs that failed

---

This is an automated alert from your maintenance management system.

Regards,<br>
{{ config('app.name') }} System
</x-mail::message>
