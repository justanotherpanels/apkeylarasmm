@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah API Provider</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.smm.api') }}">API</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Form Tambah Provider SMM
        </div>
        <div class="card-body">
            <form action="{{ url('admin/smm/api') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama Provider</label>
                        <input class="form-control" id="name" name="name" type="text" placeholder="Masukkan nama provider (Contoh: Jagoan SMM)" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="code">Kode Provider (Unique)</label>
                        <input class="form-control" id="code" name="code" type="text" placeholder="Masukkan kode unik (Contoh: jagoansmm)" value="{{ old('code') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="url">API URL Endpoint</label>
                        <input class="form-control" id="url" name="url" type="url" placeholder="https://api-endpoint-smm.com/v2" value="{{ old('url') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="api_key">API Key</label>
                        <input class="form-control" id="api_key" name="api_key" type="text" placeholder="Masukkan API Key dari provider" value="{{ old('api_key') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="balance">Balance Awal (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input class="form-control" id="balance" name="balance" type="number" step="0.01" placeholder="0.00" value="{{ old('balance', '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="status">Status Koneksi</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Not-Active" {{ old('status') == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.smm.api') }}" class="btn btn-secondary me-2">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan Koneksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
