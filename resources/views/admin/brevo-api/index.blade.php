@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Brevo API</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Brevo API</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-xl-12">
                @if (session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form class="theme-form mega-form" method="POST" action="{{ route('admin.brevo-api.update') }}">
                    @csrf
                    
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5><i class="fa fa-envelope text-info"></i> Konfigurasi Brevo API</h5>
                            <span>Pengaturan integrasi API Brevo (SendinBlue)</span>
                        </div>
                        <div class="card-body">
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
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit">Simpan Konfigurasi</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
