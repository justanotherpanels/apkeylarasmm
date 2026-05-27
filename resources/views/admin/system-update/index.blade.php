@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">System Update</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Admin</li>
        <li class="breadcrumb-item active">System Update</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @php
        $check = session('update_check', []);
        $branch = $check['branch'] ?? env('APP_UPDATE_BRANCH', 'master');
        $localHash = $check['local_hash'] ?? '-';
        $remoteHash = $check['remote_hash'] ?? '-';
        $hasUpdate = $check['has_update'] ?? false;
        $logs = session('update_logs', []);
    @endphp

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-sync-alt me-1"></i>
            Update dari GitHub
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Fitur ini akan menjalankan pull code, update dependency, migrate database, dan refresh cache.</p>

            <div class="mb-4">
                <div class="mb-2">
                    <strong>Branch:</strong>
                    <span class="ms-1">{{ $branch }}</span>
                </div>
                <div class="mb-2">
                    <strong>Local Commit:</strong>
                    <code class="ms-1">{{ $localHash }}</code>
                </div>
                <div class="mb-2">
                    <strong>Remote Commit:</strong>
                    <code class="ms-1">{{ $remoteHash }}</code>
                </div>
                <div>
                    <strong>Status:</strong>
                    <span class="ms-1">
                        @if($hasUpdate)
                            <span class="badge bg-warning text-dark">Update Available</span>
                        @elseif($check)
                            <span class="badge bg-success">Up to Date</span>
                        @else
                            <span class="badge bg-secondary">Belum Check</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.system-update.check') }}">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-sync-alt me-1"></i> Check Update
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.system-update.run') }}" onsubmit="return confirm('Yakin ingin update sistem sekarang? Proses ini akan menjalankan migration database.');">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Update Now
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(!empty($logs))
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-terminal me-1"></i>
            Update Logs
        </div>
        <div class="card-body">
            @foreach($logs as $step)
            <div class="mb-3 p-3 border rounded">
                <div class="mb-2">
                    <strong>Command:</strong>
                    <code class="ms-1">{{ $step['command'] ?? '-' }}</code>
                </div>
                <div class="mb-2">
                    <strong>Status:</strong>
                    @if(!empty($step['successful']))
                        <span class="badge bg-success ms-1">Success</span>
                    @else
                        <span class="badge bg-danger ms-1">Failed</span>
                    @endif
                    <span class="ms-2 text-muted small">Exit Code: {{ $step['exit_code'] ?? '-' }}</span>
                </div>
                @if(!empty($step['output']))
                <div class="mb-2">
                    <strong>Output:</strong>
                    <pre class="bg-light p-2 mb-0 mt-1 rounded" style="max-height: 240px; overflow: auto;">{{ $step['output'] }}</pre>
                </div>
                @endif
                @if(!empty($step['error_output']))
                <div>
                    <strong>Error Output:</strong>
                    <pre class="bg-light p-2 mb-0 mt-1 rounded text-danger" style="max-height: 240px; overflow: auto;">{{ $step['error_output'] }}</pre>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
