@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Scan WhatsApp</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.whatsapp.index') }}">WhatsApp</a></li>
                        <li class="breadcrumb-item active">Scan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-md-8">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Tautkan Perangkat</h5>
                        <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                    <div class="card-body text-center">
                        <div class="my-3">
                            <h6 class="text-muted mb-1">Nomor WhatsApp:</h6>
                            <h4>+{{ $account->phone_number }}</h4>
                        </div>

                        {{-- Loading state (shown by default, hidden by JS once QR loads) --}}
                        <div id="qr-loading" class="my-5">
                            <i class="fa fa-spin fa-spinner text-primary" style="font-size: 50px;"></i>
                            <h5 class="mt-3">Sedang Menyiapkan QR Code...</h5>
                            <p class="text-muted">Harap tunggu beberapa detik.</p>
                        </div>

                        {{-- QR Code section (hidden until JS injects) --}}
                        <div id="qr-section" class="my-4" style="display:none;">
                            <h5 class="text-muted mb-3">Scan QR Code Berikut:</h5>
                            <div class="p-3 bg-light border rounded d-inline-block">
                                <img id="qr-image" src="" alt="WhatsApp QR Code" class="img-fluid" style="max-width: 250px;" onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='';">
                                <pre id="qr-fallback" class="mt-2 mb-0" style="display:none; font-size: 10px; max-width: 250px; white-space: pre-wrap; word-break: break-all;"></pre>
                            </div>
                            <div class="mt-4 text-start bg-light-info p-3 rounded border">
                                <h6>Cara menghubungkan:</h6>
                                <ol class="text-muted mb-0">
                                    <li>Buka WhatsApp di HP Anda (nomor +{{ $account->phone_number }}).</li>
                                    <li>Ketuk ikon ⋮ (Menu) di pojok kanan atas.</li>
                                    <li>Pilih <strong>Perangkat Tertaut</strong>.</li>
                                    <li>Ketuk <strong>Tautkan Perangkat</strong>.</li>
                                    <li>Arahkan kamera ke QR Code di atas.</li>
                                </ol>
                            </div>
                            <p class="mt-3 text-muted" id="qr-wait-text"><small><i class="fa fa-spin fa-spinner"></i> Menunggu konfirmasi dari HP Anda...</small></p>
                        </div>

                        {{-- Error/expired state --}}
                        <div id="qr-error" class="my-4" style="display:none;">
                            <div class="alert alert-warning mb-3">
                                <i class="fa fa-warning"></i> QR Code kedaluwarsa atau terjadi kesalahan. Klik tombol di bawah untuk memperbarui.
                            </div>
                            <a href="{{ route('admin.whatsapp.scan', $account->id) }}" class="btn btn-primary">
                                <i class="fa fa-refresh me-2"></i> Muat Ulang QR Code
                            </a>
                        </div>

                        {{-- Connected state --}}
                        <div id="qr-connected" class="my-4" style="display:none;">
                            <i class="fa fa-check-circle text-success" style="font-size: 60px;"></i>
                            <h4 class="mt-3 text-success">WhatsApp Terhubung!</h4>
                            <p class="text-muted">Anda akan dialihkan secara otomatis...</p>
                        </div>

                        {{-- Debug panel --}}
                        <div id="debug-panel" class="mt-4 text-start" style="display:none;">
                            <div class="card border-dark">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-1">
                                    <small><strong>Debug Log</strong></small>
                                    <button type="button" class="btn btn-sm btn-outline-light py-0" onclick="document.getElementById('debug-log').innerHTML=''">Clear</button>
                                </div>
                                <div class="card-body p-2">
                                    <pre id="debug-log" class="mb-0" style="font-size: 11px; max-height: 200px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const statusUrl = '{{ route('admin.whatsapp.status', $account->id) }}';
    const indexUrl = '{{ route('admin.whatsapp.index') }}';
    let pollInterval = null;
    let failCount = 0;
    const MAX_FAILS = 30; // after 60 seconds of failures, show error
    let pollCounter = 0;

    // Show debug panel
    document.getElementById('debug-panel').style.display = '';

    function debugLog(msg) {
        const time = new Date().toLocaleTimeString();
        const line = `[${time}] ${msg}`;
        console.log(line);
        const logEl = document.getElementById('debug-log');
        if (logEl) {
            logEl.textContent += line + '\n';
            logEl.scrollTop = logEl.scrollHeight;
        }
    }

    function showState(state) {
        document.getElementById('qr-loading').style.display  = state === 'loading'   ? '' : 'none';
        document.getElementById('qr-section').style.display  = state === 'qr'        ? '' : 'none';
        document.getElementById('qr-error').style.display    = state === 'error'     ? '' : 'none';
        document.getElementById('qr-connected').style.display = state === 'connected' ? '' : 'none';
    }

    function pollStatus() {
        pollCounter++;
        const reqT0 = Date.now();
        debugLog(`Poll #${pollCounter} -> GET ${statusUrl}`);

        fetch(statusUrl)
            .then(res => {
                debugLog(`Poll #${pollCounter} <- HTTP ${res.status} (${Date.now() - reqT0}ms)`);
                return res.json();
            })
            .then(data => {
                failCount = 0;
                debugLog(`Poll #${pollCounter} data: ${JSON.stringify(data)}`);

                if (data.status === 'connected') {
                    debugLog('State -> CONNECTED');
                    showState('connected');
                    clearInterval(pollInterval);
                    setTimeout(() => { window.location.href = indexUrl; }, 1500);

                } else if (data.status === 'qr_ready' && data.qr) {
                    debugLog('State -> QR_READY');
                    debugLog(`QR length: ${data.qr.length}`);
                    
                    // Get elements
                    const img = document.getElementById('qr-image');
                    const fallback = document.getElementById('qr-fallback');
                    const qrSection = document.getElementById('qr-section');
                    const qrLoading = document.getElementById('qr-loading');
                    
                    // Set image source
                    img.onload = function() {
                        debugLog('QR image loaded successfully');
                    };
                    img.onerror = function() {
                        debugLog('QR image failed to load');
                        img.style.display = 'none';
                        if (fallback) fallback.style.display = '';
                    };
                    
                    img.src = data.qr;
                    img.style.display = '';
                    
                    if (fallback) {
                        fallback.textContent = data.qr.substring(0, 80) + '...';
                        fallback.style.display = 'none';
                    }
                    
                    // Force show QR section, hide loading
                    qrSection.style.display = 'block';
                    qrLoading.style.display = 'none';
                    
                    debugLog('QR displayed - check if visible now');
                    showState('qr');

                } else if (data.status === 'disconnected' || data.status === 'offline') {
                    failCount++;
                    debugLog(`State -> ${data.status.toUpperCase()} (failCount=${failCount}/${MAX_FAILS})`);
                    if (failCount >= MAX_FAILS) {
                        debugLog('MAX_FAILS reached! Showing error.');
                        showState('error');
                        clearInterval(pollInterval);
                    }
                } else {
                    debugLog(`State -> ${data.status || 'unknown'} (failCount=${failCount})`);
                }
            })
            .catch(err => {
                failCount++;
                debugLog(`Poll #${pollCounter} ERROR: ${err.message || 'Network error'} (failCount=${failCount}/${MAX_FAILS})`);
                if (failCount >= MAX_FAILS) {
                    debugLog('MAX_FAILS reached! Showing error.');
                    showState('error');
                    clearInterval(pollInterval);
                }
            });
    }

    debugLog('Debug panel initialized. Starting poll...');
    pollStatus();
    pollInterval = setInterval(pollStatus, 2000);
</script>
@endsection
