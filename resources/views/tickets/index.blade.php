@extends('layouts.app')

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

    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-ticket"></i> Tvoje tiketi</h4>

        <div class="text-warning mb-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Dodaj ticket"> Novi tiket <i class="fas fa-ticket"></i>
                </a>
        </div>
    </div>
         <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Naslov</th>
                    <th>Kreiran</th>
                    <th>Poslednji odgovor</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Akcija</th>
                </tr>

                @foreach($tickets as $key => $ticket)
                    <tr>
                        <td>{{ $key + 1 }}</td>
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
                        <td class="text-center"> <a href="{{ route('tickets.show', $ticket) }}"><button type="button" class="btn btn-sm btn-warning">Pogledaj <i class="fas fas fa-eye"></i></button></a></td>
                    </tr>
                @endforeach
            </thead>
            <tbody>

            </tbody>
        </table>
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
@endsection
