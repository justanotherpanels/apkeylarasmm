@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Ticket #TCK-{{ $ticket->id }}</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ticket.index') }}">Support Tickets</a></li>
                        <li class="breadcrumb-item active">Ticket #TCK-{{ $ticket->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
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
                            <small class="text-muted">Pengguna: {{ $ticket->user->full_name ?? $ticket->user->username ?? 'Unknown' }} ({{ $ticket->user->email ?? '' }})</small>
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
                            
                            @if($ticket->status != 'Closed')
                                <form action="{{ route('admin.ticket.close', $ticket->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Apakah Anda yakin ingin menutup tiket ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Tutup Tiket</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chat-box">
                            <div class="chat-history chat-right w-100">
                                <ul class="m-b-20">
                                    @foreach($ticket->content as $message)
                                        @if(isset($message['sender']) && $message['sender'] == 'user')
                                            <li class="clearfix">
                                                <div class="message-data text-end"><span class="message-data-time">{{ $message['created_at'] }}</span></div>
                                                <div class="message other-message pull-right bg-light-primary text-dark border-primary">
                                                    <strong>{{ $message['name'] ?? 'User' }} (Pengguna)</strong><br>
                                                    {!! nl2br(e($message['message'])) !!}
                                                </div>
                                            </li>
                                        @else
                                            <li>
                                                <div class="message-data"><span class="message-data-time">{{ $message['created_at'] }}</span></div>
                                                <div class="message my-message">
                                                    <strong>{{ $message['name'] ?? 'Admin' }} (Admin)</strong><br>
                                                    {!! nl2br(e($message['message'])) !!}
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        @if($ticket->status != 'Closed')
                            <hr>
                            <form action="{{ route('admin.ticket.reply', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="message">Balas ke pengguna</label>
                                    <textarea class="form-control" name="message" id="message" rows="5" placeholder="Ketik balasan Anda di sini..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                                </div>
                            </form>
                        @else
                            <hr>
                            <div class="alert alert-secondary text-center">
                                Tiket ini telah ditutup. Tidak bisa menambah balasan baru.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
