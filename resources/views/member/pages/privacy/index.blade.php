@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Privacy Policy</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Privacy</li>
    </ol>

    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-shield-alt me-1"></i> Our Commitment to Privacy
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Learn how we handle, collect, and protect your personal and order data.</p>
                    
                    <h6 class="fw-bold"><i class="fas fa-user-shield text-primary me-2"></i> 1. Information We Collect</h6>
                    <p class="text-muted ms-4">We collect information that you provide directly to us when registering an account, making a payment, or submitting an order (such as your email, username, and social media target links).</p>

                    <h6 class="fw-bold mt-4"><i class="fas fa-database text-primary me-2"></i> 2. How We Use Your Data</h6>
                    <p class="text-muted ms-4">We use your information solely to process SMM orders, manage payment billing, and provide customer support tickets. We never sell your personal information to third parties.</p>

                    <h6 class="fw-bold mt-4"><i class="fas fa-lock text-success me-2"></i> 3. Security Measures</h6>
                    <p class="text-muted ms-4">We implement strong industry-standard security protocols to protect your personal information and transaction history against unauthorized access, loss, or misuse.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
