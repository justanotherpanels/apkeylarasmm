@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Gateway Settings</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Payment</li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.payment.settings.update') }}">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-cogs me-1"></i>
                Konfigurasi Payment Gateway
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h5 class="mb-4"><i class="fab fa-bitcoin text-warning me-1"></i> Cryptomus</h5>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="cryptomus_merchant_id">Merchant ID</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="cryptomus_merchant_id" name="cryptomus_merchant_id" type="text" value="{{ old('cryptomus_merchant_id', $cryptomus->api_config['merchant_id'] ?? '') }}" placeholder="Masukkan Cryptomus Merchant ID">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="cryptomus_payment_key">Payment API Key</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="cryptomus_payment_key" name="cryptomus_payment_key" type="text" value="{{ old('cryptomus_payment_key', $cryptomus->api_config['payment_key'] ?? '') }}" placeholder="Masukkan Cryptomus Payment API Key">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="cryptomus_fee_bearer">Beban Biaya Admin</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="cryptomus_fee_bearer" name="cryptomus_fee_bearer">
                                    <option value="user" {{ old('cryptomus_fee_bearer', $cryptomus->api_config['fee_bearer'] ?? 'user') == 'user' ? 'selected' : '' }}>Ditanggung User (Pembuat Deposit)</option>
                                    <option value="admin" {{ old('cryptomus_fee_bearer', $cryptomus->api_config['fee_bearer'] ?? 'user') == 'admin' ? 'selected' : '' }}>Ditanggung Admin / Sistem</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 ps-md-4">
                        <h5 class="mb-4"><i class="fab fa-paypal text-primary me-1"></i> PayPal</h5>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="paypal_mode">Environment Mode</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="paypal_mode" name="paypal_mode">
                                    <option value="sandbox" {{ old('paypal_mode', $paypal->api_config['mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="live" {{ old('paypal_mode', $paypal->api_config['mode'] ?? 'sandbox') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="paypal_client_id">Client ID</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="paypal_client_id" name="paypal_client_id" type="text" value="{{ old('paypal_client_id', $paypal->api_config['client_id'] ?? '') }}" placeholder="Masukkan PayPal Client ID">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="paypal_client_secret">Client Secret</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="paypal_client_secret" name="paypal_client_secret" type="text" value="{{ old('paypal_client_secret', $paypal->api_config['client_secret'] ?? '') }}" placeholder="Masukkan PayPal Client Secret">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="paypal_fee_bearer">Beban Biaya Admin</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="paypal_fee_bearer" name="paypal_fee_bearer">
                                    <option value="user" {{ old('paypal_fee_bearer', $paypal->api_config['fee_bearer'] ?? 'user') == 'user' ? 'selected' : '' }}>Ditanggung User (Pembuat Deposit)</option>
                                    <option value="admin" {{ old('paypal_fee_bearer', $paypal->api_config['fee_bearer'] ?? 'user') == 'admin' ? 'selected' : '' }}>Ditanggung Admin / Sistem</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" type="submit">Simpan Konfigurasi</button>
            </div>
        </div>
    </form>
</div>
@endsection
