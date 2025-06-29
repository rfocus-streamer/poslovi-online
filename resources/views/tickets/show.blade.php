@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Prikaz poruka -->
        @if(session('success'))
            <div id="ticket-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="ticket-message-danger" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Desktop naslov + info -->
        <div class="d-none d-md-block ml-1 mt-1 mb-1">
            <h4 class="mb-0"><i class="fas fa-ticket"></i> {{ $ticket->title }}</h4>
                <small class="text-muted">
                    Kreiran {{ $ticket->user->name }} |
                    {{ $ticket->created_at->format('d.m.Y H:i') }}
                </small>
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100">
            <h6 class="mb-0"><i class="fas fa-ticket"></i> {{ $ticket->title }}</h6>
                <small class="text-muted">
                    Kreiran {{ $ticket->user->name }} |
                    {{ $ticket->created_at->format('d.m.Y H:i') }}
                </small>
        </div>

        <!-- Desktop -->
        <!-- Leva kolona sa detaljima ticketa -->
        <div class="d-none d-md-block col-md-8 mb-3 g-0">
            <div class="card">
                <div class="card-body">

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($ticket->user->avatar)
                                <img src="{{ Storage::url('user/' . $ticket->user->avatar) }}"
                                     class="rounded-circle me-2"
                                     alt="Avatar"
                                     width="60"
                                     height="60">
                                @endif
                                <div>
                                    <strong>{{ $ticket->user->firstname.' '. $ticket->user->lasttname}}</strong><br>
                                    <small class="text-muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>

                            <p class="mb-2">{{ $ticket->description }}</p>

                            @if($ticket->attachment)
                            <div class="mt-2">
                                <a href="{{ Storage::url($ticket->attachment) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-link">
                                    <i class="fas fa-paperclip"></i> Preuzmi originalni prilog
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <h5 class="mb-3"><i class="fas fa-comments"></i> Odgovori ({{ $ticket->responses->count() }})</h5>

                    <!-- Lista odgovora -->
                    @foreach($ticket->responses as $response)
                    <div class="card mb-3 ticket-response"
                    data-response-id="{{ $response->id }}"
                    data-is-unread="{{ $response->isUnread() && $response->user_id !== auth()->id() ? 'true' : 'false' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($response->user->avatar)
                                <img src="{{ Storage::url('user/' . $response->user->avatar) }}"
                                     class="rounded-circle me-2"
                                     alt="Avatar"
                                     width="40"
                                     height="40">
                                @endif
                                <div>
                                    <small class="text-muted">
                                        @if($response->user->role === 'support')
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                            <span class="badge bg-info ms-2">Podrška</span>
                                        @elseif($response->user->role === 'admin')
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                            <span class="badge bg-info">Administrator</span>
                                        @else
                                            {{$response->user->firstname.' '.$response->user->lastname}}<br>
                                            {{ $response->created_at->format('d.m.Y H:i') }}
                                        @endif

                                        @if($response->isUnread() && $response->user_id === auth()->id())
                                           <i class="fas fa-check-double text-secondary" title="Nepročitano"></i>
                                        @elseif(!$response->isUnread() && $response->user_id === auth()->id())
                                            <i class="fas fa-check-double text-success" title="Pročitano {{ \Carbon\Carbon::parse($response->read_at)->format('d.m.Y H:i') }}"></i>
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

        <!-- Mobile Version (Kartice) -->
        <div class="d-md-none mb-1">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($ticket->user->avatar)
                        <img src="{{ Storage::url('user/' . $ticket->user->avatar) }}"
                             class="rounded-circle me-2"
                             alt="Avatar"
                             width="60"
                             height="60">
                        @endif
                        <div>
                            <strong>{{ $ticket->user->firstname.' '. $ticket->user->lastname }}</strong><br>
                            <small class="text-muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>

                    <p class="mb-2">{{ $ticket->description }}</p>

                    @if($ticket->attachment)
                    <div class="mt-2">
                        <a href="{{ Storage::url($ticket->attachment) }}"
                           target="_blank"
                           class="btn btn-sm btn-link">
                            <i class="fas fa-paperclip"></i> Preuzmi originalni prilog
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <h5 class="mb-3"><i class="fas fa-comments"></i> Odgovori ({{ $ticket->responses->count() }})</h5>

            <!-- Lista odgovora kao kartice -->
            @foreach($ticket->responses as $response)
            <div class="card mb-3 ticket-response"
                 data-response-id="{{ $response->id }}"
                 data-is-unread="{{ $response->isUnread() && $response->user_id !== auth()->id() ? 'true' : 'false' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($response->user->avatar)
                        <img src="{{ Storage::url('user/' . $response->user->avatar) }}"
                             class="rounded-circle me-2"
                             alt="Avatar"
                             width="40"
                             height="40">
                        @endif
                        <div>
                            <small class="text-muted">
                                @if($response->user->role === 'support')
                                {{ $response->created_at->format('d.m.Y H:i') }}
                                <span class="badge bg-info ms-2">Podrška</span>
                                @elseif($response->user->role === 'admin')
                                {{ $response->created_at->format('d.m.Y H:i') }}
                                <span class="badge bg-info">Administrator</span>
                                @else
                                {{$response->user->firstname.' '.$response->user->lastname}}<br>
                                {{ $response->created_at->format('d.m.Y H:i') }}
                                @endif

                                @if($response->isUnread() && $response->user_id === auth()->id())
                                <i class="fas fa-check-double text-secondary" title="Nepročitano"></i>
                                @elseif(!$response->isUnread() && $response->user_id === auth()->id())
                                <i class="fas fa-check-double text-success" title="Pročitano {{ \Carbon\Carbon::parse($response->read_at)->format('d.m.Y H:i') }}"></i>
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
<script type="text/javascript">
    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('ticket-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('ticket-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const responseElements = document.querySelectorAll('.ticket-response[data-is-unread="true"]');

    if (responseElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && document.visibilityState === 'visible') {
                    const responseId = entry.target.dataset.responseId;

                    fetch(`/tickets/responses/${responseId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                    });
                }
            });
        }, {
            threshold: 0.5, // 50% vidljivosti
            rootMargin: '0px 0px -50px 0px' // Malo pomera granicu
        });

        responseElements.forEach(el => observer.observe(el));
    }
});
</script>
@endsection
