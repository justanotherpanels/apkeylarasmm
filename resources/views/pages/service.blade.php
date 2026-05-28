@extends('layouts.home.master')

@section('content')
<section class="pt-8 pb-6 bg-white">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h1 class="mb-3 fs-8 fs-md-9 fw-bold">Our SMM Services</h1>
                <p class="mb-0 lead text-secondary">Explore all our social media marketing services with transparent pricing and real-time execution speeds.</p>
            </div>
        </div>
        
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="servicesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">ID</th>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Category</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Service Name</th>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Price (per 1k)</th>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Min Order</th>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Max Order</th>
                            <th class="text-nowrap text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3 text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border px-2 py-1.5 font-monospace">#{{ $service->pid ?? $service->id }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold">
                                        {{ $service->category ? $service->category->name : 'General' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $service->name_service }}</h6>
                                        @if($service->refill)
                                            <span class="text-success text-xs mt-1"><i class="fas fa-redo me-1"></i> Refill Guarantee</span>
                                        @else
                                            <span class="text-muted text-xs mt-1"><i class="fas fa-times me-1"></i> No Refill</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-success fw-bold fs-1">${{ number_format($service->price_sale, 4) }}</span>
                                </td>
                                <td class="text-nowrap">{{ number_format($service->min_order) }}</td>
                                <td class="text-nowrap">{{ number_format($service->max_order) }}</td>
                                <td class="text-nowrap text-center">
                                    @if($service->desc)
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#descModal{{ $service->id }}">
                                            Details
                                        </button>
                                        
                                        <!-- Modal for Description -->
                                        <div class="modal fade" id="descModal{{ $service->id }}" tabindex="-1" aria-labelledby="descModalLabel{{ $service->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header border-0 bg-warning text-white py-3">
                                                        <h5 class="modal-title fw-bold text-white" id="descModalLabel{{ $service->id }}">{{ $service->name_service }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start p-4">
                                                        <h6 class="fw-bold text-secondary mb-2">Service Description:</h6>
                                                        <p class="text-dark fs-0 lh-base" style="white-space: pre-line;">{{ $service->desc }}</p>
                                                        <hr class="my-3 opacity-10">
                                                        <div class="row text-center">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Min Order</small>
                                                                <span class="fw-bold">{{ number_format($service->min_order) }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Max Order</small>
                                                                <span class="fw-bold">{{ number_format($service->max_order) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-3 bg-light">
                                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                        <a href="{{ route('register') }}" class="btn btn-warning rounded-pill px-4 text-white fw-bold">Order Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fs--1">No description</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@push('styles')
<style>
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
    }
    .card {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .table thead th {
        font-weight: 700;
        font-family: 'Poppins', sans-serif;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
        background-color: #f8fafc;
    }
    .table tbody td {
        font-family: 'Poppins', sans-serif;
        color: #334155;
        font-size: 0.9rem;
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Modern Custom Styles for DataTables controls */
    .dataTables_wrapper .row {
        margin-bottom: 1.2rem;
        align-items: center;
    }
    
    /* Hide the default "Search:" text and show only input with placeholder */
    .dataTables_wrapper .dataTables_filter label {
        font-size: 0 !important;
        color: transparent !important;
        position: relative;
        display: block;
        margin: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        font-size: 0.875rem !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 0.5rem 1rem !important;
        width: 250px !important;
        max-width: 100%;
        margin-left: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #ffc107 !important;
    }
    
    /* Length control styling */
    .dataTables_wrapper .dataTables_length label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 0.4rem 1.8rem 0.4rem 0.75rem !important;
        font-size: 0.875rem !important;
        outline: none !important;
        box-shadow: none !important;
        cursor: pointer;
    }
    
    /* Pagination & Info styling */
    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 500;
        padding-top: 0.5rem;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.5rem;
    }
    .dataTables_wrapper .pagination {
        display: inline-flex;
        gap: 4px;
        margin: 0;
    }
    .dataTables_wrapper .page-item .page-link {
        border: 1px solid #e2e8f0;
        border-radius: 8px !important;
        color: #475569;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .dataTables_wrapper .page-item.active .page-link {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #fff !important;
    }
    .dataTables_wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #ffc107;
    }
    .dataTables_wrapper .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #cbd5e1;
    }
    .btn-outline-warning:hover {
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#servicesTable').DataTable({
            responsive: true,
            order: [[1, 'asc']], // Order by category initially
            language: {
                search: "",
                searchPlaceholder: "Search services...",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            },
            dom: "<'row'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end mt-2 mt-md-0'f>>" +
                 "<'row'<'col-12'tr>>" +
                 "<'row mt-3'<'col-12 col-md-5'i><'col-12 col-md-7 d-flex justify-content-md-end mt-2 mt-md-0'p>>"
        });
    });
</script>
@endpush
