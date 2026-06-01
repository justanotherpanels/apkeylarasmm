@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Profil Saya</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Admin</li>
        <li class="breadcrumb-item active">Profil Saya</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

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

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-edit me-1"></i>
                    Informasi Profil & Update Password
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input class="form-control bg-light" id="username" type="text" value="{{ $user->username }}" readonly disabled>
                                <div class="form-text small text-muted">Username tidak dapat diubah.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control bg-light" id="email" type="email" value="{{ $user->email }}" readonly disabled>
                                <div class="form-text small text-muted">Email tidak dapat diubah.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="full_name">Nama Lengkap</label>
                                <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="password">Password Baru</label>
                                <input class="form-control" id="password" name="password" type="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <div class="form-text small text-muted">Minimal 6 karakter.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password baru jika diubah">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4 me-2">Simpan Perubahan</button>
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
