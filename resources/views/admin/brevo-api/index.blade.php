@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Brevo API</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Brevo API</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope text-info me-1"></i>
            Konfigurasi Brevo API
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.brevo-api.update') }}">
                @csrf
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="status">Status</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="status" name="status">
                            <option value="Active" {{ old('status', $brevo->status ?? 'Not-Active') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Not-Active" {{ old('status', $brevo->status ?? 'Not-Active') == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="api_key">API Key</label>
                    <div class="col-sm-9">
                        <input class="form-control" id="api_key" name="api_key" type="text" value="{{ old('api_key', $brevo->api_config['api_key'] ?? '') }}" placeholder="Masukkan Brevo API Key">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="sender_email">Sender Email</label>
                    <div class="col-sm-9">
                        <input class="form-control" id="sender_email" name="sender_email" type="email" value="{{ old('sender_email', $brevo->api_config['sender_email'] ?? '') }}" placeholder="Masukkan sender email (harus diverifikasi di Brevo)">
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button class="btn btn-primary" type="submit">Simpan Konfigurasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
