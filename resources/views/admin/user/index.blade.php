@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Pengguna</h1>
    
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
                Data Pengguna
            </div>
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Pengguna</a>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                <tfoot>
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
                </tfoot>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ number_format($user->balance, 2, ',', '.') }}</td>
                        <td>
                            @if($user->level == 'Admin')
                                <span class="badge bg-primary">{{ $user->level }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $user->level }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status == 'Active')
                                <span class="badge bg-success">{{ $user->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $user->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_seller)
                                <span class="badge bg-info text-dark">Seller</span>
                            @else
                                <span class="badge bg-light text-dark">Bukan</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
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
