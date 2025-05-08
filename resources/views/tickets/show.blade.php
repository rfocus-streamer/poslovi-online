@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $ticket->title }}</h2>
    <p>Status: {{ $ticket->status }}</p>
    <p>Kreiran: {{ $ticket->created_at->format('d.m.Y. H:i') }}</p>

    <div class="card mb-3">
        <div class="card-body">
            {{ $ticket->description }}
        </div>
    </div>

    @if($ticket->attachment)
    <div class="mb-3">
        <h4>Prilog:</h4>
        <a href="{{ Storage::url($ticket->attachment) }}" target="_blank" class="btn btn-outline-secondary">
            Preuzmi prilog
        </a>
    </div>
    @endif
</div>
@endsection
