<div class="row">
    <div class="col-md-6">
        <h5>Osnovne informacije</h5>
        <ul class="list-group">
            <li class="list-group-item">
                <strong>ID:</strong> {{ $payout->id }}
            </li>
            <li class="list-group-item">
                <strong>Korisnik:</strong>
                {{ $payout->user->firstname }} {{ $payout->user->lastname }}
            </li>
            <li class="list-group-item">
                <strong>Iznos:</strong> {{ number_format($payout->amount, 2) }} EUR
            </li>
        </ul>
    </div>

    <div class="col-md-6">
        <h5>Status i datumi</h5>
        <ul class="list-group">
            <li class="list-group-item">
                <strong>Status:</strong>
                @switch($payout->status)
                    @case('requested') <span class="badge bg-warning">Na čekanju</span> @break
                    @case('completed') <span class="badge bg-success">Izvršeno</span> @break
                    @case('rejected') <span class="badge bg-danger">Odbijeno</span> @break
                @endswitch
            </li>
            <li class="list-group-item">
                <strong>Datum zahteva:</strong> {{ $payout->request_date->format('d.m.Y. H:i') }}
            </li>
            <li class="list-group-item">
                <strong>Datum isplate:</strong>
                {{ $payout->payed_date ? $payout->payed_date->format('d.m.Y. H:i') : 'N/A' }}
            </li>
        </ul>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h5>Detalji plaćanja</h5>
        <div class="card">
            <div class="card-body">
                <p><strong>Način plaćanja:</strong> {{ ucfirst($payout->payment_method) }}</p>
                <p><strong>Detalji:</strong> {{ $payout->payment_details }}</p>
                @if($payout->transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $payout->transaction_id }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
