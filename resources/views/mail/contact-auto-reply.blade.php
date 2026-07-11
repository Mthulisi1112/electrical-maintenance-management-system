<x-mail::message>
# Thank You for Contacting EMMS.

---

Thank You for Contacting Us!

Dear {{ $contact->name }}

Thank you for reaching out to the Electrical Maintenance Management System (EMMS) team. We have received your message and will get back to you as soon as possible.


---
**Your message:**

**Subject:** {{ $contact->subject }}
**Message:** {{ $contact->message }}


If you have any urgent concerns, please contact us directly at support@emms.com or call us at +27 (82) 082 2083 .


<x-mail::button :url="route('maintenance-schedules.index')">
Contact-auto-reply
</x-mail::button>

---


Regards,<br>
{{ config('app.name') }}  The EMMS Team
</x-mail::message>
