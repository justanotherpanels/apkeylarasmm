@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Daftar API Provider</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">SMM</li>
                        <li class="breadcrumb-item active">API</li>
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
                @if (session('error'))
                <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Data API Provider</h5>
                        <a href="{{ route('admin.smm.api.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah API</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Provider</th>
                                        <th>Kode</th>
                                        <th>URL</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apis ?? [] as $api)
                                    <tr>
                                        <td>{{ $api->id }}</td>
                                        <td>{{ $api->name }}</td>
                                        <td>{{ $api->code }}</td>
                                        <td>{{ $api->url }}</td>
                                        <td>$ {{ number_format($api->balance, 2, '.', ',') }}</td>
                                        <td>
                                            @if($api->status == 'Active')
                                                <span class="badge badge-success">{{ $api->status }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $api->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.smm.api.sync', $api->id) }}" class="btn btn-info btn-sm px-2 py-1 text-white" title="Sync Balance"><i class="fa fa-refresh"></i></a>
                                            <a href="{{ route('admin.smm.api.edit', $api->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <form action="{{ route('admin.smm.api.destroy', $api->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus provider ini?');" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Hapus"><i class="fa fa-trash"></i></button>
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
