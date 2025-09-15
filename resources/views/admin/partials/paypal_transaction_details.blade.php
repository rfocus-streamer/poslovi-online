<div class="row">
    <div class="col-md-6"><strong>ID Transakcije:</strong> {{ $transaction->id }}</div>
    <div class="col-md-6"><strong>Iznos:</strong> {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</div>
</div>
<div class="row mt-2">
    <div class="col-md-6"><strong>Status:</strong>
        <span class="badge badge-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
            {{ $transaction->status }}
        </span>
    </div>
    <div class="col-md-6"><strong>Datum:</strong> {{ $transaction->created_at->format('d.m.Y. H:i') }}</div>
</div>
<div class="row mt-2">
    <div class="col-md-6"><strong>Email kupca:</strong> {{ $transaction->payer_email }}</div>
    <div class="col-md-6"><strong>Ime kupca:</strong> {{ $transaction->payer_name ?? 'Nepoznato' }}</div>
</div>
@if($transaction->subscription_id)
<div class="row mt-2">
    <div class="col-md-12"><strong>Subscription ID:</strong> {{ $transaction->subscription_id }}</div>
</div>
@endif
<div class="row mt-2">
    <div class="col-md-12"><strong>Opis:</strong> {{ $transaction->description ?? 'Nema opisa' }}</div>
</div>
