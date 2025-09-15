<div class="tab-pane {{ $activeTab === 'paypal_transactions' ? 'show active' : '' }}" id="paypal_transactions" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">PayPal Transakcije</h5>
        </div>
        <div class="card-body">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.dashboard') }}">
                <input type="hidden" name="tab" value="paypal_transactions">
                <div class="row mb-3 d-flex align-items-center">
                    <div class="col-auto">
                        <label for="paypal_status">Status</label>
                        <select name="paypal_status" class="form-control">
                            <option value="">Svi statusi</option>
                            <option value="COMPLETED" {{ request('paypal_status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                            <option value="PENDING" {{ request('paypal_status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="FAILED" {{ request('paypal_status') == 'FAILED' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="paypal_transaction_id">ID Transakcije</label>
                        <input type="text" name="paypal_transaction_id" class="form-control"
                               value="{{ request('paypal_transaction_id') }}" placeholder="PAY-XXX">
                    </div>
                    <div class="col-auto">
                        <label for="paypal_customer_email">Email kupca</label>
                        <input type="text" name="paypal_customer_email" class="form-control"
                               value="{{ request('paypal_customer_email') }}" placeholder="email@example.com">
                    </div>
                    <div class="col-auto">
                        <label for="paypal_from_date">Od datuma</label>
                        <input type="date" name="paypal_from_date" class="form-control"
                               value="{{ request('paypal_from_date') }}">
                    </div>
                    <div class="col-auto">
                        <label for="paypal_to_date">Do datuma</label>
                        <input type="date" name="paypal_to_date" class="form-control"
                               value="{{ request('paypal_to_date') }}">
                    </div>
                    <div class="col-auto mt-3">
                        <button type="submit" class="btn btn-primary">Filtriraj</button>
                        <a href="{{ route('admin.dashboard', ['tab' => 'paypal_transactions']) }}" class="btn btn-secondary ml-2">Resetuj filtere</a>
                    </div>
                </div>
            </form>

            {{-- Tabela transakcija --}}
            @if($paypalTransactions && count($paypalTransactions) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Transakcije</th>
                                <th>Iznos</th>
                                <th>Status</th>
                                <th>Kupac</th>
                                <th>Subscription ID</th>
                                <th>Datum</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paypalTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transaction->payer_email }}
                                    </td>
                                    <td>
                                        {{ $transaction->subscription_id ?? 'Nema pretplate' }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y. H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-paypal-transaction"
                                                data-id="{{ $transaction->id }}">
                                            Detalji
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginacija --}}
                {{ $paypalTransactions->appends(request()->input())->links() }}
            @else
                <div class="alert alert-info">
                    Nema PayPal transakcija za odabrane filtere.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal za detalje transakcije -->
    <div class="modal fade" id="paypalTransactionModal" tabindex="-1" role="dialog" aria-labelledby="paypalTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paypalTransactionModalLabel">Detalji PayPal Transakcije</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="paypalTransactionDetails">
                        <!-- Detalji će biti učitani AJAX-om -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
                    document.getElementById('paypalTransactionDetails').innerHTML = data.html;
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
