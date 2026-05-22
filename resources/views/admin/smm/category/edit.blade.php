@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Edit Kategori</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">SMM</li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.smm.category') }}">Kategori</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
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
                        <h5>Form Edit Kategori SMM</h5>
                        <span>Perbarui konfigurasi kategori layanan di bawah ini.</span>
                    </div>
                    <div class="card-body">
                        <form class="theme-form" action="{{ route('admin.smm.category.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="name">Nama Kategori</label>
                                        <input class="form-control" id="name" name="name" type="text" placeholder="Masukkan nama kategori" value="{{ old('name', $category->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="code">Kode Kategori (Slug/Unique)</label>
                                        <input class="form-control" id="code" name="code" type="text" placeholder="Masukkan kode unik" value="{{ old('code', $category->code) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="status">Status Kategori</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Active" {{ old('status', $category->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Not-Active" {{ old('status', $category->status) == 'Not-Active' ? 'selected' : '' }}>Not-Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end px-0 pb-0">
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                <a href="{{ route('admin.smm.category') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
