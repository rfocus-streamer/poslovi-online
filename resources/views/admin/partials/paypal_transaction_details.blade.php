<div class="row">
    <div class="col-md-6"><strong>ID Transakcije:</strong> {{ $transaction->id }}</div>
    <div class="col-md-6"><strong>Iznos:</strong> {{ number_format($transaction->amount, 2) }} {{ $transaction->currency_code }}</div>
</div>
<div class="row mt-2">
    <div class="col-md-6"><strong>Status:</strong>
        <span class="badge badge-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
            {{ $transaction->status }}
        </span>
    </div>
    <div class="col-md-6"><strong>Datum:</strong> {{ \Carbon\Carbon::parse($transaction->create_time)->format('d.m.Y. H:i') }}</div>
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

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all view-transaction buttons
    document.querySelectorAll('.view-paypal-transaction').forEach(function(button) {
        button.addEventListener('click', function() {
            var transactionId = this.getAttribute('data-id');
            var modal = new bootstrap.Modal(document.getElementById('paypalTransactionModal'));

            // Učitaj detalje transakcije
            fetch('/admin/paypal-transactions/' + transactionId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('paypalTransactionDetails').innerHTML = data.html;
                    } else {
                        document.getElementById('paypalTransactionDetails').innerHTML =
                            '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                    modal.show();
                })
                .catch(error => {
                    document.getElementById('paypalTransactionDetails').innerHTML =
                        '<div class="alert alert-danger">Greška pri učitavanju detalja transakcije.</div>';
                    modal.show();
                });
        });
    });
});
</script>
