@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
@section('content')

<div class="container">
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
        <div class="d-none d-md-flex justify-content-between align-items-center">
             @if(!in_array(Auth::user()->role, ['support', 'admin']))
                <h4><i class="fas fa-ticket"></i> Tvoje tiketi</h4>
                <div class="text-warning mb-2">
                    <a href="{{ route('tickets.create') }}" class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Dodaj ticket"> Novi tiket <i class="fas fa-ticket"></i>
                        </a>
                </div>
            @else
                <h4><i class="fas fa-ticket"></i> Lista otvorenih tiketa</h4>
            @endif
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100">
            @if(!in_array(Auth::user()->role, ['support', 'admin']))
                <h6><i class="fas fa-ticket"></i> Tvoje tiketi</h6>
                <div class="text-warning mb-2">
                    <a href="{{ route('tickets.create') }}" class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Dodaj ticket"> Novi tiket <i class="fas fa-ticket"></i>
                        </a>
                </div>
            @else
                <h4><i class="fas fa-ticket"></i> Lista otvorenih tiketa</h4>
            @endif
        </div>

        <!-- Originalna Tabela za desktop -->
        <div class="d-none d-md-flex">
         <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    @if(in_array(Auth::user()->role, ['support', 'admin']))
                        <th>Korisnik</th>
                    @endif
                    <th>Naslov</th>
                    <th>Kreiran</th>
                    <th>Poslednji odgovor</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Akcija</th>
                </tr>

                @foreach($tickets as $key => $ticket)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        @if(in_array(Auth::user()->role, ['support', 'admin']))
                            <td>{{$ticket->user->firstname.' '.$ticket->user->lastname}}</td>
                        @endif
                        <td>{{ $ticket->title }}</td>
                        <td>{{ ucfirst($ticket->created_at->locale('sr')->diffForHumans()) }}</td>
                        <td>
                            @if ($ticket->responses->isNotEmpty())
                                {{ ucfirst($ticket->responses->last()->created_at->locale('sr')->diffForHumans()) }}
                            @else
                                Nema još uvek odgovora
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : 'danger' }}">
                                @if($ticket->status === 'open')
                                   Otvoren
                                @else
                                   Zatvoren
                                @endif
                            </span>
                        </td>
                       <td class="text-center">
                            <a href="{{ route('tickets.show', $ticket) }}">
                                <button type="button" class="btn btn-sm btn-warning position-relative">
                                    Pogledaj
                                    @php
                                        $unReadAnsw = $ticket->responses->whereNull('read_at')->where('user_id', '!=', auth()->id())->count();
                                        $countResponse = $ticket->responses->count();
                                        $isSupportOrAdmin = in_array(Auth::user()->role, ['support', 'admin']);
                                        $unReadAnsw = ($unReadAnsw === 0 && $countResponse === 0 && $isSupportOrAdmin) ? 1 : $unReadAnsw;
                                    @endphp
                                    <i class="fas fa-eye"></i>
                                    @if($unReadAnsw > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unReadAnsw }}
                                        </span>
                                    @endif
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </thead>
            <tbody>

            </tbody>
        </table>
        <!-- Paginacija -->
        <div class="d-flex justify-content-center pagination-buttons" id="pagination-links">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Mobile & Tablet cards -->
    <div class="d-md-none">
        @foreach($tickets as $key => $ticket)
        <div class="card mb-3 subscription-card" data-id="{{ $ticket->id }}">
            <div class="card-header btn-poslovi-green text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tickets.show', $ticket) }}" class="text-light">
                        <span>{{ $ticket->title }}</span>
                    </a>
                    <span class="badge bg-light text-dark">
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : 'danger' }}">
                            @if($ticket->status === 'open')
                               Otvoren
                            @else
                               Zatvoren
                            @endif
                        </span>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Kreiran</small>
                        <div>{{ ucfirst($ticket->created_at->locale('sr')->diffForHumans()) }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Poslednji odgovor</small>
                        <div>
                            @if ($ticket->responses->isNotEmpty())
                                {{ ucfirst($ticket->responses->last()->created_at->locale('sr')->diffForHumans()) }}
                            @else
                                Nema još uvek odgovora
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('tickets.show', $ticket) }}">
                        <button type="button" class="btn btn-sm btn-warning position-relative">
                            Pogledaj
                            @php
                                $unReadAnsw = $ticket->responses->whereNull('read_at')->where('user_id', '!=', auth()->id())->count();
                                $countResponse = $ticket->responses->count();
                                $isSupportOrAdmin = in_array(Auth::user()->role, ['support', 'admin']);
                                $unReadAnsw = ($unReadAnsw === 0 && $countResponse === 0 && $isSupportOrAdmin) ? 1 : $unReadAnsw;
                            @endphp
                            <i class="fas fa-eye"></i>
                            @if($unReadAnsw > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unReadAnsw }}
                                </span>
                            @endif
                        </button>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
<script type="text/javascript">
    // Poziv funkcije za prevođenje teksta paginacije
    translatePaginationText();

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

    // Funkcija za prevođenje teksta paginacije
    function translatePaginationText() {
        const textElement = document.querySelector("p.text-sm.text-gray-700"); // Selektujemo element koji sadrži tekst
        if (textElement) {
            let text = textElement.textContent.trim();

            // Regex za hvatanje brojeva u stringu
            let matches = text.match(/\d+/g);

            if (matches && matches.length === 3) {
                let translatedText = `Prikazuje od ${matches[0]} do ${matches[1]} od ukupno ${matches[2]} rezultata`;
                textElement.textContent = translatedText;
            }
        }
    }
</script>
@endsection
