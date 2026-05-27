@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Layanan (Services)</h1>
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Data Layanan SMM
            </div>
            <div>
                <a href="{{ route('admin.smm.import') }}" class="btn btn-info btn-sm text-white me-2"><i class="fas fa-sync-alt"></i> Tarik Layanan API</a>
                <a href="{{ route('admin.smm.service.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Layanan</a>
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
                            <button type="submit" class="btn btn-outline-warning"><i class="fas fa-sync-alt"></i> Update PID</button>
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
            
            <table id="datatablesSimple">
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
                <tfoot>
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
                </tfoot>
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
                            @if($service->type == 'Default') <span class="badge bg-primary">Default</span>
                            @elseif($service->type == 'Package') <span class="badge bg-info">Package</span>
                            @elseif($service->type == 'Custom Comments') <span class="badge bg-warning text-dark">Custom Comments</span>
                            @else <span class="badge bg-secondary">{{ $service->type }}</span> @endif
                        </td>
                        <td>
                            @if($service->status == 'Active')
                                <span class="badge bg-success">{{ $service->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $service->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.smm.service.edit', $service->id) }}" class="btn btn-warning btn-sm px-2 py-1 mb-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('admin.smm.service.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1 mb-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
