@php
    try {
        $setting = \App\Models\Setting::first();
    } catch (\Throwable $e) {
        $setting = null;
    }
    $siteName = $setting && $setting->site_name ? $setting->site_name : config('app.name', 'Apkey SMM');
@endphp

@extends('layouts.home.master')

@section('content')
<section class="pt-8 pb-6 bg-white">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h1 class="mb-3 fs-8 fs-md-9 fw-bold text-dark">Privacy Policy</h1>
                <p class="mb-0 lead text-secondary">Your privacy is important to us. Learn how we handle and protect your personal information at {{ $siteName }}.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                    <div class="card-body p-0">
                        <p class="text-muted mb-4">Last Updated: May 28, 2026</p>
                        
                        <div class="privacy-content">
                            <h4 class="fw-bold text-dark mb-3 mt-4">1. Introduction</h4>
                            <p class="text-secondary lh-lg mb-4">
                                Welcome to <strong>{{ $siteName }}</strong>. We are committed to protecting your personal data and respecting your privacy. This Privacy Policy outlines how we collect, use, process, and share your personal information when you use our SMM panel, register an account, make payments, and order services.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">2. Information We Collect</h4>
                            <p class="text-secondary lh-lg mb-2">
                                When you register and use the services of {{ $siteName }}, we collect several types of information:
                            </p>
                            <ul class="text-secondary lh-lg mb-4 ps-3">
                                <li><strong>Account Details:</strong> Your full name, email address, password, username, and contact details (including WhatsApp number or Telegram username).</li>
                                <li><strong>Transaction Information:</strong> Information about payments you make on our platform, such as deposit records, invoice references, and gateway logs. Note that we do not store full credit card or password credentials on our local servers. All payment flows are processed securely via encrypted API checkouts.</li>
                                <li><strong>Order Details:</strong> Social media links, profile URLs, usernames, or channels that you input when ordering SMM services (likes, followers, views, comments, etc.).</li>
                                <li><strong>Usage and Log Data:</strong> Technical logs including your IP address, browser type, device information, operating system, and pages visited on our website.</li>
                            </ul>

                            <h4 class="fw-bold text-dark mb-3 mt-4">3. How We Use Your Information</h4>
                            <p class="text-secondary lh-lg mb-2">
                                We utilize the collected information for the following business and operational purposes:
                            </p>
                            <ul class="text-secondary lh-lg mb-4 ps-3">
                                <li>To create and manage your membership account.</li>
                                <li>To execute SMM service orders and monitor delivery status.</li>
                                <li>To verify and process billing transactions and deposits.</li>
                                <li>To send transactional updates, critical alerts, and support details via Email or WhatsApp.</li>
                                <li>To prevent fraudulent activities, secure our website, and troubleshoot technical glitches.</li>
                                <li>To offer customer support, solve tickets, and respond to inquiries.</li>
                            </ul>

                            <h4 class="fw-bold text-dark mb-3 mt-4">4. Sharing Data with SMM Providers</h4>
                            <p class="text-secondary lh-lg mb-4">
                                To fulfill SMM orders (such as social media interactions), we communicate order details (e.g., target URL/link and order quantity) to third-party SMM providers. We do <strong>NOT</strong> share your personal account information, billing details, password, or contact information with these providers. They only receive the public links necessary to deliver the purchased social media package.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">5. Cookies and Tracking</h4>
                            <p class="text-secondary lh-lg mb-4">
                                {{ $siteName }} uses cookies and standard browser tracking mechanisms (including tools like local storage and analytics cookies) to improve user experiences, remember authentication states, and analyze user interactions on our dashboard. You can opt to turn off cookies in your browser settings, though doing so might disable certain essential functionalities of our dashboard.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">6. Data Security and Retention</h4>
                            <p class="text-secondary lh-lg mb-4">
                                We enforce strong technical, physical, and administrative security measures to prevent unauthorized access, alteration, disclosure, or destruction of your personal details. We retain your information as long as your account remains active or as required by law to comply with billing audits, taxation laws, and platform dispute resolutions.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">7. Your Choices and Rights</h4>
                            <p class="text-secondary lh-lg mb-4">
                                You have the right to view, correct, or update your personal information by editing your profile settings. If you wish to permanently delete your account and clear all transaction logs, you may submit a request by creating a support ticket in our dashboard. We will evaluate and process your request in accordance with our legal obligations.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">8. Policy Updates</h4>
                            <p class="text-secondary lh-lg mb-4">
                                We reserve the right to edit or modify this Privacy Policy at any time. When updates occur, we will post the revised policy on this page and revise the "Last Updated" date at the top. We encourage you to review this policy periodically to stay informed about how we protect your personal information.
                            </p>

                            <hr class="my-5 opacity-10">

                            <div class="text-center">
                                <h5 class="fw-bold text-dark mb-2">Got Questions?</h5>
                                <p class="text-secondary mb-4">If you have any questions or concerns regarding our privacy practices, please register/log in and submit a Support Ticket.</p>
                                <a href="{{ route('login') }}" class="btn btn-warning rounded-pill px-4 text-white fw-bold">Contact Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .privacy-content h4 {
        font-family: 'Poppins', sans-serif;
    }
    .privacy-content p, .privacy-content li {
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
    }
    .card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
</style>
@endpush
