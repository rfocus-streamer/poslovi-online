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
                                <span class="d-none d-md-inline">Pretraži</span>
                            </button>
                            @if(request('transactions_search') || request('transactions_status'))
                                <a href="?tab=transactions" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                    <span class="d-none d-md-inline">Reset</span>
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
                                class="btn btn-outline-success {{ request('transactions_status') === 'completed' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Završene</span>
                                <span class="d-md-none">Z</span>
                            </a>
                            <a href="?{{ http_build_query(array_merge($baseParams, ['transactions_status' => 'pending'])) }}"
                                class="btn btn-outline-warning {{ request('transactions_status') === 'pending' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Na čekanju</span>
                                <span class="d-md-none">Č</span>
                            </a>
                            <a href="?{{ http_build_query(array_merge($baseParams, ['transactions_status' => 'failed'])) }}"
                                class="btn btn-outline-danger {{ request('transactions_status') === 'failed' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Neuspešne</span>
                                <span class="d-md-none">N</span>
                            </a>
                            <a href="?{{ http_build_query($baseParams) }}"
                                class="btn btn-outline-dark {{ !request('transactions_status') ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Sve</span>
                                <span class="d-md-none">S</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark d-md-table-header-group">
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
                    <!-- Desktop view -->
                    <td class="d-none d-md-table-cell">{{ $transaction->id }}</td>
                    <td class="d-none d-md-table-cell">
                        <a href="?tab=users&users_search={{ urlencode($transaction->user->email) }}"
                            class="text-decoration-none d-flex align-items-center gap-2"
                            title="Prikaži korisnika">
                            <img src="{{ $transaction->user->avatar ? Storage::url('user/' . $transaction->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="30" height="30" class="rounded-circle">
                            <span>{{ $transaction->user->firstname }} {{ $transaction->user->lastname }}</span>
                        </a>
                    </td>
                    <td class="d-none d-md-table-cell">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                    <td class="d-none d-md-table-cell">{{ ucfirst($transaction->payment_method) }}</td>
                    <td class="d-none d-md-table-cell">
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
                    <td class="d-none d-md-table-cell">
                        @if($transaction->created_at)
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y. H:i') }}
                        @else
                            Nije postavljeno
                        @endif
                    </td>

                    <!-- Mobile view -->
                    <td class="d-md-none">
                        <div class="mobile-transaction-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>ID: {{ $transaction->id }}</strong>
                                    <div class="small text-muted">
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y. H:i') }}
                                    </div>
                                </div>
                                <div>
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
                                </div>
                            </div>

                            <div class="mb-2">
                                <a href="?tab=users&users_search={{ urlencode($transaction->user->email) }}"
                                    class="text-decoration-none d-flex align-items-center gap-2"
                                    title="Prikaži korisnika">
                                    <img src="{{ $transaction->user->avatar ? Storage::url('user/' . $transaction->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="40" height="40" class="rounded-circle">
                                    <div>
                                        <div class="fw-bold">{{ $transaction->user->firstname }} {{ $transaction->user->lastname }}</div>
                                        <div class="small text-muted">{{ $transaction->user->email }}</div>
                                    </div>
                                </a>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                    <span class="fw-bold">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-credit-card me-1 text-info"></i>
                                    <span>{{ ucfirst($transaction->payment_method) }}</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-primary view-transaction-details"
                                        data-transaction-id="{{ $transaction->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#transactionDetailsModal">
                                    <i class="fas fa-info-circle"></i> Detalji transakcije
                                </button>
                            </div>
                        </div>
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

<style>
    /* Stilovi za mobilni prikaz */
    .mobile-transaction-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-transaction-card .small {
        font-size: 0.85rem;
    }

    /* Prilagodba paginacije za mobilne uređaje */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }

    .page-link {
        padding: 0.375rem 0.65rem;
        font-size: 0.875rem;
    }

    /* Responsive table header */
    @media (max-width: 767.98px) {
        .d-md-table-header-group {
            display: none !important;
        }

        .table-responsive {
            border: none;
        }

        .table > tbody > tr > td {
            border-top: 1px solid #dee2e6;
            padding: 0.5rem;
        }

        .table > tbody > tr:first-child > td {
            border-top: none;
        }

        .view-transaction-details {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Prikaz za tablete */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .table td, .table th {
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-group .btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.8rem;
        }
    }

    /* Prilagodba filter dugmadi za mobilne uređaje */
    @media (max-width: 575.98px) {
        .btn-group .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal('#transactionDetailsModal');

    // Otvaranje detalja transakcije
    document.querySelectorAll('.view-transaction-details').forEach(btn => {
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
