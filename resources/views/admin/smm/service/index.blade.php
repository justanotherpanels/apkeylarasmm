@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Daftar Layanan (Services)</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">SMM</li>
                        <li class="breadcrumb-item active">Service</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                @if (session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Data Layanan SMM</h5>
                        <div>
                            <a href="{{ route('admin.smm.import') }}" class="btn btn-info me-2"><i class="fa fa-refresh"></i> Tarik Layanan API</a>
                            <a href="{{ route('admin.smm.service.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Layanan</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <form action="{{ route('admin.smm.service.update_pid') }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <div class="input-group w-100">
                                        <select name="id_api_smm" class="form-select" required>
                                            <option value="">Pilih API Provider...</option>
                                            @foreach(($apis ?? []) as $api)
                                                <option value="{{ $api->id }}">{{ $api->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-warning"><i class="fa fa-refresh"></i> Update PID</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.smm.service.apply_markup') }}" method="POST" class="d-flex align-items-center justify-content-md-end">
                                    @csrf
                                    <div class="input-group" style="max-width: 500px;">
                                        <span class="input-group-text">% Sale</span>
                                        <input type="number" step="0.01" min="0" name="markup_sale" class="form-control" placeholder="cth: 20" required>
                                        <span class="input-group-text">% Reseller</span>
                                        <input type="number" step="0.01" min="0" name="markup_reseller" class="form-control" placeholder="cth: 10" required>
                                        <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>PID</th>
                                        <th>Nama Layanan</th>
                                        <th>Kategori</th>
                                        <th>API Provider</th>
                                        <th>Harga API</th>
                                        <th>Harga Jual</th>
                                        <th>Harga Reseller</th>
                                        <th>Min - Max Order</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services ?? [] as $service)
                                    <tr>
                                        <td>{{ $service->id }}</td>
                                        <td>{{ $service->pid ?? '-' }}</td>
                                        <td>{{ $service->name_service }}</td>
                                        <td>{{ $service->category->name ?? 'Tidak Ada Kategori' }}</td>
                                        <td>{{ $service->api->name ?? 'Tidak Ada Provider' }}</td>
                                        <td>$ {{ number_format($service->price_api, 4, '.', ',') }}</td>
                                        <td>$ {{ number_format($service->price_sale, 4, '.', ',') }}</td>
                                        <td>$ {{ number_format($service->price_reseller, 4, '.', ',') }}</td>
                                        <td>{{ number_format($service->min_order) }} - {{ number_format($service->max_order) }}</td>
                                        <td>
                                            @if($service->type == 'Default') <span class="badge badge-primary">Default</span>
                                            @elseif($service->type == 'Package') <span class="badge badge-info">Package</span>
                                            @elseif($service->type == 'Custom Comments') <span class="badge badge-warning">Custom Comments</span>
                                            @else <span class="badge badge-secondary">{{ $service->type }}</span> @endif
                                        </td>
                                        <td>
                                            @if($service->status == 'Active')
                                                <span class="badge badge-success">{{ $service->status }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $service->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.smm.service.edit', $service->id) }}" class="btn btn-warning btn-sm px-2 py-1 mb-1" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <form action="{{ route('admin.smm.service.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1 mb-1" title="Hapus"><i class="fa fa-trash"></i></button>
                                            </form>
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
</div>
@endsection
