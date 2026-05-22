@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>New Deposit</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">Deposit</li>
                    <li class="breadcrumb-item active">New Deposit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Container-fluid starts-->
<div class="container-fluid">
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
        <div class="col-sm-12 col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Deposit Form</h5>
                    <span>Use the following form to automatically add funds to your account.</span>
                </div>
                <form class="theme-form" method="POST" action="{{ route('member.payment.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="amount">Deposit Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="1" placeholder="Enter deposit amount (e.g. 10)" required value="{{ old('amount') }}">
                            </div>
                            <small class="form-text text-muted">Minimum deposit is $1.00 USD.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="col-form-label">Payment Method</label>
                            <div class="col-sm-9">
                                @if($paypalConfigured)
                                    <div class="form-check radio radio-primary mb-2">
                                        <input class="form-check-input" id="radio-paypal" type="radio" name="payment_method" value="paypal" checked>
                                        <label class="form-check-label" for="radio-paypal">
                                            <i class="fa fa-paypal text-primary me-2"></i><strong>PayPal</strong> (Credit Card / USD Balance)
                                        </label>
                                    </div>
                                @endif

                                @if($cryptomusConfigured)
                                    <div class="form-check radio radio-primary">
                                        <input class="form-check-input" id="radio-cryptomus" type="radio" name="payment_method" value="cryptomus" {{ !$paypalConfigured ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radio-cryptomus">
                                            <i class="fa fa-bitcoin text-warning me-2"></i><strong>Cryptomus</strong> (Crypto / USDT / BTC / LTC / etc)
                                        </label>
                                    </div>
                                @endif

                                @if(!$paypalConfigured && !$cryptomusConfigured)
                                    <div class="alert alert-warning">
                                        Payment methods have not been configured by the administrator. Please contact support.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if($paypalConfigured || $cryptomusConfigured)
                            <button class="btn btn-primary" type="submit">Proceed to Payment</button>
                        @endif
                        <a href="{{ route('member.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="col-sm-12 col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Information & Guidelines</h5>
                    <span>Please read the guidelines below before making a payment.</span>
                </div>
                <div class="card-body">
                    <h6><i class="fa fa-info-circle text-primary me-2"></i> Important Notes:</h6>
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

                    <h6><i class="fa fa-lock text-success me-2"></i> Secure Transactions:</h6>
                    <p class="text-muted">
                        All transactions are processed securely using end-to-end encryption protocols provided directly by PayPal and Cryptomus. We do not store your credit card details or crypto wallet credentials.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
