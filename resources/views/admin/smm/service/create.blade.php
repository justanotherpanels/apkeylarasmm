@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Layanan SMM</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.smm.service') }}">Layanan</a></li>
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
            Form Tambah Layanan SMM
        </div>
        <div class="card-body">
            <form action="{{ route('admin.smm.service.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="name_service">Nama Layanan</label>
                            <input class="form-control" id="name_service" name="name_service" type="text" placeholder="Masukkan nama layanan (Contoh: Instagram Followers Real)" value="{{ old('name_service') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="id_category_smm">Kategori Layanan</label>
                            <select class="form-select" id="id_category_smm" name="id_category_smm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('id_category_smm') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="id_api_smm">API Provider</label>
                            <select class="form-select" id="id_api_smm" name="id_api_smm" required>
                                <option value="">-- Pilih Provider --</option>
                                @foreach($apis ?? [] as $api)
                                    <option value="{{ $api->id }}" {{ old('id_api_smm') == $api->id ? 'selected' : '' }}>
                                        {{ $api->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="pid">Provider Service ID (PID)</label>
                            <input class="form-control" id="pid" name="pid" type="text" placeholder="Masukkan ID Layanan dari Provider (Contoh: 1204)" value="{{ old('pid') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="price_api">Harga API ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="price_api" name="price_api" type="number" step="0.0001" placeholder="0.00" value="{{ old('price_api', '0.00') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="price_sale">Harga Jual ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="price_sale" name="price_sale" type="number" step="0.0001" placeholder="0.00" value="{{ old('price_sale', '0.00') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="price_reseller">Harga Reseller ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="price_reseller" name="price_reseller" type="number" step="0.0001" placeholder="0.00" value="{{ old('price_reseller', '0.00') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="min_order">Minimal Order</label>
                            <input class="form-control" id="min_order" name="min_order" type="number" placeholder="100" value="{{ old('min_order', 100) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="max_order">Maksimal Order</label>
                            <input class="form-control" id="max_order" name="max_order" type="number" placeholder="10000" value="{{ old('max_order', 10000) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="type">Tipe Layanan</label>
                            <select class="form-select" id="type" name="type">
                                <option value="Default" {{ old('type') == 'Default' ? 'selected' : '' }}>Default</option>
                                <option value="Package" {{ old('type') == 'Package' ? 'selected' : '' }}>Package</option>
                                <option value="Custom Comments" {{ old('type') == 'Custom Comments' ? 'selected' : '' }}>Custom Comments</option>
                                <option value="Poll" {{ old('type') == 'Poll' ? 'selected' : '' }}>Poll</option>
                                <option value="Subscriptions" {{ old('type') == 'Subscriptions' ? 'selected' : '' }}>Subscriptions</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="refill">Status Refill</label>
                            <select class="form-select" id="refill" name="refill">
                                <option value="1" {{ old('refill') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('refill') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status Aktif</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Not-Active" {{ old('status') == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="desc">Deskripsi Layanan</label>
                            <textarea class="form-control" id="desc" name="desc" rows="4" placeholder="Masukkan detail atau deskripsi mengenai layanan ini...">{{ old('desc') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.smm.service') }}" class="btn btn-secondary me-2">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
