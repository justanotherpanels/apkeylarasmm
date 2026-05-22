@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Support Tickets</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">Tickets</li>
                    <li class="breadcrumb-item active">Support Tickets</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
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

        <!-- Zero Configuration  Starts-->
        <div class="col-sm-12 col-xl-8">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>Your Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Last Update</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                <tr>
                                    <td>#TCK-{{ $ticket->id }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
                                        @if($ticket->status == 'Pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($ticket->status == 'Reply')
                                            <span class="badge badge-info">Reply</span>
                                        @elseif($ticket->status == 'Response')
                                            <span class="badge badge-success">Response</span>
                                        @elseif($ticket->status == 'Closed')
                                            <span class="badge badge-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->update_at }}</td>
                                    <td>
                                        <a href="{{ route('member.tickets.show', $ticket->id) }}" class="btn btn-primary btn-sm px-2 py-1"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No tickets found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-12 col-xl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Create a Support Ticket</h5>
                </div>
                <form class="theme-form" action="{{ route('member.tickets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="subjectInput">Subject</label>
                            <input class="form-control" name="subject" id="subjectInput" type="text" placeholder="e.g. Deposit Issue, Order Refill" required>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="messageInput">Message</label>
                            <textarea class="form-control" name="message" id="messageInput" rows="5" placeholder="Describe your query in detail..." required></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Zero Configuration  Ends-->
    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
