@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Multi-Akun WhatsApp</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Admin</li>
        <li class="breadcrumb-item active">WhatsApp</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Data Table Akun WA -->
        <div class="col-xl-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        Daftar Akun WhatsApp
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="fas fa-plus me-1"></i>Tambah Akun
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped text-center align-middle">
                        <thead>
                            <tr>
                                <th>Nomor WA</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Kode Tautan (Pairing)</th>
                                <th>Ditambahkan Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Nomor WA</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Kode Tautan (Pairing)</th>
                                <th>Ditambahkan Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @forelse($accounts as $acc)
                                <tr>
                                    <td class="fw-bold">{{ $acc->phone_number }}</td>
                                    <td>
                                        @if($acc->type == 'Bot')
                                            <span class="badge bg-info text-dark"><i class="fas fa-robot me-1"></i> Bot</span>
                                        @else
                                            <span class="badge bg-primary"><i class="fas fa-key me-1"></i> Sender OTP</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($acc->status == 'connected')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Terhubung</span>
                                        @elseif($acc->status == 'pairing_ready')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Menunggu Scan</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Terputus</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($acc->status, ['pairing_ready', 'qr_ready']))
                                            <span class="badge bg-light text-dark border">Menunggu Scan QR</span>
                                            <br><small class="text-muted">Klik Scan untuk melihat QR</small>
                                        @elseif($acc->status == 'connected')
                                            <span class="text-success"><i class="fas fa-check"></i> Selesai</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $acc->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(in_array($acc->status, ['disconnected', 'pairing_ready', 'qr_ready', 'offline']))
                                                <a href="{{ route('admin.whatsapp.scan', $acc->id) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-qrcode"></i> Scan</a>
                                            @endif
                                            <form action="{{ route('admin.whatsapp.logout') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus akun ini?');">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $acc->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <!-- Empty state is handled by DataTables automatically when using the simple library -->
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3 text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Halaman me-refresh otomatis setiap 5 detik jika ada akun berstatus "Menunggu Scan".
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Kirim Pesan -->
        <div class="col-xl-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-paper-plane me-1"></i>
                    Uji Coba Kirim Pesan
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Test pengiriman pesan ke nomor WhatsApp.</p>
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
                            <div class="form-text">Gunakan awalan 62 atau 08.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="message">Pesan Uji Coba</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Halo, ini pesan percobaan dari SMM Panel." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i> Kirim Pesan</button>
                        </div>
                    </form>
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
                    <p class="text-muted mb-3 small">Masukkan nomor WhatsApp yang akan ditambahkan ke daftar. Setelah tersimpan, Anda bisa meminta Kode Tautan.</p>
                    <div class="mb-3">
                        <label class="form-label">Tipe Akun</label>
                        <select class="form-select" name="type" required>
                            <option value="Bot">Bot</option>
                            <option value="Sender OTP">Sender OTP</option>
                        </select>
                        <div class="form-text">Bot: Untuk pesan umum, Sender OTP: Khusus kirim kode verifikasi</div>
                    </div>
                    <div class="row g-2 mb-3">
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
    // Only applied if there are accounts in 'pairing_ready' or 'qr_ready' status
    @if($accounts->whereIn('status', ['pairing_ready', 'qr_ready'])->count() > 0)
    setInterval(function() {
        window.location.reload();
    }, 5000);
    @endif
</script>
@endsection
