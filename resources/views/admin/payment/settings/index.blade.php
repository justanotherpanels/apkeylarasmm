@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Payment Gateway</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">Payment</li>
                        <li class="breadcrumb-item active">Settings</li>
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

                <form class="theme-form mega-form" method="POST" action="{{ route('admin.payment.settings.update') }}">
                    @csrf
                    
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5><i class="fa fa-bitcoin text-warning"></i> Konfigurasi Cryptomus</h5>
                            <span>Pengaturan integrasi API Cryptomus (Crypto Payment)</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="cryptomus_merchant_id">Merchant ID</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="cryptomus_merchant_id" name="cryptomus_merchant_id" type="text" value="{{ old('cryptomus_merchant_id', $cryptomus->api_config['merchant_id'] ?? '') }}" placeholder="Masukkan Cryptomus Merchant ID">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="cryptomus_payment_key">Payment API Key</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="cryptomus_payment_key" name="cryptomus_payment_key" type="text" value="{{ old('cryptomus_payment_key', $cryptomus->api_config['payment_key'] ?? '') }}" placeholder="Masukkan Cryptomus Payment API Key">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="cryptomus_fee_bearer">Beban Biaya Admin</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="cryptomus_fee_bearer" name="cryptomus_fee_bearer">
                                        <option value="user" {{ old('cryptomus_fee_bearer', $cryptomus->api_config['fee_bearer'] ?? 'user') == 'user' ? 'selected' : '' }}>Ditanggung User (Pembuat Deposit)</option>
                                        <option value="admin" {{ old('cryptomus_fee_bearer', $cryptomus->api_config['fee_bearer'] ?? 'user') == 'admin' ? 'selected' : '' }}>Ditanggung Admin / Sistem</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5><i class="fa fa-paypal text-primary"></i> Konfigurasi PayPal</h5>
                            <span>Pengaturan integrasi API PayPal</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="paypal_mode">Environment Mode</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="paypal_mode" name="paypal_mode">
                                        <option value="sandbox" {{ old('paypal_mode', $paypal->api_config['mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="live" {{ old('paypal_mode', $paypal->api_config['mode'] ?? 'sandbox') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="paypal_client_id">Client ID</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="paypal_client_id" name="paypal_client_id" type="text" value="{{ old('paypal_client_id', $paypal->api_config['client_id'] ?? '') }}" placeholder="Masukkan PayPal Client ID">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="paypal_client_secret">Client Secret</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="paypal_client_secret" name="paypal_client_secret" type="text" value="{{ old('paypal_client_secret', $paypal->api_config['client_secret'] ?? '') }}" placeholder="Masukkan PayPal Client Secret">
                                </div>
                            </div>
                            <div class="mb-3 row mb-0">
                                <label class="col-sm-3 col-form-label" for="paypal_fee_bearer">Beban Biaya Admin</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="paypal_fee_bearer" name="paypal_fee_bearer">
                                        <option value="user" {{ old('paypal_fee_bearer', $paypal->api_config['fee_bearer'] ?? 'user') == 'user' ? 'selected' : '' }}>Ditanggung User (Pembuat Deposit)</option>
                                        <option value="admin" {{ old('paypal_fee_bearer', $paypal->api_config['fee_bearer'] ?? 'user') == 'admin' ? 'selected' : '' }}>Ditanggung Admin / Sistem</option>
                                    </select>
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
