@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Service Details</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">SMM</li>
                    <li class="breadcrumb-item"><a href="{{ route('member.smm.service') }}">Services</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Service Information</h5>
                    <span>Detailed pricing, specifications, and description for this SMM service.</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Service ID:</div>
                        <div class="col-md-9"><code>#{{ $service->pid }}</code></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Category:</div>
                        <div class="col-md-9"><span class="badge badge-primary">{{ $service->category ? $service->category->name : 'General' }}</span></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Service Name:</div>
                        <div class="col-md-9"><strong>{{ $service->name_service }}</strong></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Price (per 1,000):</div>
                        <div class="col-md-9 text-success font-weight-bold" style="font-size: 1.2rem;">${{ number_format($service->price_sale, 4) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Minimum Order:</div>
                        <div class="col-md-9">{{ number_format($service->min_order) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Maximum Order:</div>
                        <div class="col-md-9">{{ number_format($service->max_order) }}</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 font-weight-bold">Refill Status:</div>
                        <div class="col-md-9">
                            @if($service->refill)
                                <span class="badge badge-success"><i class="fa fa-check me-1"></i> Refill Guarantee Enabled</span>
                            @else
                                <span class="badge badge-secondary"><i class="fa fa-times me-1"></i> Refill Guarantee Disabled</span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <h6 class="font-weight-bold">Service Description:</h6>
                            <div class="p-3 rounded border mt-2" style="white-space: pre-wrap; line-height: 1.6; background-color: rgba(99, 98, 231, 0.05); border-color: rgba(99, 98, 231, 0.15) !important;">
                                {!! nl2br(e(!empty($service->desc) ? $service->desc : 'No description available for this service.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('member.smm.order', ['service_id' => $service->id]) }}" class="btn btn-success"><i class="fa fa-shopping-cart"></i> Order Now</a>
                    <a href="{{ route('member.smm.service') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Catalog</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid ends-->
@endsection
