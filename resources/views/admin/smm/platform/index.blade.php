@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Daftar Platform</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">SMM</li>
                        <li class="breadcrumb-item active">Platform</li>
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
                        <h5>Data Platform</h5>
                        <a href="{{ route('admin.smm.platform.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Platform</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon</th>
                                        <th>Nama Platform</th>
                                        <th>ID Kategori / Nama Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($platforms ?? [] as $platform)
                                    <tr>
                                        <td>{{ $platform->id }}</td>
                                        <td>
                                            @if($platform->icon_imagekit_url)
                                                <img src="{{ $platform->icon_imagekit_url }}" alt="Icon" width="40" height="40" style="object-fit: cover; border-radius: 5px;">
                                            @else
                                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center;"><i class="fa fa-image text-muted"></i></div>
                                            @endif
                                        </td>
                                        <td>{{ $platform->name }}</td>
                                        <td>
                                            @if($platform->category)
                                                <span class="badge badge-light text-dark">{{ $platform->category->name }}</span>
                                                <small class="text-muted d-block">ID: {{ $platform->id_category_smm }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.smm.platform.edit', $platform->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <form action="{{ route('admin.smm.platform.destroy', $platform->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus platform ini?');" style="display:inline-block;">
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
