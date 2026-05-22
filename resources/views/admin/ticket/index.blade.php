@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Kelola Support Tickets</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Tickets</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                @if (session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Data Support Tickets</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display" id="basic-1">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Pengguna</th>
                                        <th>Subject</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Update Terakhir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr>
                                        <td>#TCK-{{ $ticket->id }}</td>
                                        <td>{{ $ticket->user->full_name ?? $ticket->user->username ?? 'Unknown' }}</td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->create_at }}</td>
                                        <td>{{ $ticket->update_at }}</td>
                                        <td>
                                            @if($ticket->status == 'Pending')
                                                <span class="badge badge-warning">Pending (Menunggu Admin)</span>
                                            @elseif($ticket->status == 'Reply')
                                                <span class="badge badge-info">Reply (Balasan User)</span>
                                            @elseif($ticket->status == 'Response')
                                                <span class="badge badge-success">Response (Sudah Dibalas)</span>
                                            @elseif($ticket->status == 'Closed')
                                                <span class="badge badge-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.ticket.show', $ticket->id) }}" class="btn btn-primary btn-sm px-2 py-1" title="Lihat/Balas Ticket"><i class="fa fa-eye"></i> Detail</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada support ticket.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
