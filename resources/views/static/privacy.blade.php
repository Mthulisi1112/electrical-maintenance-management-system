@extends('layouts.app')

@section('title', 'Privacy Policy - EMMS')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-700">
                <h1 class="text-2xl font-bold text-white">Privacy Policy</h1>
                <p class="text-sm text-blue-100 mt-1">Last updated: {{ now()->format('F j, Y') }}</p>
            </div>
            
            <div class="p-8 prose prose-sm max-w-none">
                <h2>1. Information We Collect</h2>
                <p>We collect information you provide directly to us, such as when you create an account, update your profile, or use our services. This may include:</p>
                <ul>
                    <li>Name, email address, and contact information</li>
                    <li>Employee ID and department</li>
                    <li>Account login credentials</li>
                    <li>Any other information you choose to provide</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process and complete transactions</li>
                    <li>Send you technical notices, updates, and support messages</li>
                    <li>Respond to your comments and questions</li>
                    <li>Monitor and analyze trends, usage, and activities</li>
                </ul>

                <h2>3. Sharing of Information</h2>
                <p>We do not share your personal information with third parties except as described in this policy or with your consent. We may share information:</p>
                <ul>
                    <li>With vendors, consultants, and other service providers who need access to such information to carry out work on our behalf</li>
                    <li>In response to a request for information if we believe disclosure is in accordance with, or required by, any applicable law or legal process</li>
                    <li>If we believe your actions are inconsistent with our user agreements or policies, or to protect the rights, property, and safety of our company or others</li>
                </ul>

                <h2>4. Data Security</h2>
                <p>We take reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction.</p>

                <h2>5. Changes to this Policy</h2>
                <p>We may change this privacy policy from time to time. If we make changes, we will notify you by revising the date at the top of the policy and, in some cases, we may provide you with additional notice.</p>

                <h2>6. Contact Us</h2>
                <p>If you have any questions about this privacy policy, please contact us at <a href="mailto:privacy@emms.com">privacy@emms.com</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection