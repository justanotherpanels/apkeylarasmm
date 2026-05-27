@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Services List</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item active">Services</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All SMM Services & Rates
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Service Name</th>
                        <th>Price (per 1k)</th>
                        <th>Min Order</th>
                        <th>Max Order</th>
                        <th>Refill</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Service Name</th>
                        <th>Price (per 1k)</th>
                        <th>Min Order</th>
                        <th>Max Order</th>
                        <th>Refill</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td><code>#{{ $service->pid }}</code></td>
                            <td><span class="badge bg-primary">{{ $service->category ? $service->category->name : 'General' }}</span></td>
                            <td><strong>{{ $service->name_service }}</strong></td>
                            <td><span class="text-success fw-bold">${{ number_format($service->price_sale, 4) }}</span></td>
                            <td>{{ number_format($service->min_order) }}</td>
                            <td>{{ number_format($service->max_order) }}</td>
                            <td>
                                @if($service->refill)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Refill Guarantee</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No Refill</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('member.smm.service.show', $service->id) }}" class="btn btn-primary btn-sm py-1 px-2"><i class="fas fa-info-circle"></i> Detail</a>
                                    <a href="{{ route('member.smm.order', ['service_id' => $service->id]) }}" class="btn btn-success btn-sm py-1 px-2"><i class="fas fa-shopping-cart"></i> Order</a>
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
