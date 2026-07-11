<x-mail::message>
# New Contact Form Submission


**Name:** {{ $contact->name }}
**Email:** {{ $contact->email }}
**Subject:** {{ $contact->subject }}
**Message:** {{ $contact->message }}

---


<x-mail::button :url="route('contact.showForm', $contact)">
Contact Form
</x-mail::button>

---

Regards,<br>
{{ config('app.name') }}  The EMMS Team
</x-mail::message>
