@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Support Tickets</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Support Tickets</li>
    </ol>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Data Support Tickets
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
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
                <tfoot>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Pengguna</th>
                        <th>Subject</th>
                        <th>Tanggal Dibuat</th>
                        <th>Update Terakhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>#TCK-{{ $ticket->id }}</td>
                        <td>{{ $ticket->user->full_name ?? $ticket->user->username ?? 'Unknown' }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->create_at }}</td>
                        <td>{{ $ticket->update_at }}</td>
                        <td>
                            @if($ticket->status == 'Pending')
                                <span class="badge bg-warning text-dark">Pending (Menunggu Admin)</span>
                            @elseif($ticket->status == 'Reply')
                                <span class="badge bg-info text-dark">Reply (Balasan User)</span>
                            @elseif($ticket->status == 'Response')
                                <span class="badge bg-success">Response (Sudah Dibalas)</span>
                            @elseif($ticket->status == 'Closed')
                                <span class="badge bg-secondary">Closed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.ticket.show', $ticket->id) }}" class="btn btn-primary btn-sm px-2 py-1" title="Lihat/Balas Ticket"><i class="fas fa-eye"></i> Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
