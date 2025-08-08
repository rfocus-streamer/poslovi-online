<div class="tab-pane fade {{ $activeTab === 'transactions' ? 'show active' : '' }}" id="transactions">
    <h2 class="mb-4">Transakcije ({{ $transactions->total() }})</h2>

    <!-- Pretraga i filteri -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="transactions">
                <div class="row">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text"
                                class="form-control"
                                name="transactions_search"
                                placeholder="Pretraži transakcije (korisnik, iznos, ID)..."
                                value="{{ request('transactions_search') }}"
                                id="transactionsSearchInput">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('transactions_search') || request('transactions_status'))
                                    <a href="?tab=transactions" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        @php
                            $baseParams = request()->except(['transactions_status']);
                            $baseParams['tab'] = 'transactions';
                        @endphp
                        <div class="btn-group w-100">
                            <a href="?{{ http_build_query(array_merge($baseParams, ['transactions_status' => 'completed'])) }}"
                                class="btn btn-outline-success {{ request('transactions_status') === 'completed' ? 'active' : '' }}">Završene</a>
                            <a href="?{{ http_build_query(array_merge($baseParams, ['transactions_status' => 'pending'])) }}"
                                class="btn btn-outline-warning {{ request('transactions_status') === 'pending' ? 'active' : '' }}">Na čekanju</a>
                            <a href="?{{ http_build_query(array_merge($baseParams, ['transactions_status' => 'failed'])) }}"
                                                       class="btn btn-outline-danger {{ request('transactions_status') === 'failed' ? 'active' : '' }}">Neuspešne</a>
                            <a href="?{{ http_build_query($baseParams) }}"
                                class="btn btn-outline-dark {{ !request('transactions_status') ? 'active' : '' }}">Sve
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['transactions_sort_column', 'transactions_sort_direction']), [
                            'tab' => 'transactions',
                            'transactions_sort_column' => 'id',
                            'transactions_sort_direction' => request('transactions_sort_column') == 'id' && request('transactions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">ID
                            @if(request('transactions_sort_column') == 'id')
                                <i class="fas fa-arrow-{{ request('transactions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Korisnik</th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['transactions_sort_column', 'transactions_sort_direction']), [
                            'tab' => 'transactions',
                            'transactions_sort_column' => 'amount',
                            'transactions_sort_direction' => request('transactions_sort_column') == 'amount' && request('transactions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Iznos
                            @if(request('transactions_sort_column') == 'amount')
                                <i class="fas fa-arrow-{{ request('transactions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['transactions_sort_column', 'transactions_sort_direction']), [
                            'tab' => 'transactions',
                            'transactions_sort_column' => 'payment_method',
                            'transactions_sort_direction' => request('transactions_sort_column') == 'payment_method' && request('transactions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Način plaćanja
                            @if(request('transactions_sort_column') == 'payment_method')
                                <i class="fas fa-arrow-{{ request('transactions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['transactions_sort_column', 'transactions_sort_direction']), [
                            'tab' => 'transactions',
                            'transactions_sort_column' => 'status',
                            'transactions_sort_direction' => request('transactions_sort_column') == 'status' && request('transactions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Status
                            @if(request('transactions_sort_column') == 'status')
                                <i class="fas fa-arrow-{{ request('transactions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['transactions_sort_column', 'transactions_sort_direction']), [
                            'tab' => 'transactions',
                            'transactions_sort_column' => 'created_at',
                            'transactions_sort_direction' => request('transactions_sort_column') == 'created_at' && request('transactions_sort_direction') == 'desc' ? 'asc' : 'desc'
                                            ])) }}" class="text-white text-decoration-none">Datum
                            @if(request('transactions_sort_column') == 'created_at')
                                <i class="fas fa-arrow-{{ request('transactions_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>
                    <a href="?tab=users&users_search={{ urlencode($transaction->user->email) }}"
                        class="text-decoration-none d-flex align-items-center gap-2"
                        title="Prikaži korisnika">
                        <img src="{{ $transaction->user->avatar ? Storage::url('user/' . $transaction->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="30" height="30" class="rounded-circle">
                        <span>{{ $transaction->user->firstname }} {{ $transaction->user->lastname }}</span>
                    </a>
                </td>
                <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                <td>{{ ucfirst($transaction->payment_method) }}</td>
                <td>
                    @php
                        $statusColors = [
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger'
                                        ];
                        $color = $statusColors[$transaction->status] ?? 'primary';
                    @endphp
                    <span class="badge bg-{{ $color }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
                <td>
                    @if($transaction->created_at)
                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y. H:i') }}
                    @else
                        Nije postavljeno
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $transactions->onEachSide(1)->appends([
                'tab' => 'transactions',
                'transactions_search' => request('transactions_search'),
                'transactions_status' => request('transactions_status'),
                'transactions_sort_column' => request('transactions_sort_column'),
                'transactions_sort_direction' => request('transactions_sort_direction')
                ])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal za detalje transakcije -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalji transakcije #<span id="transactionId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetailsContent">
                Učitavanje...
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal('#transactionDetailsModal');

    // Otvaranje detalja transakcije
    document.querySelectorAll('[data-bs-target="#transactionDetailsModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            document.getElementById('transactionId').textContent = transactionId;

            // AJAX za dobavljanje detalja
            fetch(`/admin/transactions/${transactionId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('transactionDetailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Korisnik:</strong> ${data.user_name}</p>
                                <p><strong>Iznos:</strong> ${data.amount} ${data.currency}</p>
                                <p><strong>Način plaćanja:</strong> ${data.payment_method}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.status}</span></p>
                                <p><strong>Datum:</strong> ${data.created_at}</p>
                                <p><strong>Transakcija ID:</strong> ${data.transaction_id}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6>Detalji:</h6>
                            <pre class="bg-light p-3">${JSON.stringify(JSON.parse(data.payload), null, 2)}</pre>
                        </div>
                    `;
                })
                .catch(() => {
                    document.getElementById('transactionDetailsContent').innerHTML =
                        `<div class="alert alert-danger">Došlo je do greške pri učitavanju detalja</div>`;
                });
        });
    });
});
</script>
