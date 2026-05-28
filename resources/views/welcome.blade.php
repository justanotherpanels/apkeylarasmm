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
<section class="pt-7">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start text-center py-6">
                <h1 class="mb-4 fs-8 fs-md-9 fw-bold">#1 World's Cheapest and Best SMM Panel!</h1>
                <p class="mb-6 lead text-secondary">With {{ $siteName }}, you'll revolutionize your social media growth – together; we lead, they follow!</p>
                <div class="text-center text-md-start">
                    <a class="btn btn-warning me-3 btn-lg text-white fw-bold" href="{{ route('register') }}" role="button">Get Started</a>
                    <a class="btn btn-link text-warning fw-medium text-decoration-none" href="{{ route('services') }}" role="button">
                        <span class="fas fa-list me-2"></span>View Services
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <img class="pt-7 pt-md-0 img-fluid" src="{{ asset('themes/assets/img/hero/hero-img.png')}}" alt="SMM Growth" />
            </div>
        </div>
    </div>
</section>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5 pt-md-9 mb-6" id="feature">
    <div class="bg-holder z-index--1 bottom-0 d-none d-lg-block" style="background-image:url({{ asset('themes/assets/img/category/shape.png') }});opacity:.5;"></div>
    
    <div class="container">
        <h1 class="fs-9 fw-bold mb-4 text-center">Our SMM Channels</h1>
        <p class="text-secondary text-center mb-6">Boost your engagement and following on the world's most popular platforms.</p>
        
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-4">
                <img class="mb-3 ms-n3" src="{{ asset('themes/assets/img/category/icon1.png')}}" width="75" alt="Instagram Boost" />
                <h4 class="mb-3">Instagram Growth</h4>
                <p class="mb-0 fw-medium text-secondary">Get premium Instagram followers, likes, comments, and story views instantly.</p>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <img class="mb-3 ms-n3" src="{{ asset('themes/assets/img/category/icon2.png')}}" width="75" alt="TikTok Reach" />
                <h4 class="mb-3">TikTok Viral</h4>
                <p class="mb-0 fw-medium text-secondary">Enhance your TikTok authority with real followers, views, likes, and shares.</p>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <img class="mb-3 ms-n3" src="{{ asset('themes/assets/img/category/icon3.png')}}" width="75" alt="YouTube Views" />
                <h4 class="mb-3">YouTube Authority</h4>
                <p class="mb-0 fw-medium text-secondary">Accelerate watchtime hours, organic views, likes, and subscribers.</p>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <img class="mb-3 ms-n3" src="{{ asset('themes/assets/img/category/icon4.png')}}" width="75" alt="Facebook Reach" />
                <h4 class="mb-3">Facebook Boost</h4>
                <p class="mb-0 fw-medium text-secondary">Boost your Page likes, post engagement, views, and group followers.</p>
            </div>
        </div>
        <div class="text-center"><a class="btn btn-warning text-white fw-bold" href="{{ route('register') }}" role="button">SIGN UP NOW</a></div>
    </div>
</section>
<!-- <section> close ============================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5" id="validation">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h5 class="text-warning fw-bold mb-3">Best & Cheapest Services</h5>
                <h2 class="mb-4 fs-7 fs-md-8 fw-bold text-dark">Designed for Online Industry Leaders</h2>
                <p class="mb-4 fw-medium text-secondary lh-lg">
                    The online world is run by industry leaders who know exactly what their customers want, and what to offer them in return. Here, we provide the best and most affordable SMM panel services to those leaders just like you, and we assure you that you’ll be the one who generates the most website traffic to your business online or your social media accounts after you use the best SMM panel.
                </p>
                <div class="mt-4">
                    <a href="{{ route('services') }}" class="btn btn-outline-warning rounded-pill px-4 fw-bold">Check Our Rates</a>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                <img class="img-fluid" src="{{ asset('themes/assets/img/validation/validation.png')}}" alt="Validation" />
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5" id="manager">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img class="img-fluid" src="{{ asset('themes/assets/img/manager/manager.png')}}" alt="Compatibility" />
            </div>
            <div class="col-lg-6">
                <h5 class="text-warning fw-bold mb-3">Fully Responsive Panel</h5>
                <p class="fs-7 fs-md-8 fw-bold mb-3 text-dark">Compatible with all devices!</p>
                <p class="mb-4 fw-medium text-secondary lh-lg">
                    Best SMM is fully compatible with all desktops and devices, allowing you to perform all your transactions instantly from anywhere.
                </p>
                <div class="d-flex align-items-center mb-3">
                    <img class="me-sm-4 me-2" src="{{ asset('themes/assets/img/manager/tick.png')}}" width="35" alt="tick" />
                    <p class="fw-medium mb-0 text-secondary">Track orders live on desktop, mobile, or tablet devices.</p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <img class="me-sm-4 me-2" src="{{ asset('themes/assets/img/manager/tick.png')}}" width="35" alt="tick" />
                    <p class="fw-medium mb-0 text-secondary">Easy automated APIs designed for resellers and developers.</p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <img class="me-sm-4 me-2" src="{{ asset('themes/assets/img/manager/tick.png')}}" width="35" alt="tick" />
                    <p class="fw-medium mb-0 text-secondary">Safe, secure, and encrypted payment gateways and wallets.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5 pb-5" id="marketer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h5 class="text-warning fw-bold mb-3">Automated Growth API</h5>
                <p class="mb-3 fs-7 fs-md-8 fw-bold text-dark">Reseller-Ready Integration</p>
                <p class="mb-4 fw-medium text-secondary lh-lg">
                    Are you an agency or an independent SMM reseller? Our developer-friendly API allows you to integrate our wholesale rates directly with your website, letting you automate sales and scale your operations without manual work.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-warning text-white fw-bold px-4 rounded-pill">Get API Key</a>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                <img class="img-fluid" src="{{ asset('themes/assets/img/marketer/marketer.png')}}" alt="Developer API" />
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="py-md-11 py-8" id="superhero">
    <div class="bg-holder z-index--1 bottom-0 d-none d-lg-block background-position-top" style="background-image:url({{ asset('themes/assets/img/superhero/oval.png') }});opacity:.5; background-position: top !important ;"></div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <h1 class="fw-bold mb-4 fs-7 fs-md-8 text-dark">Ready to Revolutionize Your Growth?</h1>
                <p class="mb-5 text-secondary fw-medium lh-lg">Join thousands of businesses and influencers who are already using our SMM panel services to scale their authority, build real social proof, and drive web traffic.</p>
                <a href="{{ route('register') }}" class="btn btn-warning btn-lg text-white fw-bold rounded-pill px-5">Join {{ $siteName }} Now</a>
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5 pb-7" id="marketing">
    <div class="container">
        <h1 class="fw-bold fs-6 fs-md-7 text-dark text-center mb-3">Social Media Strategies</h1>
        <p class="text-secondary text-center mb-6">Read our latest advice on growing your channels and optimizing SMM orders.</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img class="card-img-top rounded-top-3" src="{{ asset('themes/assets/img/marketing/marketing01.png')}}" alt="Strategy 1" />
                    <div class="card-body p-4">
                        <p class="text-secondary text-sm">By {{ $siteName }} Team | <span class="ms-1">May 2026</span></p>
                        <h5 class="fw-bold text-dark mt-2">Increasing Engagement With Social Proof</h5>
                        <p class="text-secondary text-sm mt-3 lh-relaxed">Understand how likes and shares drive organic algorithm updates and bring real visitors to your page.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img class="card-img-top rounded-top-3" src="{{ asset('themes/assets/img/marketing/marketing02.png')}}" alt="Strategy 2" />
                    <div class="card-body p-4">
                        <p class="text-secondary text-sm">By {{ $siteName }} Team | <span class="ms-1">May 2026</span></p>
                        <h5 class="fw-bold text-dark mt-2">How to Go Viral on TikTok in 2026</h5>
                        <p class="text-secondary text-sm mt-3 lh-relaxed">Discover the key algorithm updates for TikTok and how view counts affect search recommendations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img class="card-img-top rounded-top-3" src="{{ asset('themes/assets/img/marketing/marketing03.png')}}" alt="Strategy 3" />
                    <div class="card-body p-4">
                        <p class="text-secondary text-sm">By {{ $siteName }} Team | <span class="ms-1">May 2026</span></p>
                        <h5 class="fw-bold text-dark mt-2">Automated Reselling via SMM APIs</h5>
                        <p class="text-secondary text-sm mt-3 lh-relaxed">Learn step-by-step how to set up an SMM website and use automated API providers to fulfil orders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->
@endsection