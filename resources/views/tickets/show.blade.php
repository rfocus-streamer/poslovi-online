@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Leva kolona sa detaljima ticketa -->
        <div class="col-md-8 mb-3 g-0">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        @if($ticket->user->avatar)
                        <img src="{{ Storage::url('user/' . Auth::user()->avatar) }}"
                             class="rounded-circle me-3"
                             alt="Avatar"
                             width="60"
                             height="60">
                        @endif
                        <strong>{{Auth::user()->firstname.' '.Auth::user()->lastname}}</strong>
                    </div>

                     <div class="mt-1">
                            <h4 class="mb-0">{{ $ticket->title }}</h4>
                            <small class="text-muted">
                                Otvoren: {{ $ticket->user->name }} |
                                {{ $ticket->created_at->format('d.m.Y H:i') }}
                            </small>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <p>{{ $ticket->description }}</p>

                            @if($ticket->attachment)
                            <div class="mt-3">
                                <a href="{{ Storage::url($ticket->attachment) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> Preuzmi originalni prilog
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <h5 class="mb-3"><i class="fas fa-comments"></i> Odgovori ({{ $ticket->responses->count() }})</h5>

                    <!-- Lista odgovora -->
                    @foreach($ticket->responses as $response)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($response->user->avatar)
                                <img src="{{ Storage::url('user/' . Auth::user()->avatar) }}"
                                     class="rounded-circle me-2"
                                     alt="Avatar"
                                     width="40"
                                     height="40">
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $response->user->name }}</h6>
                                    <small class="text-muted">
                                        @if($response->user->role === 'support')
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                            <span class="badge bg-info ms-2">Podrška</span>
                                        @elseif($response->user->role === 'admin')
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                            <span class="badge bg-info">Administrator</span>
                                        @else
                                            {{Auth::user()->firstname.' '.Auth::user()->lastname}}<br>
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <p class="mb-2">{{ $response->content }}</p>

                            @if($response->attachment)
                            <div class="mt-2">
                                <a href="{{ Storage::url($response->attachment) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-link">
                                    <i class="fas fa-paperclip"></i> Prilog uz odgovor
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <!-- Forma za novi odgovor -->
                    @if($ticket->status !== 'closed')
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-reply"></i> Dodaj odgovor</h6>
                            <form method="POST" action="{{ route('tickets.responses.store', $ticket) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control"
                                              name="content"
                                              rows="4"
                                              placeholder="Unesite tekst odgovora..."
                                              required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Prilog (opcionalno)</label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>
                                <button type="submit" class="btn btn-success w-100" style="background-color: #198754">
                                    <i class="fas fa-paper-plane"></i> Pošalji odgovor
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Desna kolona sa informacijama i akcijama -->
        <div class="col-md-4 mb-3 g-0">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">
                        <i class="fas fa-info-circle"></i> Status tiketa
                    </h6>

                    <div class="mb-4 d-flex align-items-center gap-2">
                        <p class="mb-0"><strong>Status:</strong></p>
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : 'danger' }}">
                            @if($ticket->status === 'open')
                               Otvoren
                            @else
                               Zatvoren
                            @endif
                        </span>
                    </div>

                    <div class="mb-4 d-flex align-items-center gap-2">
                        <p class="mb-0"><strong>Dodeljen timu:</strong></p>
                        @if($ticket->assigned_team === 'support')
                            <span class="badge bg-info">Podrška</span>
                        @else
                            <span class="badge bg-info">Administrator</span>
                        @endif
                    </div>

                    @if(auth()->user()->role === 'support' or auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <h6><i class="fas fa-cogs"></i> Akcije</h6>
                        <form method="POST" action="{{ route('tickets.redirect', $ticket) }}" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <select name="team" class="form-select">
                                    <option value="support" {{ $ticket->assigned_team === 'support' ? 'selected' : '' }}>Support</option>
                                    <option value="admin" {{ $ticket->assigned_team === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-redo-alt"></i> Preusmeri
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                            @csrf
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Otvoren</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Zatvoren</option>
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Ažuriraj
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <div class="mt-4">
                        <h6><i class="fas fa-history"></i> Istorija</h6>
                        <ul class="list-unstyled">
                            <li><small>Kreiran: {{ $ticket->created_at->format('d.m.Y H:i') }}</small></li>

                            @if($ticket->responses->isNotEmpty())
                                <li><small>Poslednja aktivnost:
                                    {{$ticket->responses->last()->created_at->format('d.m.Y H:i')}}
                                </small></li>
                            @elseif($ticket->updated_at != $ticket->created_at)
                                <li><small>Poslednja izmena: {{ $ticket->updated_at->format('d.m.Y H:i') }}</small></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
