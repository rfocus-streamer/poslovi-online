@extends('layouts.app')
<title>Poslovi Online | Poruke</title>
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
@vite(['resources/js/app.js'])
<!-- Ostali meta tagovi i linkovi -->
<meta name="user_id" content="{{ auth()->user()->id }}"> <!-- Promenjeno u user_id -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<style>
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    .blinking-alert {
        animation: blink 1s infinite;
    }
</style>

<div class="container py-5">
    <div class="row">
         <!-- Prikaz poruke sa anchor ID -->
        @if(session('success'))
            <div id="chat-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="chat-message-danger" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="p-3 bg-white border-bottom d-flex align-items-center mb-2">
            <img src="" class="avatar rounded-circle me-3">
            <div>
                <h5 class="mb-0">Aktivni chat</h5>
                <small class="text-muted">Naziv projekta</small>
            </div>
        </div>

        <!-- Glavni sadržaj -->
        <div class="col-md-4">
           <div class="card-body">
                <div class="row">
                    <div class="list-group list-group-flush">
                        @foreach($chats as $chat)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $chat->user->avatar }}" class="avatar rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $chat->user->ime }} {{ $chat->user->prezime }}</h6>
                                        <small class="text-muted">{{ $chat->project->naziv }}</small>
                                        <p class="mb-0 text-muted small text-truncate">{{ $chat->last_message }}</p>
                                    </div>
                                    <small class="text-muted">15:30</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">

                        <!-- Poruke -->
                        <div class="chat-messages flex-grow-1 overflow-auto" style="max-height: 60vh;">
                            @foreach($messages as $message)
                            <div class="d-flex {{ $message->sender->id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                <div class="message-card">
                                    <div class="card {{ $message->sender->id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ $message->sender->avatar }}" class="avatar rounded-circle me-2">
                                                <div>
                                                    <h6 class="mb-0">{{ $message->sender->ime }}</h6>
                                                    <small>{{ $message->created_at->format('H:i') }}</small>
                                                </div>
                                            </div>
                                            <p class="mb-0">{{ $message->content }}</p>
                                            @if($message->attachment)
                                            <div class="mt-2">
                                                <a href="{{ $message->attachment }}" class="text-decoration-none {{ $message->sender->id === auth()->id() ? 'text-white' : 'text-primary' }}">
                                                    <i class="bi bi-paperclip me-1"></i>Prilog
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Forma za slanje poruke -->
                        <div class="p-3 border-top">
                            <form id="messageForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ encrypt($service_id) }}">
                                <input type="hidden" name="user_id" value="{{ encrypt($userId) }}">
                                <div class="border rounded p-2 bg-light">
                                    <div class="mb-2">
                                        <textarea name="content" class="form-control" rows="3" placeholder="Unesite poruku..." required></textarea>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <input type="file" name="attachment" class="form-control form-control-sm w-50">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Pošalji
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
