@extends('layouts.member.master')


@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Order History</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">SMM</li>
                    <li class="breadcrumb-item active">Order History</li>
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
                <div class="card-header">
                    <h5>SMM Orders</h5>
                    <span>All SMM orders placed through your account, fetched directly from database.</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Service</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Start Count</th>
                                    <th>Remains</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->invoice }}</strong></td>
                                    <td>{{ $order->service->name_service ?? 'N/A' }}</td>
                                    <td>{{ number_format($order->amount) }}</td>
                                    <td>${{ number_format($order->price_sale, 2) }}</td>
                                    <td>{{ number_format($order->start_count) }}</td>
                                    <td>{{ number_format($order->remains) }}</td>
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
                                    <td>{{ $order->create_at ? $order->create_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 align-items-center">
                                            <a href="{{ route('member.smm.history.show', $order->invoice) }}" class="btn btn-primary btn-sm px-2 py-1" title="Order Details"><i class="fa fa-eye"></i> Detail</a>
                                            @if($order->api && $order->sid)
                                                @if(in_array(strtolower($order->status_order), ['success', 'finish', 'completed', 'complete']))
                                                    <button class="btn btn-info btn-sm px-2 py-1" disabled title="Sync Status"><i class="fa fa-refresh"></i></button>
                                                @else
                                                    <form method="POST" action="{{ route('member.smm.order.sync', $order->id) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-info btn-sm px-2 py-1" title="Sync Status"><i class="fa fa-refresh"></i></button>
                                                    </form>
                                                @endif
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
@endsection

