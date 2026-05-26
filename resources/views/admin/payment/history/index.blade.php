@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Deposit History</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">Payment</li>
                        <li class="breadcrumb-item active">Deposit History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                @if (session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Deposit History</h5>
                            <span>All deposit transactions from users.</span>
                        </div>
                        <form method="POST" action="{{ route('admin.payment.history.sync-all') }}">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to sync all pending deposits?');">
                                <i class="fa fa-refresh"></i> Sync All Pending
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
                                    <tr>
                                        <td><strong>#{{ $deposit->invoice }}</strong></td>
                                        <td>
                                            {{ $deposit->user->full_name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $deposit->user->username ?? '' }}</small>
                                        </td>
                                        <td>${{ number_format($deposit->amount, 2) }}</td>
                                        <td>{{ $deposit->detail_transaction['payment_method'] ?? 'N/A' }}</td>
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
                                        <td>{{ $deposit->create_at ? \Carbon\Carbon::parse($deposit->create_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Zero Configuration  Ends-->
        </div>
    </div>
</div>
@endsection
