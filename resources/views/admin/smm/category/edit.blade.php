@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Kategori</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.smm.category') }}">Kategori</a></li>
        <li class="breadcrumb-item active">Edit</li>
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
            <i class="fas fa-edit me-1"></i>
            Form Edit Kategori SMM
        </div>
        <div class="card-body">
            <form action="{{ route('admin.smm.category.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama Kategori</label>
                            <input class="form-control" id="name" name="name" type="text" placeholder="Masukkan nama kategori" value="{{ old('name', $category->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="code">Kode Kategori (Slug/Unique)</label>
                            <input class="form-control" id="code" name="code" type="text" placeholder="Masukkan kode unik" value="{{ old('code', $category->code) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status Kategori</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" {{ old('status', $category->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Not-Active" {{ old('status', $category->status) == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.smm.category') }}" class="btn btn-secondary me-2">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
