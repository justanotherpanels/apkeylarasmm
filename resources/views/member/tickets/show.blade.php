@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Ticket #TCK-{{ $ticket->id }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('member.tickets.index') }}">Tickets</a></li>
                    <li class="breadcrumb-item active">Ticket #TCK-{{ $ticket->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            @if(session('success'))
                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ $ticket->subject }}</h5>
                        <span>Ticket Status</span>
                    </div>
                    <div>
                        @if($ticket->status == 'Pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($ticket->status == 'Reply')
                            <span class="badge badge-info">Reply</span>
                        @elseif($ticket->status == 'Response')
                            <span class="badge badge-success">Response</span>
                        @elseif($ticket->status == 'Closed')
                            <span class="badge badge-secondary">Closed</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="chat-box">
                        <div class="chat-history chat-right w-100">
                            <ul class="m-b-20">
                                @foreach($ticket->content as $message)
                                    @if(isset($message['sender']) && $message['sender'] == 'admin')
                                        <li class="clearfix">
                                            <div class="message-data text-end"><span class="message-data-time">{{ $message['created_at'] }}</span></div>
                                            <div class="message other-message pull-right bg-light-primary text-dark border-primary">
                                                <strong>Support Admin</strong><br>
                                                {!! nl2br(e($message['message'])) !!}
                                            </div>
                                        </li>
                                    @else
                                        <li>
                                            <div class="message-data"><span class="message-data-time">{{ $message['created_at'] }}</span></div>
                                            <div class="message my-message">
                                                <strong>{{ $message['name'] ?? 'You' }}</strong><br>
                                                {!! nl2br(e($message['message'])) !!}
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                
                @if($ticket->status != 'Closed')
                    <hr class="m-0">
                    <form action="{{ route('member.tickets.reply', $ticket->id) }}" method="POST" class="theme-form">
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="col-form-label pt-0" for="message">Reply to this ticket</label>
                                <textarea class="form-control" name="message" id="message" rows="4" placeholder="Type your message here..." required></textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit">Send Reply</button>
                            <a href="{{ route('member.tickets.index') }}" class="btn btn-secondary">Back to Tickets</a>
                        </div>
                    </form>
                @else
                    <div class="card-footer text-center">
                        <div class="alert alert-secondary mb-0">
                            This ticket has been closed. You cannot send any more replies.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
