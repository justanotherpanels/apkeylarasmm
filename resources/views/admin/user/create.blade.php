@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Tambah Pengguna</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-xl-12">
                @if ($errors->any())
                <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Form Tambah Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <form class="theme-form" method="POST" action="{{ route('admin.user.store') }}">
                            @csrf
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="full_name">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="username">Username</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Masukkan username" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="email">Email</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="password">Password</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="password" name="password" type="password" placeholder="Masukkan password (min. 6 karakter)" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="balance">Saldo (Rp)</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', 0) }}" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="level">Level Akses</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="Member" {{ old('level') == 'Member' ? 'selected' : '' }}>Member</option>
                                        <option value="Admin" {{ old('level') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label" for="status">Status Akun</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Not-Active" {{ old('status') == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label pb-0">Role Seller</label>
                                <div class="col-sm-9">
                                    <div class="mb-0">
                                        <div class="form-check form-check-inline checkbox checkbox-primary">
                                            <input class="form-check-input" id="is_seller" name="is_seller" type="checkbox" value="1" {{ old('is_seller') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_seller">Jadikan pengguna ini sebagai Seller</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary me-2">Simpan</button>
                                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
