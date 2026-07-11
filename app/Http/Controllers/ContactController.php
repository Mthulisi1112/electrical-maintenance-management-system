<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessContactForm;
use App\Models\Contact;
use Illuminate\Http\Request;
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

        // 2. Store in database
        try {
            $contact = Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ]);

        ProcessContactForm::dispatch($contact);

        
        return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Failed to save contact: ' . $e->getMessage());
          
        }

    }
}