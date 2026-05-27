@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Service Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item"><a href="{{ route('member.smm.service') }}">Services</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>

    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Service Information
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Detailed pricing, specifications, and description for this SMM service.</p>

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Service ID:</div>
                        <div class="col-md-9"><code>#{{ $service->pid }}</code></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Category:</div>
                        <div class="col-md-9"><span class="badge bg-primary">{{ $service->category ? $service->category->name : 'General' }}</span></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Service Name:</div>
                        <div class="col-md-9"><strong>{{ $service->name_service }}</strong></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Price (per 1,000):</div>
                        <div class="col-md-9 text-success fw-bold" style="font-size: 1.2rem;">${{ number_format($service->price_sale, 4) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Minimum Order:</div>
                        <div class="col-md-9">{{ number_format($service->min_order) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Maximum Order:</div>
                        <div class="col-md-9">{{ number_format($service->max_order) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 fw-bold">Refill Status:</div>
                        <div class="col-md-9">
                            @if($service->refill)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Refill Guarantee Enabled</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> Refill Guarantee Disabled</span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <h6 class="fw-bold">Service Description:</h6>
                            <div class="p-3 rounded border mt-2 bg-light">
                                {!! nl2br(e(!empty($service->desc) ? $service->desc : 'No description available for this service.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('member.smm.order', ['service_id' => $service->id]) }}" class="btn btn-success"><i class="fas fa-shopping-cart me-1"></i> Order Now</a>
                    <a href="{{ route('member.smm.service') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Catalog</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
