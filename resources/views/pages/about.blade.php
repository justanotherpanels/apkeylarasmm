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
                <h1 class="mb-3 fs-8 fs-md-9 fw-bold text-dark">About Us</h1>
                <p class="mb-0 lead text-secondary">Discover who we are and why {{ $siteName }} is the number one choice for social media growth.</p>
            </div>
        </div>

        <div class="row justify-content-center align-items-center mb-6">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h3 class="fw-bold text-dark mb-4">Empowering Your Online Presence</h3>
                <p class="text-secondary lh-lg mb-3">
                    <strong>{{ $siteName }}</strong> is a leading global Social Media Marketing (SMM) service provider. We offer high-quality automated growth services including followers, likes, views, comments, and subscribers across major platforms like Instagram, TikTok, Facebook, YouTube, and Telegram.
                </p>
                <p class="text-secondary lh-lg">
                    Whether you are an influencer, a local startup, or a corporate marketing agency, our automated platform delivers instant, reliable social proof solutions to expand your brand's reach and authority in no time.
                </p>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-warning text-white">
                    <h4 class="fw-bold text-white mb-3">Our Mission</h4>
                    <p class="mb-0 lh-lg opacity-90">
                        Our ultimate mission is to supply scalable, budget-friendly growth solutions for creators and brands worldwide. We simplify the social media scaling process so you can focus on creating high-quality content while we handle the distribution.
                    </p>
                </div>
            </div>
        </div>

        <div class="row text-center mt-5 mb-5 g-4">
            <div class="col-6 col-md-3">
                <div class="p-3 border-bottom border-warning border-3 rounded shadow-sm">
                    <h2 class="fw-bold text-dark mb-1">5M+</h2>
                    <p class="text-secondary mb-0 text-sm">Completed Orders</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-bottom border-warning border-3 rounded shadow-sm">
                    <h2 class="fw-bold text-dark mb-1">100K+</h2>
                    <p class="text-secondary mb-0 text-sm">Active Members</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-bottom border-warning border-3 rounded shadow-sm">
                    <h2 class="fw-bold text-dark mb-1">99.9%</h2>
                    <p class="text-secondary mb-0 text-sm">Success Rate</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-bottom border-warning border-3 rounded shadow-sm">
                    <h2 class="fw-bold text-dark mb-1">24/7</h2>
                    <p class="text-secondary mb-0 text-sm">Support Active</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center text-center mt-6">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                    <h4 class="fw-bold text-dark mb-3">Why Choose {{ $siteName }}?</h4>
                    <div class="row text-start mt-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-dark"><i class="fas fa-bolt text-warning me-2"></i> Instant Automation</h6>
                            <p class="text-secondary text-sm">Orders are pushed instantly through APIs to ensure rapid, automated delivery.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-dark"><i class="fas fa-tags text-warning me-2"></i> Wholesale Prices</h6>
                            <p class="text-secondary text-sm">Our API connections fetch the lowest wholesale rates in the market for our users.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-dark"><i class="fas fa-lock text-warning me-2"></i> Encrypted Safety</h6>
                            <p class="text-secondary text-sm">Your security is our top priority. We do not require account passwords to deliver actions.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-dark"><i class="fas fa-headset text-warning me-2"></i> Professional Support</h6>
                            <p class="text-secondary text-sm">Our technical support ticket system is staffed round the clock to resolve issues.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-warning rounded-pill px-4 text-white fw-bold">Join Us Today</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .text-sm {
        font-size: 0.875rem;
    }
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
    }
    p, li {
        font-family: 'Poppins', sans-serif;
    }
</style>
@endpush
