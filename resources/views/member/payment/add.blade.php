@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">New Deposit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Deposit</li>
        <li class="breadcrumb-item active">New Deposit</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Deposit Form Card -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-wallet me-1"></i> Deposit Form
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Use the following form to automatically add funds to your account.</p>
                    <form method="POST" action="{{ route('member.payment.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="amount">Deposit Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="1" placeholder="Enter deposit amount (e.g. 10)" required value="{{ old('amount') }}">
                            </div>
                            <div class="form-text text-muted">Minimum deposit is $1.00 USD.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label d-block">Payment Method</label>
                            @if($paypalConfigured)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="radio-paypal" type="radio" name="payment_method" value="paypal" checked>
                                    <label class="form-check-label" for="radio-paypal">
                                        <i class="fab fa-paypal text-primary me-2"></i><strong>PayPal</strong> (Credit Card / USD Balance)
                                    </label>
                                </div>
                            @endif

                            @if($cryptomusConfigured)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="radio-cryptomus" type="radio" name="payment_method" value="cryptomus" {{ !$paypalConfigured ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radio-cryptomus">
                                        <i class="fab fa-btc text-warning me-2"></i><strong>Cryptomus</strong> (Crypto / USDT / BTC / LTC / etc)
                                    </label>
                                </div>
                            @endif

                            @if(!$paypalConfigured && !$cryptomusConfigured)
                                <div class="alert alert-warning py-2 mb-0">
                                    Payment methods have not been configured by the administrator. Please contact support.
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            @if($paypalConfigured || $cryptomusConfigured)
                                <button class="btn btn-primary" type="submit">Proceed to Payment</button>
                            @endif
                            <a href="{{ route('member.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Information & Guidelines
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Please read the guidelines below before making a payment.</p>
                    
                    <h6 class="fw-bold"><i class="fas fa-exclamation-circle text-primary me-2"></i> Important Notes:</h6>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item bg-transparent px-0 border-0 pb-1">
                            1. Deposits are processed automatically as soon as the transaction is validated by the payment gateway.
                        </li>
                        <li class="list-group-item bg-transparent px-0 border-0 pb-1">
                            2. <strong>PayPal:</strong> You can pay using credit/debit cards, PayPal balance, or available bank transfer options.
                        </li>
                        <li class="list-group-item bg-transparent px-0 border-0 pb-1">
                            3. <strong>Cryptomus:</strong> Cryptocurrency transactions are processed after receiving sufficient blockchain network confirmations. Ensure you send the exact amount instructed by Cryptomus.
                        </li>
                        <li class="list-group-item bg-transparent px-0 border-0 pb-1">
                            4. Please contact our support team via the <strong>Support Ticket</strong> menu if your balance is not updated within 10-15 minutes.
                        </li>
                    </ul>

                    <h6 class="fw-bold"><i class="fas fa-lock text-success me-2"></i> Secure Transactions:</h6>
                    <p class="text-muted small mb-0">
                        All transactions are processed securely using end-to-end encryption protocols provided directly by PayPal and Cryptomus. We do not store your credit card details or crypto wallet credentials.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
