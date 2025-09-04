<div class="tab-pane fade {{ $activeTab === 'subscriptions' ? 'show active' : '' }}" id="subscriptions">
    <h2 class="mb-4">Pretplate ({{ $subscriptions->total() }})</h2>

    <!-- Pretraga i filteri -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="subscriptions">
                <div class="row">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text"
                                class="form-control"
                                name="subscriptions_search"
                                placeholder="Pretraži pretplate (korisnik, plan, gateway)..."
                                value="{{ request('subscriptions_search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-md-inline">Pretraži</span>
                            </button>
                            @if(request('subscriptions_search') || request('subscriptions_status'))
                                <a href="?tab=subscriptions" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                    <span class="d-none d-md-inline">Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="btn-group w-100">
                            @php
                                $baseParams = request()->except(['subscriptions_status']);
                                $baseParams['tab'] = 'subscriptions';
                            @endphp

                            <a href="?{{ http_build_query(array_merge($baseParams, ['subscriptions_status' => 'active'])) }}"
                                class="btn btn-outline-success {{ request('subscriptions_status') === 'active' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Aktivne</span>
                                <span class="d-md-none">A</span>
                            </a>

                            <a href="?{{ http_build_query(array_merge($baseParams, ['subscriptions_status' => 'pending'])) }}"
                                class="btn btn-outline-warning {{ request('subscriptions_status') === 'pending' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Na čekanju</span>
                                <span class="d-md-none">Č</span>
                            </a>

                            <a href="?{{ http_build_query(array_merge($baseParams, ['subscriptions_status' => 'canceled'])) }}"
                                class="btn btn-outline-danger {{ request('subscriptions_status') === 'canceled' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Otkazane</span>
                                <span class="d-md-none">O</span>
                            </a>

                            <a href="?{{ http_build_query(array_merge($baseParams, ['subscriptions_status' => 'expired'])) }}"
                                class="btn btn-outline-secondary {{ request('subscriptions_status') === 'expired' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Istekle</span>
                                <span class="d-md-none">I</span>
                            </a>

                            <a href="?{{ http_build_query($baseParams) }}"
                                class="btn btn-outline-dark {{ !request('subscriptions_status') ? 'active' : '' }}">
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
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'id',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'id' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">ID
                            @if(request('subscriptions_sort_column') == 'id')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Korisnik</th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'plan_id',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'plan_id' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Plan
                            @if(request('subscriptions_sort_column') == 'plan_id')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'created_at',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'created_at' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Kreirana
                            @if(request('subscriptions_sort_column') == 'created_at')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'amount',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'amount' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Iznos
                            @if(request('subscriptions_sort_column') == 'amount')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'status',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'status' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Status
                            @if(request('subscriptions_sort_column') == 'status')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'gateway',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'gateway' && request('subscriptions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Gateway
                            @if(request('subscriptions_sort_column') == 'gateway')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['subscriptions_sort_column', 'subscriptions_sort_direction']), [
                            'tab' => 'subscriptions',
                            'subscriptions_sort_column' => 'ends_at',
                            'subscriptions_sort_direction' => request('subscriptions_sort_column') == 'ends_at' && request('subscriptions_sort_direction') == 'desc' ? 'asc' : 'desc'
                                            ])) }}" class="text-white text-decoration-none">Istek
                            @if(request('subscriptions_sort_column') == 'ends_at')
                                <i class="fas fa-arrow-{{ request('subscriptions_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $subscription)
                    <tr>
                        <!-- Desktop view -->
                        <td class="d-none d-md-table-cell">{{ $subscription->id }}</td>
                        <td class="d-none d-md-table-cell">
                            <a href="?tab=users&users_search={{ urlencode($subscription->user->email) }}"
                                class="text-decoration-none d-flex align-items-center gap-2"
                                title="Prikaži korisnika">
                                <img src="{{ $subscription->user->avatar ? Storage::url('user/' . $subscription->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="30" height="30" class="rounded-circle">
                                <span>{{ $subscription->user->firstname }} {{ $subscription->user->lastname }}</span>
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($subscription->package)
                                <a href="?tab=packages&packages_search={{ $subscription->package->id }}"
                                    class="text-decoration-none"
                                    title="Prikaži paket">
                                    {{ $subscription->package->name }}
                                </a>
                            @else
                                Paket #{{ $subscription->plan_id }} (obrisan)
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($subscription->created_at)
                                {{ \Carbon\Carbon::parse($subscription->created_at)->format('d.m.Y. H:i') }}
                            @else
                                Nema datuma
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">{{ number_format($subscription->amount, 2) }} €</td>
                        <td class="d-none d-md-table-cell">
                            @php
                                $statusColors = [
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'canceled' => 'danger',
                                                    'expired' => 'secondary'
                                                ];
                                $color = $statusColors[$subscription->status] ?? 'primary';
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="d-none d-md-table-cell">{{ ucfirst($subscription->gateway) }}</td>
                        <td class="d-none d-md-table-cell">
                            @if($subscription->user->package_expires_at)
                                {{ \Carbon\Carbon::parse($subscription->user->package_expires_at)->format('d.m.Y. H:i') }}
                            @else
                                Nije aktivirana
                            @endif
                        </td>

                        <!-- Mobile view -->
                        <td class="d-md-none">
                            <div class="mobile-subscription-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>ID: {{ $subscription->id }}</strong>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($subscription->created_at)->format('d.m.Y. H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'pending' => 'warning',
                                                'canceled' => 'danger',
                                                'expired' => 'secondary'
                                            ];
                                            $color = $statusColors[$subscription->status] ?? 'primary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <a href="?tab=users&users_search={{ urlencode($subscription->user->email) }}"
                                        class="text-decoration-none d-flex align-items-center gap-2"
                                        title="Prikaži korisnika">
                                        <img src="{{ $subscription->user->avatar ? Storage::url('user/' . $subscription->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="40" height="40" class="rounded-circle">
                                        <div>
                                            <div class="fw-bold">{{ $subscription->user->firstname }} {{ $subscription->user->lastname }}</div>
                                            <div class="small text-muted">{{ $subscription->user->email }}</div>
                                        </div>
                                    </a>
                                </div>

                                <div class="mb-2">
                                    <i class="fas fa-box me-1 text-primary"></i>
                                    @if($subscription->package)
                                        <a href="?tab=packages&packages_search={{ $subscription->package->id }}"
                                            class="text-decoration-none"
                                            title="Prikaži paket">
                                            {{ $subscription->package->name }}
                                        </a>
                                    @else
                                        Paket #{{ $subscription->plan_id }} (obrisan)
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                        <span class="fw-bold">{{ number_format($subscription->amount, 2) }} €</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-credit-card me-1 text-info"></i>
                                        <span>{{ ucfirst($subscription->gateway) }}</span>
                                    </div>
                                </div>

                                <div class="border-top pt-2">
                                    <i class="fas fa-clock me-1 text-warning"></i>
                                    <span class="small">
                                        @if($subscription->user->package_expires_at)
                                            Istek: {{ \Carbon\Carbon::parse($subscription->user->package_expires_at)->format('d.m.Y. H:i') }}
                                        @else
                                            Nije aktivirana
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-center">
            {{ $subscriptions->onEachSide(1)->appends([
                'tab' => 'subscriptions',
                'subscriptions_search' => request('subscriptions_search'),
                'subscriptions_status' => request('subscriptions_status'),
                'subscriptions_sort_column' => request('subscriptions_sort_column'),
                'subscriptions_sort_direction' => request('subscriptions_sort_direction')
                ])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
    /* Stilovi za mobilni prikaz */
    .mobile-subscription-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-subscription-card .small {
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
    // Automatsko slanje forme pri promeni statusa
    const statusSelect = document.querySelector('select[name="subscriptions_status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }

    // Enter za pretragu pretplata
    const subscriptionsSearchInput = document.querySelector('input[name="subscriptions_search"]');
    if (subscriptionsSearchInput) {
        subscriptionsSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }

    // Fokus na pretragu pri promeni taba
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('href') === '#subscriptions') {
                const searchInput = document.querySelector('input[name="subscriptions_search"]');
                if (searchInput) searchInput.focus();
            }
        });
    });
});
</script>
