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
                <h1 class="mb-3 fs-8 fs-md-9 fw-bold text-dark">Terms of Service</h1>
                <p class="mb-0 lead text-secondary">Please read these Terms of Service carefully before registering an account or placing an order at {{ $siteName }}.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                    <div class="card-body p-0">
                        <p class="text-muted mb-4">Last Updated: May 28, 2026</p>
                        
                        <div class="terms-content">
                            <h4 class="fw-bold text-dark mb-3 mt-4">1. General</h4>
                            <p class="text-secondary lh-lg mb-3">
                                By placing an order with <strong>{{ $siteName }}</strong>, you automatically accept all the terms of service listed below, whether you read them or not.
                            </p>
                            <p class="text-secondary lh-lg mb-3">
                                We reserve the right to change these Terms of Service without notice. You are expected to read all terms of service before placing any order to ensure you are up to date with any changes.
                            </p>
                            <p class="text-secondary lh-lg mb-4">
                                You will only use the {{ $siteName }} website in a manner which follows all agreements made with Instagram, Facebook, YouTube, Twitter, TikTok, and other social media networks on their individual Terms of Service pages.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">2. Service Disclaimer</h4>
                            <p class="text-secondary lh-lg mb-2">
                                {{ $siteName }} is designed to promote your social media profiles and boost your online presence. Please note:
                            </p>
                            <ul class="text-secondary lh-lg mb-4 ps-3">
                                <li>We do <strong>not</strong> guarantee that your new followers will interact with your posts or content. We only guarantee delivery of the paid followers, likes, views, or general social services.</li>
                                <li>We do <strong>not</strong> guarantee that 100% of our delivered accounts will have profile pictures, bios, and uploaded posts, although we make every effort to maintain high quality.</li>
                                <li>You shall not upload, link, or promote any material that contains nudity, hate speech, violence, or any content not accepted or suitable for general social media networks.</li>
                            </ul>

                            <h4 class="fw-bold text-dark mb-3 mt-4">3. Refund & Chargeback Policy</h4>
                            <p class="text-secondary lh-lg mb-3">
                                <strong>No refunds will be made to your payment method.</strong> Once a deposit has been completed, it cannot be reversed. You must use your loaded balance on orders from {{ $siteName }}.
                            </p>
                            <p class="text-secondary lh-lg mb-3">
                                You agree that once you complete a deposit, you will not file a dispute or a chargeback against us for any reason.
                            </p>
                            <p class="text-secondary lh-lg mb-4">
                                If you file a dispute or chargeback after a deposit, we reserve the right to terminate all future orders, ban your account, and remove any followers, likes, or views delivered to your or your client's accounts.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">4. Liability</h4>
                            <p class="text-secondary lh-lg mb-4">
                                {{ $siteName }} is in no way responsible or liable for any account suspension, shadowbans, or post/media deletions carried out by Instagram, Facebook, YouTube, TikTok, Twitter, or other social networks. Use of SMM services is at your own risk.
                            </p>

                            <h4 class="fw-bold text-dark mb-3 mt-4">5. Account Termination</h4>
                            <p class="text-secondary lh-lg mb-4">
                                Fraudulent activity, such as using unauthorized or stolen credit cards, raising false tickets, or attempt to manipulate database variables, will lead to immediate account termination and permanent IP banning without exception.
                            </p>

                            <hr class="my-5 opacity-10">

                            <div class="text-center">
                                <h5 class="fw-bold text-dark mb-2">Ready to Boost Your Profiles?</h5>
                                <p class="text-secondary mb-4">Create your account today and gain access to thousands of social media growth tools.</p>
                                <a href="{{ route('register') }}" class="btn btn-warning rounded-pill px-4 text-white fw-bold">Sign Up Now</a>
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
    .terms-content h4 {
        font-family: 'Poppins', sans-serif;
    }
    .terms-content p, .terms-content li {
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
    }
    .card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
</style>
@endpush
