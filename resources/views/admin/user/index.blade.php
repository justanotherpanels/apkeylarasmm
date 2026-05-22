@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Daftar Pengguna</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                @if (session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Data Pengguna</h5>
                        <a href="{{ route('admin.user.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Pengguna</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Saldo (Rp)</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Role Seller</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->full_name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ number_format($user->balance, 2, ',', '.') }}</td>
                                        <td>
                                            @if($user->level == 'Admin')
                                                <span class="badge badge-primary">{{ $user->level }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $user->level }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == 'Active')
                                                <span class="badge badge-success">{{ $user->status }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $user->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_seller)
                                                <span class="badge badge-info">Seller</span>
                                            @else
                                                <span class="badge badge-light text-dark">Bukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fa fa-pencil"></i></a>
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
            <!-- Zero Configuration  Ends-->
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection
