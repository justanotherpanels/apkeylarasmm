@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pengguna</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Admin</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">Users</a></li>
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
            Form Tambah Pengguna
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.user.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="full_name">Nama Lengkap</label>
                    <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control" id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Masukkan username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" placeholder="Masukkan password (min. 6 karakter)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="balance">Saldo (Rp)</label>
                    <input class="form-control" id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', 0) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="level">Level Akses</label>
                    <select class="form-select" id="level" name="level" required>
                        <option value="Member" {{ old('level') == 'Member' ? 'selected' : '' }}>Member</option>
                        <option value="Admin" {{ old('level') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="status">Status Akun</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Not-Active" {{ old('status') == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label pb-0">Role Seller</label>
                    <div class="form-check">
                        <input class="form-check-input" id="is_seller" name="is_seller" type="checkbox" value="1" {{ old('is_seller') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_seller">Jadikan pengguna ini sebagai Seller</label>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Simpan</button>
                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
