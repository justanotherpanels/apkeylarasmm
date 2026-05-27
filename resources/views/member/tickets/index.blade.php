@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Support Tickets</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Tickets</li>
        <li class="breadcrumb-item active">Support Tickets</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Tickets List (Size 7) -->
        <div class="col-lg-7 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Your Tickets
                </div>
                <div class="card-body">
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td><strong class="text-primary">#TCK-{{ $ticket->id }}</strong></td>
                                <td>{{ $ticket->subject }}</td>
                                <td>
                                    @if($ticket->status == 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($ticket->status == 'Reply')
                                        <span class="badge bg-info text-dark">Reply</span>
                                    @elseif($ticket->status == 'Response')
                                        <span class="badge bg-success">Response</span>
                                    @elseif($ticket->status == 'Closed')
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->update_at }}</td>
                                <td>
                                    <a href="{{ route('member.tickets.show', $ticket->id) }}" class="btn btn-primary btn-sm px-2 py-1">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Create Ticket Form (Size 5) -->
        <div class="col-lg-5 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus me-1"></i> Create a Support Ticket
                </div>
                <form action="{{ route('member.tickets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="subjectInput">Subject</label>
                            <input class="form-control" name="subject" id="subjectInput" type="text" placeholder="e.g. Deposit Issue, Order Refill" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="messageInput">Message</label>
                            <textarea class="form-control" name="message" id="messageInput" rows="5" placeholder="Describe your query in detail..." required></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-transparent border-0 pt-0">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
