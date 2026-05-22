@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Pengaturan Website</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Setting</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        @if (session('success'))
        <div class="alert alert-success dark alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form class="theme-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <!-- Kolom Kiri: Informasi Umum & Sosial Media -->
                <div class="col-xl-6 col-md-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Informasi Umum</h5>
                            <span>Nama situs, email, dan identitas website Anda.</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="site_name">Nama Situs</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="site_name" name="site_name" type="text"
                                        value="{{ old('site_name', $setting->site_name ?? '') }}"
                                        placeholder="Contoh: APKEY SMM Panel">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="email">Email Kontak</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="email" name="email" type="email"
                                        value="{{ old('email', $setting->email ?? '') }}"
                                        placeholder="admin@example.com">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="logo">Upload Logo</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="logo" name="logo" type="file" accept="image/*">
                                    @error('logo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @if(!empty($setting->logo_path))
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <div class="border rounded p-1 bg-light d-inline-block">
                                                <img src="{{ $setting->logo_path }}" alt="Logo" style="height:40px; max-width: 150px; object-fit: contain;">
                                            </div>
                                            <span class="text-muted small word-break-all">{{ basename($setting->logo_path) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="favicon">Upload Favicon</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="favicon" name="favicon" type="file" accept="image/*">
                                    @error('favicon')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @if(!empty($setting->favicon_path))
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <div class="border rounded p-1 bg-light d-inline-block">
                                                <img src="{{ $setting->favicon_path }}" alt="Favicon" style="height:24px; max-width: 24px; object-fit: contain;">
                                            </div>
                                            <span class="text-muted small word-break-all">{{ basename($setting->favicon_path) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Sosial Media</h5>
                            <span>Tautan sosial media dan kontak langsung.</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="whatsapp_url"><i class="fa fa-whatsapp me-1"></i> WhatsApp</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="whatsapp_url" name="whatsapp_url" type="text"
                                        value="{{ old('whatsapp_url', $setting->whatsapp_url ?? '') }}"
                                        placeholder="6281234567890 atau https://wa.me/...">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="instagram_url"><i class="fa fa-instagram me-1"></i> Instagram</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="instagram_url" name="instagram_url" type="text"
                                        value="{{ old('instagram_url', $setting->instagram_url ?? '') }}"
                                        placeholder="https://instagram.com/username">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label" for="facebook_url"><i class="fa fa-facebook me-1"></i> Facebook</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="facebook_url" name="facebook_url" type="text"
                                        value="{{ old('facebook_url', $setting->facebook_url ?? '') }}"
                                        placeholder="https://facebook.com/username">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Custom Code -->
                <div class="col-xl-6 col-md-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Head Code</h5>
                            <span>Script atau tag yang akan dimasukkan ke dalam <code>&lt;head&gt;</code> halaman (misal: Analytics, Pixel, SEO meta).</span>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="head_code" name="head_code" rows="12"
                                placeholder="<!-- Contoh: Google Analytics atau Meta Pixel tag -->">{{ old('head_code', $setting->head_code ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Footer Code</h5>
                            <span>Script atau tag yang akan dimasukkan sebelum penutup <code>&lt;/body&gt;</code> (misal: Live Chat, script khusus).</span>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="footer_code" name="footer_code" rows="12"
                                placeholder="<!-- Contoh: Tidio, Tawkto, atau script lainnya -->">{{ old('footer_code', $setting->footer_code ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Save Button -->
            <div class="row mb-4">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="fa fa-save me-2"></i>Simpan Pengaturan</button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
