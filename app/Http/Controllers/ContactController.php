<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactFormAdminMail;
use App\Mail\ContactFormAutoReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('static.contact'); 
    }

    public function submitForm(Request $request)
    {
        // 1. Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // 2. OPTION A: Store in database
        try {
            $contact = Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save contact: ' . $e->getMessage());
            // Continue anyway - don't let DB failure stop email sending
        }

        // 3. OPTION B: Send emails using Mailtrap 
        
        // Send email to YOU (admin notification)
        try {
            Mail::to('mthulisi.ndhlovu123@gmail.com') 
                ->send(new ContactFormAdminMail($validated));
        } catch (\Exception $e) {
            Log::error('Failed to send admin email: ' . $e->getMessage());
        }

        // Send auto-reply to the USER (confirmation)
        try {
            Mail::to($validated['email'])
                ->send(new ContactFormAutoReply($validated));
        } catch (\Exception $e) {
            Log::error('Failed to send auto-reply: ' . $e->getMessage());
        }

        // 4. Redirect with success message
        return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
}