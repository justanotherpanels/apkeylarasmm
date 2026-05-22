@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Edit Platform</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">SMM</li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.smm.platform') }}">Platform</a></li>
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
                        <h5>Form Edit Platform</h5>
                        <span>Perbarui konfigurasi data platform di bawah ini.</span>
                    </div>
                    <div class="card-body">
                        <form class="theme-form" action="{{ route('admin.smm.platform.update', $platform->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="name">Nama Platform</label>
                                        <input class="form-control" id="name" name="name" type="text" placeholder="Masukkan nama platform" value="{{ old('name', $platform->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="id_category_smm">Kategori SMM</label>
                                        <select class="form-select" id="id_category_smm" name="id_category_smm">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->id }}" {{ old('id_category_smm', $platform->id_category_smm) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="icon_imagekit_url">Icon Imagekit URL</label>
                                        <input class="form-control" id="icon_imagekit_url" name="icon_imagekit_url" type="url" placeholder="https://ik.imagekit.io/your_id/platform/instagram.png" value="{{ old('icon_imagekit_url', $platform->icon_imagekit_url) }}">
                                        <small class="form-text text-muted">Masukkan link URL icon SVG/PNG platform dari Imagekit.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end px-0 pb-0">
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                <a href="{{ route('admin.smm.platform') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
