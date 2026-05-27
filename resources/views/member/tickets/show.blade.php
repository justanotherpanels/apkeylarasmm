@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ticket #TCK-{{ $ticket->id }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('member.tickets.index') }}">Tickets</a></li>
        <li class="breadcrumb-item active">Ticket #TCK-{{ $ticket->id }}</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-envelope-open-text me-1"></i> <strong class="text-dark">{{ $ticket->subject }}</strong>
                    </div>
                    <div>
                        @if($ticket->status == 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($ticket->status == 'Reply')
                            <span class="badge bg-info text-dark">Reply</span>
                        @elseif($ticket->status == 'Response')
                            <span class="badge bg-success">Response</span>
                        @elseif($ticket->status == 'Closed')
                            <span class="badge bg-secondary">Closed</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body bg-light" style="max-height: 600px; overflow-y: auto;">
                    <div class="d-flex flex-column gap-3">
                        @foreach($ticket->content as $message)
                            @if(isset($message['sender']) && $message['sender'] == 'admin')
                                <div class="card bg-white border-start border-primary border-4 col-md-9 align-self-start shadow-sm">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-primary fw-bold"><i class="fas fa-user-shield me-1"></i> Support Admin</span>
                                            <small class="text-muted">{{ $message['created_at'] }}</small>
                                        </div>
                                        <div class="text-dark">
                                            {!! nl2br(e($message['message'])) !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card bg-success-subtle border-start border-success border-4 col-md-9 align-self-end shadow-sm">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-success fw-bold"><i class="fas fa-user me-1"></i> {{ $message['name'] ?? 'You' }}</span>
                                            <small class="text-muted">{{ $message['created_at'] }}</small>
                                        </div>
                                        <div class="text-dark">
                                            {!! nl2br(e($message['message'])) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                @if($ticket->status != 'Closed')
                    <div class="card-footer">
                        <form action="{{ route('member.tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="message">Reply to this ticket</label>
                                <textarea class="form-control" name="message" id="message" rows="4" placeholder="Type your message here..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-reply me-1"></i> Send Reply</button>
                                <a href="{{ route('member.tickets.index') }}" class="btn btn-secondary">Back to Tickets</a>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card-footer text-center">
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-lock me-1"></i> This ticket has been closed. You cannot send any more replies.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
