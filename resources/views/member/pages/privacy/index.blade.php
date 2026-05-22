@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Privacy Policy</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Privacy</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Our Commitment to Privacy</h5>
                    <span>Learn how we handle, collect, and protect your personal and order data.</span>
                </div>
                <div class="card-body">
                    <h6>1. Information We Collect</h6>
                    <p class="text-muted">We collect information that you provide directly to us when registering an account, making a payment, or submitting an order (such as your email, username, and social media target links).</p>

                    <h6 class="mt-4">2. How We Use Your Data</h6>
                    <p class="text-muted">We use your information solely to process SMM orders, manage payment billing, and provide customer support tickets. We never sell your personal information to third parties.</p>

                    <h6 class="mt-4">3. Security Measures</h6>
                    <p class="text-muted">We implement strong industry-standard security protocols to protect your personal information and transaction history against unauthorized access, loss, or misuse.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
