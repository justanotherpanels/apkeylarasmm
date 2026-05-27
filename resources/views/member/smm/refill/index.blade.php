@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Refill History</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">History</li>
        <li class="breadcrumb-item active">Refill History</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-sync-alt me-1"></i> Your Refill Requests
        </div>
        <div class="card-body">
            <p class="text-muted small mb-4">Track and check the status of your SMM order refill requests.</p>
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Refill ID</th>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Refill ID</th>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td><strong class="text-primary">#RF-108</strong></td>
                        <td>2023-10-15 15:45</td>
                        <td><strong>#2940</strong></td>
                        <td>YouTube Subscribers [High Quality]</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                    </tr>
                    <tr>
                        <td><strong class="text-primary">#RF-102</strong></td>
                        <td>2023-10-10 11:20</td>
                        <td><strong>#2891</strong></td>
                        <td>Instagram Followers [Real & Active]</td>
                        <td><span class="badge bg-success">Completed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
