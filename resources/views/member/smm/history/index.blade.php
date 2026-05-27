@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Order History</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item active">Order History</li>
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

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            SMM Orders
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                    <tfoot>
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
                    </tfoot>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong class="text-primary">#{{ $order->invoice }}</strong></td>
                            <td>{{ $order->service->name_service ?? 'N/A' }}</td>
                            <td>{{ number_format($order->amount) }}</td>
                            <td>${{ number_format($order->price_sale, 4) }}</td>
                            <td>{{ number_format($order->start_count) }}</td>
                            <td>{{ number_format($order->remains) }}</td>
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
                                    } elseif ($status === 'processing' || $status === 'in progres') {
                                        $badgeClass = 'bg-info text-dark';
                                    } elseif ($status === 'partial') {
                                        $badgeClass = 'bg-primary';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $order->status_order }}</span>
                            </td>
                            <td>{{ $order->create_at ? $order->create_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>
                                <div class="d-flex gap-1 align-items-center">
                                    <a href="{{ route('member.smm.history.show', $order->invoice) }}" class="btn btn-primary btn-sm px-2 py-1" title="Order Details">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    @if($order->status_order == 'Pending')
                                        <a href="{{ route('member.payment.add') }}" class="btn btn-success btn-sm px-2 py-1" title="Pay">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </a>
                                    @else
                                        <button class="btn btn-success btn-sm px-2 py-1" disabled title="Pay">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </button>
                                    @endif
                                    @if($order->api && $order->sid)
                                        @if(in_array(strtolower($order->status_order), ['success', 'finish', 'completed', 'complete']))
                                            <button class="btn btn-info btn-sm px-2 py-1" disabled title="Sync Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('member.smm.order.sync', $order->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info btn-sm px-2 py-1" title="Sync Status">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
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
@endsection
