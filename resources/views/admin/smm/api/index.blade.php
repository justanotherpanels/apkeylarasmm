@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar API Provider</h1>
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
        {{ session('error') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Data API Provider
            </div>
            <a href="{{ route('admin.smm.api.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah API</a>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nama Provider</th>
                        <th>Kode</th>
                        <th>URL</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
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
                                <span class="badge bg-success">{{ $api->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $api->status }}</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.smm.api.sync', $api->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm px-2 py-1 text-white" title="Sync Balance"><i class="fas fa-sync-alt"></i></button>
                            </form>
                            <a href="{{ route('admin.smm.api.edit', $api->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('admin.smm.api.destroy', $api->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus provider ini?');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Hapus"><i class="fas fa-trash"></i></button>
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
