<style>
.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: black;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.list-group-item {
    padding: 0.5rem 1rem;
}
</style>

<div class="tab-pane {{ $activeTab === 'stripe_transactions' ? 'show active' : '' }}" id="stripe_transactions" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Stripe Transakcije</h5>
        </div>
        <div class="card-body">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.dashboard') }}">
                <input type="hidden" name="tab" value="stripe_transactions">
                <input type="hidden" name="stripe_page" value="1">
                <div class="row mb-3 d-flex align-items-center">
                    <div class="col-auto">
                        <label for="stripe_status">Status</label>
                        <select name="stripe_status" class="form-control">
                            <option value="">Svi statusi</option>
                            <option value="succeeded" {{ $stripeFilters['status'] == 'succeeded' ? 'selected' : '' }}>Uspešno</option>
                            <option value="pending" {{ $stripeFilters['status'] == 'pending' ? 'selected' : '' }}>Na čekanju</option>
                            <option value="failed" {{ $stripeFilters['status'] == 'failed' ? 'selected' : '' }}>Neuspešno</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="stripe_transaction_id">ID Transakcije</label>
                        <input type="text" name="stripe_transaction_id" class="form-control"
                               value="{{ $stripeFilters['transaction_id'] }}" placeholder="ch_xxx">
                    </div>
                    <div class="col-auto">
                        <label for="stripe_customer_email">Email kupca</label>
                        <input type="text" name="stripe_customer_email" class="form-control"
                               value="{{ $stripeFilters['customer_email'] }}" placeholder="email@example.com">
                    </div>
                    <div class="col-auto">
                        <label for="stripe_from_date">Od datuma</label>
                        <input type="date" name="stripe_from_date" class="form-control"
                               value="{{ $stripeFilters['from_date'] }}">
                    </div>
                    <div class="col-auto">
                        <label for="stripe_to_date">Do datuma</label>
                        <input type="date" name="stripe_to_date" class="form-control"
                               value="{{ $stripeFilters['to_date'] }}">
                    </div>
                    <div class="col-auto mt-3">
                        <button type="submit" class="btn btn-primary">Filtriraj</button>
                        <a href="{{ route('admin.dashboard', ['tab' => 'stripe_transactions']) }}" class="btn btn-secondary ml-2">Resetuj filtere</a>
                    </div>
                </div>
            </form>

            {{-- Tabela transakcija --}}
            @if($stripeTransactions && count($stripeTransactions->data) > 0)
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
                            @foreach($stripeTransactions->data as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ number_format($transaction->amount / 100, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->status === 'succeeded' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ $transaction->status === 'succeeded' ? 'Uspešno' : ($transaction->status === 'pending' ? 'Na čekanju' : 'Neuspešno') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->customer)
                                            {{ $transaction->customer->name }} ({{ $transaction->customer->email }})
                                        @else
                                            Nepoznato
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->invoice->subscription))
                                            {{ $transaction->invoice->subscription->id }}
                                        @else
                                            Nema pretplate
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp($transaction->created)->format('d.m.Y. H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-transaction"
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
                <nav>
                    <ul class="pagination">
                        @if($stripePagination['current_page'] > 1)
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ route('admin.dashboard', array_merge(['tab' => 'stripe_transactions'], $stripeFilters, ['stripe_page' => $stripePagination['current_page'] - 1, 'ending_before' => $stripeTransactions->data[0]->id])) }}">
                                    Prethodna
                                </a>
                            </li>
                        @endif

                        <li class="page-item disabled">
                            <span class="page-link">Strana {{ $stripePagination['current_page'] }}</span>
                        </li>

                        @if($stripePagination['has_more'])
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ route('admin.dashboard', array_merge(['tab' => 'stripe_transactions'], $stripeFilters, ['stripe_page' => $stripePagination['current_page'] + 1, 'starting_after' => end($stripeTransactions->data)->id])) }}">
                                    Sledeća
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            @else
                <div class="alert alert-info">
                    @if(!empty($stripeFilters['status']) || !empty($stripeFilters['transaction_id']) || !empty($stripeFilters['customer_email']) || !empty($stripeFilters['from_date']) || !empty($stripeFilters['to_date']))
                        Nema Stripe transakcija za odabrane filtere.
                    @else
                        Nema Stripe transakcija u sistemu.
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal za detalje transakcije -->
    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Detalji Transakcije</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="transactionDetails">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Učitavanje...</span>
                            </div>
                            <p>Učitavanje detalja transakcije...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all view-transaction buttons
    document.querySelectorAll('.view-transaction').forEach(function(button) {
        button.addEventListener('click', function() {
            var transactionId = this.getAttribute('data-id');
            var modal = document.getElementById('transactionModal');
            var details = document.getElementById('transactionDetails');

            // Show loading state
            details.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Učitavanje...</span></div><p>Učitavanje detalja transakcije...</p></div>';

            // Show modal using Bootstrap's vanilla JS API
            var bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();

            // Make API request with vanilla JS fetch
            fetch('{{ route("admin.stripe.transaction.details", "") }}/' + transactionId)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        console.log(data);
                        var transaction = data.transaction;
                        var html = '<div class="row">';
                        html += '<div class="col-md-6"><strong>ID Transakcije:</strong> ' + transaction.id + '</div>';
                        html += '<div class="col-md-6"><strong>Iznos:</strong> ' + transaction.amount + ' ' + transaction.currency + '</div>';
                        html += '</div><div class="row mt-2">';
                        html += '<div class="col-md-6"><strong>Status:</strong> <span class="badge badge-' + (transaction.status === 'succeeded' ? 'success' : (transaction.status === 'pending' ? 'warning' : 'danger')) + '">' + (transaction.status === 'succeeded' ? 'Uspešno' : (transaction.status === 'pending' ? 'Na čekanju' : 'Neuspešno')) + '</span></div>';
                        html += '<div class="col-md-6"><strong>Datum:</strong> ' + transaction.created + '</div>';
                        html += '</div><div class="row mt-2">';
                        html += '<div class="col-md-6"><strong>Način plaćanja:</strong> ' + transaction.payment_method + '</div>';
                        html += '<div class="col-md-6"><strong>Opis:</strong> ' + transaction.description + '</div>';
                        html += '</div>';

                        if (transaction.customer) {
                            html += '<div class="row mt-2">';
                            html += '<div class="col-md-12"><strong>Kupac:</strong> ' + transaction.customer.name + ' (' + transaction.customer.email + ')</div>';
                            html += '</div>';
                        }

                        if (transaction.invoice) {
                            html += '<div class="row mt-2">';
                            html += '<div class="col-md-12"><strong>Faktura:</strong> ' + transaction.invoice.number;
                            if (transaction.invoice.pdf_url) {
                                html += ' <a href="' + transaction.invoice.pdf_url + '" target="_blank" class="btn btn-sm btn-primary">Preuzmi PDF</a>';
                            }
                            html += '</div></div>';

                            if (transaction.invoice.subscription) {
                                html += '<div class="row mt-2">';
                                html += '<div class="col-md-12"><strong>Pretplata:</strong> ' + transaction.invoice.subscription.id;
                                html += '<br><strong>Status pretplate:</strong> <span class="badge badge-' + (transaction.invoice.subscription.status === 'active' ? 'success' : 'secondary') + '">' + transaction.invoice.subscription.status + '</span>';
                                html += '<br><strong>Period:</strong> ' + transaction.invoice.subscription.current_period_start + ' - ' + transaction.invoice.subscription.current_period_end;
                                html += '</div></div>';
                            }

                            if (transaction.invoice.lines && transaction.invoice.lines.length > 0) {
                                html += '<div class="row mt-2">';
                                html += '<div class="col-md-12"><strong>Stavke fakture:</strong><ul class="list-group">';
                                transaction.invoice.lines.forEach(function(line) {
                                    html += '<li class="list-group-item">' + line.description + ' - ' + line.amount + ' ' + line.currency;
                                    if (line.period) {
                                        html += ' (' + line.period.start + ' - ' + line.period.end + ')';
                                    }
                                    html += '</li>';
                                });
                                html += '</ul></div></div>';
                            }
                        }

                        details.innerHTML = html;
                    } else {
                        details.innerHTML = '<div class="alert alert-danger">Greška pri učitavanju detalja transakcije: ' + data.message + '</div>';
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    details.innerHTML = '<div class="alert alert-danger">Greška pri učitavanju detalja transakcije.</div>';
                });
        });
    });
});
</script>
