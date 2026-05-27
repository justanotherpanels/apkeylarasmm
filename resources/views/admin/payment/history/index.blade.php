@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Deposit History</h1>
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Deposit History
            </div>
            <form method="POST" action="{{ route('admin.payment.history.sync-all') }}">
                @csrf
                <button type="submit" class="btn btn-info btn-sm text-white" onclick="return confirm('Are you sure you want to sync all pending deposits?');">
                    <i class="fas fa-sync-alt"></i> Sync All Pending
                </button>
            </form>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                <tfoot>
                    <tr>
                        <th>Invoice</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </tfoot>
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
                                <span class="badge bg-success">Success</span>
                            @elseif($deposit->status_payment === 'Pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($deposit->status_payment === 'Cancel')
                                <span class="badge bg-secondary">Cancelled</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
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
@endsection
