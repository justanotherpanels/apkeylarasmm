@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Order Details</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">SMM</li>
                    <li class="breadcrumb-item"><a href="{{ route('member.smm.history') }}">Order History</a></li>
                    <li class="breadcrumb-item active">#{{ $order->invoice }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>SMM Order Details</h5>
                    <div>
                        @if($order->api && $order->sid)
                            @if(in_array(strtolower($order->status_order), ['success', 'finish', 'completed', 'complete']))
                                <button class="btn btn-info btn-sm" disabled><i class="fa fa-refresh"></i> Sync Status</button>
                            @else
                                <form method="POST" action="{{ route('member.smm.order.sync', $order->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-refresh"></i> Sync Status</button>
                                </form>
                            @endif
                        @endif
                        <a href="{{ route('member.smm.history') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Invoice No.</th>
                                    <td><strong class="text-primary">{{ $order->invoice }}</strong></td>
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
                                        @if($order->status_order == 'Success' || $order->status_order == 'Finish')
                                            <span class="badge badge-success">{{ $order->status_order }}</span>
                                        @elseif($order->status_order == 'Pending')
                                            <span class="badge badge-warning text-dark">{{ $order->status_order }}</span>
                                        @elseif($order->status_order == 'In Progres' || $order->status_order == 'Partial')
                                            <span class="badge badge-info">{{ $order->status_order }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $order->status_order }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Auto Refill</th>
                                    <td>
                                        @if($order->refill)
                                            <span class="badge badge-primary"><i class="fa fa-refresh"></i> Active</span>
                                        @else
                                            <span class="badge badge-light text-dark">Inactive</span>
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
