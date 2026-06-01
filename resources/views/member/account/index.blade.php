@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Pengaturan Akun</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Pengaturan Akun</li>
    </ol>

    @if(session('success'))
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
        <!-- Card 1: Personal & Security Settings -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-user-cog me-2"></i>Informasi Akun & Keamanan</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('member.account.update') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="username">Username</label>
                                <input class="form-control bg-light" id="username" type="text" value="{{ $user->username }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="email">Email</label>
                                <input class="form-control bg-light" id="email" type="text" value="{{ $user->email }}" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="full_name">Nama Lengkap</label>
                                <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}" required placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold" for="country_code">Kode Negara</label>
                                <input class="form-control" id="country_code" name="country_code" type="text" value="{{ old('country_code', $user->country_code ?? '62') }}" required placeholder="Contoh: 62">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="phone">Nomor HP</label>
                                <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 8123456789">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3"><i class="fas fa-lock me-2 text-warning"></i>Ganti Password Baru</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="password">Password Baru</label>
                                <input class="form-control" id="password" name="password" type="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                <div class="form-text small text-muted">Minimal 6 karakter.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="Masukkan kembali password baru">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4 me-2">Simpan Perubahan</button>
                            <a href="{{ route('member.index') }}" class="btn btn-secondary px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 2: API Key Integration -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-key me-2"></i>API Key Saya</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">Gunakan API Key ini untuk melakukan integrasi SMM panel dengan website atau bot Anda.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="apiKeyInput">Kunci API</label>
                        <div class="input-group">
                            <input class="form-control bg-light font-monospace" type="password" value="{{ $user->api_key }}" id="apiKeyInput" placeholder="Belum ada API Key" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKey(event)"><i class="fa fa-eye"></i></button>
                            @if($user->api_key)
                                <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey(event)"><i class="fa fa-copy"></i></button>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('member.pages.api.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info w-100 fw-bold text-dark mt-2">
                            <i class="fa fa-sync-alt me-1"></i> {{ $user->api_key ? 'Regenerate API Key' : 'Generate API Key' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
function toggleApiKey(event) {
    var x = document.getElementById("apiKeyInput");
    var btn = event.currentTarget;
    if (x.type === "password") {
        x.type = "text";
        btn.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
        x.type = "password";
        btn.innerHTML = '<i class="fa fa-eye"></i>';
    }
}

function copyApiKey(event) {
    var copyText = document.getElementById("apiKeyInput");
    if (!copyText.value) return;
    
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    var copyBtn = event.currentTarget;
    var originalHTML = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fa fa-check"></i>';
    copyBtn.className = 'btn btn-success';
    setTimeout(function() {
        copyBtn.innerHTML = originalHTML;
        copyBtn.className = 'btn btn-outline-secondary';
    }, 2000);
}
</script>
@endsection
@endsection
