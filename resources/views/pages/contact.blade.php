@php
    try {
        $setting = \App\Models\Setting::first();
    } catch (\Throwable $e) {
        $setting = null;
    }
    $siteName = $setting && $setting->site_name ? $setting->site_name : config('app.name', 'Apkey SMM');
    $email = $setting && $setting->email ? $setting->email : 'support@example.com';
    
    $whatsappUrl = null;
    if ($setting && $setting->whatsapp_url) {
        $whatsappUrl = trim($setting->whatsapp_url);
        // If it's a numeric string (phone number), convert it to a wa.me link
        if (preg_match('/^[0-9]+$/', $whatsappUrl)) {
            if (strpos($whatsappUrl, '0') === 0) {
                $whatsappUrl = '62' . substr($whatsappUrl, 1);
            }
            $whatsappUrl = 'https://wa.me/' . $whatsappUrl;
        }
    }
    
    $facebookUrl = $setting && $setting->facebook_url ? $setting->facebook_url : null;
    $instagramUrl = $setting && $setting->instagram_url ? $setting->instagram_url : null;
@endphp

@extends('layouts.home.master')

@section('content')
<section class="pt-8 pb-6 bg-white">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h1 class="mb-3 fs-8 fs-md-9 fw-bold text-dark">Contact Us</h1>
                <p class="mb-0 lead text-secondary">Have questions or need assistance? We are here to support you 24/7.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4 justify-content-center">
                    
                    <!-- Left: Contact Details -->
                    <div class="col-md-5">
                        <div class="card border-0 shadow-lg rounded-4 p-4 h-100">
                            <h4 class="fw-bold text-dark mb-4">Support Channels</h4>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-warning text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-envelope fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Email Support</h6>
                                    <p class="mb-0 text-sm"><a href="mailto:{{ $email }}" class="text-secondary text-decoration-none">{{ $email }}</a></p>
                                </div>
                            </div>
                            
                            @if($whatsappUrl)
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-success text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fab fa-whatsapp fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">WhatsApp Live Chat</h6>
                                    <p class="mb-0 text-sm">
                                        <a href="{{ $whatsappUrl }}" target="_blank" class="text-success fw-bold text-decoration-none">
                                            Chat with Us <i class="fas fa-external-link-alt ms-1 text-xs"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-ticket-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Member Tickets</h6>
                                    <p class="text-secondary text-sm mb-0">Registered members can open a technical dispute ticket directly from the member portal dashboard.</p>
                                </div>
                            </div>
                            
                            @if($instagramUrl || $facebookUrl)
                            <div class="border-top pt-4 mt-2">
                                <h6 class="fw-bold text-dark mb-3">Follow Us</h6>
                                <div class="d-flex gap-3">
                                    @if($facebookUrl)
                                        <a href="{{ $facebookUrl }}" target="_blank" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; padding: 0;">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    @endif
                                    @if($instagramUrl)
                                        <a href="{{ $instagramUrl }}" target="_blank" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; padding: 0;">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Info Panel / CTA -->
                    <div class="col-md-7">
                        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-warning text-white h-100 d-flex flex-column justify-content-center text-center">
                            <h3 class="fw-bold text-white mb-3">Need Immediate Service?</h3>
                            <p class="mb-5 lh-lg opacity-90">
                                Registration at {{ $siteName }} is 100% free. Once registered, you will gain access to our live client portal, enabling you to deposit funds, order SMM growth packages, track order completion, and submit prioritized support tickets.
                            </p>
                            <div>
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 text-warning fw-bold shadow-sm">
                                    Create Free Account
                                </a>
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
    h1, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
    }
    p, a {
        font-family: 'Poppins', sans-serif;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
</style>
@endpush
