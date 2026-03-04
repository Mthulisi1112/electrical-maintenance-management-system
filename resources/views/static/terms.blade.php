@extends('layouts.app')

@section('title', 'Terms of Service - EMMS')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-700">
                <h1 class="text-2xl font-bold text-white">Terms of Service</h1>
                <p class="text-sm text-blue-100 mt-1">Last updated: {{ now()->format('F j, Y') }}</p>
            </div>
            
            <div class="p-8 prose prose-sm max-w-none">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using the Electrical Maintenance Management System (EMMS), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>

                <h2>2. Description of Service</h2>
                <p>EMMS provides electrical maintenance management tools including asset tracking, work order management, fault reporting, and preventive maintenance scheduling for industrial environments.</p>

                <h2>3. User Accounts</h2>
                <p>To use certain features of our service, you must create an account. You are responsible for maintaining the security of your account and for all activities that occur under your account.</p>

                <h2>4. User Responsibilities</h2>
                <p>You agree to:</p>
                <ul>
                    <li>Provide accurate and complete information when creating your account</li>
                    <li>Keep your login credentials confidential</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                    <li>Use the service in compliance with all applicable laws and regulations</li>
                </ul>

                <h2>5. Acceptable Use</h2>
                <p>You may not use the service to:</p>
                <ul>
                    <li>Violate any laws or regulations</li>
                    <li>Infringe upon the rights of others</li>
                    <li>Distribute malware or harmful code</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Interfere with or disrupt the service</li>
                </ul>

                <h2>6. Intellectual Property</h2>
                <p>The service and its original content, features, and functionality are owned by EMMS and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>

                <h2>7. Termination</h2>
                <p>We may terminate or suspend your account and bar access to the service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever, including without limitation if you breach the Terms.</p>

                <h2>8. Limitation of Liability</h2>
                <p>In no event shall EMMS be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your use of the service.</p>

                <h2>9. Changes to Terms</h2>
                <p>We reserve the right to modify or replace these terms at any time. If a revision is material, we will try to provide at least 30 days' notice prior to any new terms taking effect.</p>

                <h2>10. Contact Us</h2>
                <p>If you have any questions about these Terms, please contact us at <a href="mailto:legal@emms.com">legal@emms.com</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection