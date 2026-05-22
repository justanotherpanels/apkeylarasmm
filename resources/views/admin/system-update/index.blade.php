@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>System Update</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">System Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Update dari GitHub</h5>
                        <span>Fitur ini akan menjalankan pull code, update dependency, migrate database, dan refresh cache.</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div><strong>Branch:</strong> {{ $branch }}</div>
                            <div><strong>Local Commit:</strong> <code>{{ $localHash }}</code></div>
                            <div><strong>Remote Commit:</strong> <code>{{ $remoteHash }}</code></div>
                            <div>
                                <strong>Status:</strong>
                                @if($hasUpdate)
                                    <span class="badge bg-warning text-dark">Update Available</span>
                                @elseif($check)
                                    <span class="badge bg-success">Up to Date</span>
                                @else
                                    <span class="badge bg-secondary">Belum Check</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.system-update.check') }}">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-refresh me-1"></i> Check Update
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.system-update.run') }}" onsubmit="return confirm('Yakin ingin update sistem sekarang? Proses ini akan menjalankan migration database.');">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-download me-1"></i> Update Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($logs))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Update Logs</h5>
                        </div>
                        <div class="card-body">
                            @foreach($logs as $step)
                                <div class="mb-3 p-2 border rounded">
                                    <div><strong>Command:</strong> <code>{{ $step['command'] ?? '-' }}</code></div>
                                    <div>
                                        <strong>Status:</strong>
                                        @if(!empty($step['successful']))
                                            <span class="badge bg-success">Success</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                        <span class="ms-2">Exit Code: {{ $step['exit_code'] ?? '-' }}</span>
                                    </div>
                                    @if(!empty($step['output']))
                                        <div class="mt-2">
                                            <strong>Output:</strong>
                                            <pre class="bg-light p-2 mb-0" style="max-height: 240px; overflow:auto;">{{ $step['output'] }}</pre>
                                        </div>
                                    @endif
                                    @if(!empty($step['error_output']))
                                        <div class="mt-2">
                                            <strong>Error Output:</strong>
                                            <pre class="bg-light p-2 mb-0 text-danger" style="max-height: 240px; overflow:auto;">{{ $step['error_output'] }}</pre>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

