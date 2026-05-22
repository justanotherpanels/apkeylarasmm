@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Deposit History</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">History</li>
                    <li class="breadcrumb-item active">Deposit History</li>
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
        <!-- Zero Configuration Starts-->
        <div class="col-sm-12">
            <div class="card">
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deposits as $deposit)
                                    <tr>
                                        <td><strong>#{{ $deposit->invoice }}</strong></td>
                                        <td>{{ $deposit->create_at ? $deposit->create_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td>{{ $deposit->detail_transaction['payment_method'] ?? 'N/A' }}</td>
                                        <td>${{ number_format($deposit->amount, 2) }}</td>
                                        <td>USD</td>
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
                                        <td>
                                            <div class="d-flex gap-1 align-items-center">
                                                <a href="{{ route('member.payment.history.show', $deposit->invoice) }}" class="btn btn-primary btn-sm px-2 py-1" title="View Details"><i class="fa fa-eye"></i> Detail</a>
                                                @if($deposit->status_payment === 'Pending')
                                                    <a href="{{ route('member.payment.history.sync', $deposit->invoice) }}" class="btn btn-info btn-sm px-2 py-1" title="Sync Status"><i class="fa fa-refresh"></i></a>
                                                @else
                                                    <button class="btn btn-info btn-sm px-2 py-1" disabled title="Sync Status"><i class="fa fa-refresh"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid ends-->
@endsection
