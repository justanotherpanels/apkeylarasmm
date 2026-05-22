@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Services List</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">SMM</li>
                    <li class="breadcrumb-item active">Services</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <!-- Zero Configuration Starts-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>All SMM Services & Rates</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
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
                            <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td><code>#{{ $service->pid }}</code></td>
                                        <td><span class="badge badge-primary">{{ $service->category ? $service->category->name : 'General' }}</span></td>
                                        <td><strong>{{ $service->name_service }}</strong></td>
                                        <td><span class="text-success font-weight-bold">${{ number_format($service->price_sale, 4) }}</span></td>
                                        <td>{{ number_format($service->min_order) }}</td>
                                        <td>{{ number_format($service->max_order) }}</td>
                                        <td>
                                            @if($service->refill)
                                                <span class="badge badge-success"><i class="fa fa-check me-1"></i> Refill Guarantee</span>
                                            @else
                                                <span class="badge badge-secondary"><i class="fa fa-times me-1"></i> No Refill</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('member.smm.service.show', $service->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-info-circle"></i> Detail</a>
                                            <a href="{{ route('member.smm.order', ['service_id' => $service->id]) }}" class="btn btn-success btn-xs"><i class="fa fa-shopping-cart"></i> Order</a>
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
