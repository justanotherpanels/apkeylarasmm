@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Order Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item"><a href="{{ route('member.smm.history') }}">Order History</a></li>
        <li class="breadcrumb-item active">#{{ $order->invoice }}</li>
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
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-shopping-bag me-1"></i> SMM Order Details
                    </div>
                    <div>
                        @if($order->api && $order->sid)
                            @if(in_array(strtolower($order->status_order), ['success', 'finish', 'completed', 'complete']))
                                <button class="btn btn-info btn-sm text-dark" disabled><i class="fas fa-sync-alt"></i> Sync Status</button>
                            @else
                                <form method="POST" action="{{ route('member.smm.order.sync', $order->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm text-dark"><i class="fas fa-sync-alt"></i> Sync Status</button>
                                </form>
                            @endif
                        @endif
                        <a href="{{ route('member.smm.history') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Invoice No.</th>
                                    <td><strong class="text-primary">#{{ $order->invoice }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Service Category</th>
                                    <td>{{ $order->service->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Service Name</th>
                                    <td>{{ $order->service->name_service ?? 'N/A' }} (ID: {{ $order->id_service_smm }})</td>
                                </tr>
                                <tr>
                                    <th>Target / Link</th>
                                    <td>
                                        <a href="{{ $order->target }}" target="_blank" class="text-break">{{ $order->target }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td>{{ number_format($order->amount) }}</td>
                                </tr>
                                <tr>
                                    <th>Price / Cost</th>
                                    <td>${{ number_format($order->price_sale, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Start Count</th>
                                    <td>{{ number_format($order->start_count) }}</td>
                                </tr>
                                <tr>
                                    <th>Remains</th>
                                    <td>{{ number_format($order->remains) }}</td>
                                </tr>
                                <tr>
                                    <th>Order Status</th>
                                    <td>
                                        @php
                                            $status = strtolower($order->status_order);
                                            $badgeClass = 'bg-primary';
                                            if ($status === 'success' || $status === 'completed' || $status === 'finish') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($status === 'pending') {
                                                $badgeClass = 'bg-warning text-dark';
                                            } elseif ($status === 'cancel' || $status === 'canceled' || $status === 'error') {
                                                $badgeClass = 'bg-danger';
                                            } elseif ($status === 'processing' || $status === 'in progres' || $status === 'partial') {
                                                $badgeClass = 'bg-info text-dark';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $order->status_order }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Auto Refill</th>
                                    <td>
                                        @if($order->refill)
                                            <span class="badge bg-primary"><i class="fas fa-sync-alt"></i> Active</span>
                                        @else
                                            <span class="badge bg-light text-dark">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->create_at ? $order->create_at->format('d F Y - H:i') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
