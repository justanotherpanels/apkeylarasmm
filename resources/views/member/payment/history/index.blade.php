@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Deposit History</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">History</li>
        <li class="breadcrumb-item active">Deposit History</li>
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
            Deposits
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                    <tfoot>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($deposits as $deposit)
                            <tr>
                                <td><strong class="text-primary">#{{ $deposit->invoice }}</strong></td>
                                <td>{{ $deposit->create_at ? $deposit->create_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $deposit->detail_transaction['payment_method'] ?? 'N/A' }}</td>
                                <td>${{ number_format($deposit->amount, 2) }}</td>
                                <td>USD</td>
                                <td>
                                    @php
                                        $status = strtolower($deposit->status_payment);
                                        $badgeClass = 'bg-primary';
                                        if ($status === 'success' || $status === 'completed') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($status === 'pending') {
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif ($status === 'cancel' || $status === 'cancelled') {
                                            $badgeClass = 'bg-secondary';
                                        } else {
                                            $badgeClass = 'bg-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $deposit->status_payment }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        <a href="{{ route('member.payment.history.show', $deposit->invoice) }}" class="btn btn-primary btn-sm px-2 py-1" title="View Details">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        @if($deposit->status_payment === 'Pending')
                                            <form method="POST" action="{{ route('member.payment.history.sync', $deposit->invoice) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info btn-sm px-2 py-1" title="Sync Status">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-info btn-sm px-2 py-1" disabled title="Sync Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
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
