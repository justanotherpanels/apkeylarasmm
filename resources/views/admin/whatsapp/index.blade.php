@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Multi-Akun WhatsApp</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">WhatsApp</li>
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
        
        @if (session('error'))
        <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <!-- Data Table Akun WA -->
            <div class="col-xl-8 col-md-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Daftar Akun WhatsApp</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                            <i class="fa fa-plus me-2"></i>Tambah Akun
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nomor WA</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Kode Tautan (Pairing)</th>
                                        <th>Ditambahkan Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accounts as $acc)
                                        <tr>
                                            <td class="fw-bold">{{ $acc->phone_number }}</td>
                                            <td>
                                                @if($acc->type == 'Bot')
                                                    <span class="badge badge-info"><i class="fa fa-robot me-1"></i> Bot</span>
                                                @else
                                                    <span class="badge badge-primary"><i class="fa fa-key me-1"></i> Sender OTP</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($acc->status == 'connected')
                                                    <span class="badge badge-success"><i class="fa fa-check-circle me-1"></i> Terhubung</span>
                                                @elseif($acc->status == 'pairing_ready')
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o me-1"></i> Menunggu Scan</span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fa fa-times-circle me-1"></i> Terputus</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(in_array($acc->status, ['pairing_ready', 'qr_ready']))
                                                    <span class="badge badge-light text-dark fs-6 border">Menunggu Scan QR</span>
                                                    <br><small class="text-muted">Klik Scan untuk melihat QR</small>
                                                @elseif($acc->status == 'connected')
                                                    <span class="text-success"><i class="fa fa-check"></i> Selesai</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $acc->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if(in_array($acc->status, ['disconnected', 'pairing_ready', 'qr_ready', 'offline']))
                                                        <a href="{{ route('admin.whatsapp.scan', $acc->id) }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-qrcode"></i> Scan</a>
                                                    @endif
                                                    <form action="{{ route('admin.whatsapp.logout') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus akun ini?');">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $acc->id }}">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada akun WhatsApp yang ditambahkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-muted small">
                            <i class="fa fa-info-circle me-1"></i> Halaman me-refresh otomatis setiap 5 detik jika ada akun berstatus "Menunggu Scan".
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Kirim Pesan -->
            <div class="col-xl-4 col-md-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Uji Coba Kirim Pesan</h5>
                        <span>Test pengiriman pesan ke nomor WhatsApp.</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.whatsapp.test') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="sender">Pilih Akun Pengirim (Opsional)</label>
                                <select class="form-select" id="sender" name="sender">
                                    <option value="">-- Acak / Otomatis --</option>
                                    @foreach($accounts->where('status', 'connected') as $acc)
                                        <option value="{{ $acc->phone_number }}">{{ $acc->phone_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="number">Nomor Tujuan</label>
                                <input class="form-control" id="number" name="number" type="text" placeholder="Contoh: 628123456789" required>
                                <small class="text-muted">Gunakan awalan 62 atau 08.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="message">Pesan Uji Coba</label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Halo, ini pesan percobaan dari SMM Panel." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane me-2"></i>Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountModalLabel">Tambah Akun WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Masukkan nomor WhatsApp yang akan ditambahkan ke daftar. Setelah tersimpan, Anda bisa meminta Kode Tautan.</p>
                    <div class="mb-3">
                        <label class="form-label">Tipe Akun</label>
                        <select class="form-select" name="type" required>
                            <option value="Bot">Bot</option>
                            <option value="Sender OTP">Sender OTP</option>
                        </select>
                        <small class="text-muted">Bot: Untuk pesan umum, Sender OTP: Khusus kirim kode verifikasi</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-3">
                            <label class="form-label">Kode Negara</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                <input class="form-control" name="country_code" type="text" value="62" required>
                            </div>
                        </div>
                        <div class="col-9">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input class="form-control" name="phone_number" type="text" placeholder="81234567890" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Nomor</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    // Auto refresh every 5 seconds to reflect any status change (connect/disconnect)
    setInterval(function() {
        window.location.reload();
    }, 5000);
</script>
@endsection
