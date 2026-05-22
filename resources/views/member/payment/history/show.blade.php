@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Deposit Details</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('member.payment.history') }}">Deposit History</a></li>
                    <li class="breadcrumb-item active">Invoice #{{ $deposit->invoice }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Main details -->
        <div class="col-sm-12 col-xl-7">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Invoice #{{ $deposit->invoice }}</h5>
                    <span>Detailed breakdown of this deposit transaction.</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong>Invoice ID</strong></td>
                                    <td><span class="text-primary font-weight-bold">#{{ $deposit->invoice }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>User Account</strong></td>
                                    <td>{{ auth()->user()->username }} ({{ auth()->user()->email }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount</strong></td>
                                    <td><h4 class="text-success m-0">${{ number_format($deposit->amount, 2) }} USD</h4></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method</strong></td>
                                    <td>{{ $deposit->detail_transaction['payment_method'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date</strong></td>
                                    <td>{{ $deposit->create_at ? $deposit->create_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated</strong></td>
                                    <td>{{ $deposit->update_at ? $deposit->update_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if($deposit->status_payment === 'Success')
                                            <span class="badge badge-success">Success</span>
                                        @elseif($deposit->status_payment === 'Pending')
                                            <span class="badge badge-warning text-dark">Pending</span>
                                        @elseif($deposit->status_payment === 'Cancel')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @else
                                            <span class="badge badge-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('member.payment.history') }}" class="btn btn-secondary"><i class="fa fa-arrow-left me-2"></i>Back to History</a>
                </div>
            </div>
        </div>

        <!-- Technical Transaction Metadata details -->
        <div class="col-sm-12 col-xl-5">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Gateway Log</h5>
                    <span>Verification data from the payment provider.</span>
                </div>
                <div class="card-body">
                    @php
                        $method = strtolower($deposit->detail_transaction['payment_method'] ?? '');
                        $details = $deposit->detail_transaction;
                    @endphp

                    @if($method === 'paypal')
                        <h6><i class="fa fa-paypal text-primary me-2"></i> PayPal Details</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent px-0">
                                <strong>PayPal Order ID:</strong> <br>
                                <code class="text-dark">{{ $details['paypal_order_id'] ?? 'N/A' }}</code>
                            </li>
                            @if(isset($details['capture_details']))
                                @php $cap = $details['capture_details']; @endphp
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Payer Email:</strong> <br>
                                    <span>{{ $cap['payer']['email_address'] ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Capture ID:</strong> <br>
                                    <code>{{ $cap['purchase_units'][0]['payments']['captures'][0]['id'] ?? 'N/A' }}</code>
                                </li>
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Status:</strong> <br>
                                    <span class="text-uppercase">{{ $cap['status'] ?? 'N/A' }}</span>
                                </li>
                            @endif
                        </ul>

                    @elseif($method === 'cryptomus')
                        <h6><i class="fa fa-bitcoin text-warning me-2"></i> Cryptomus Details</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent px-0">
                                <strong>Cryptomus UUID:</strong> <br>
                                <code class="text-dark">{{ $details['cryptomus_uuid'] ?? 'N/A' }}</code>
                            </li>
                            @if(isset($details['callback_details']))
                                @php $cb = $details['callback_details']; @endphp
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Cryptocurrency:</strong> <br>
                                    <span class="text-uppercase">{{ $cb['payer_currency'] ?? ($cb['currency'] ?? 'N/A') }}</span>
                                </li>
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Amount Received:</strong> <br>
                                    <span>{{ $cb['payer_amount'] ?? 'N/A' }}</span>
                                </li>
                                @if(isset($cb['txid']))
                                    <li class="list-group-item bg-transparent px-0">
                                        <strong>Blockchain TxID:</strong> <br>
                                        <code class="text-break">{{ $cb['txid'] }}</code>
                                    </li>
                                @endif
                                <li class="list-group-item bg-transparent px-0">
                                    <strong>Status:</strong> <br>
                                    <span class="text-uppercase">{{ $cb['status'] ?? 'N/A' }}</span>
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="text-muted">No external gateway transaction details available for this payment method.</p>
                    @endif

                    @if($deposit->status_payment === 'Pending')
                        <div class="alert alert-info mt-3 p-3">
                            <i class="fa fa-info-circle me-2"></i>
                            This payment is still pending. If you completed the checkout, your balance will update automatically in a few moments.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
