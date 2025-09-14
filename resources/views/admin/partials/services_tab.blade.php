<div class="tab-pane fade {{ $activeTab === 'services' ? 'show active' : '' }}" id="services">
    <h2 class="mb-4">Ponude</h2>

    <!-- Pretraga i filteri -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="services">
                <div class="row">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text"
                                class="form-control"
                                name="services_search"
                                placeholder="Pretraži ponude (naziv, ID, autor)..."
                                value="{{ request('services_search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-md-inline">Pretraži</span>
                            </button>
                            @if(request('services_search'))
                                <a href="?tab=services" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                    <span class="d-none d-md-inline">Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100">
                            <a href="?tab=services&services_status=active"
                                class="btn btn-outline-success {{ request('services_status') === 'active' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Aktivne</span>
                                <span class="d-md-none">A</span>
                            </a>
                            <a href="?tab=services&services_status=inactive"
                                class="btn btn-outline-danger {{ request('services_status') === 'inactive' ? 'active' : '' }}">
                                <span class="d-none d-md-inline">Neaktivne</span>
                                <span class="d-md-none">N</span>
                            </a>
                            <a href="?tab=services"
                                class="btn btn-outline-secondary {{ !request('services_status') ? 'active' : '' }}">
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
                        <a href="?{{ http_build_query(array_merge(request()->except(['services_sort_column', 'services_sort_direction']), [
                            'tab' => 'services',
                            'services_sort_column' => 'id',
                            'services_sort_direction' => request('services_sort_column') == 'id' && request('services_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">ID
                            @if(request('services_sort_column') == 'id')
                                <i class="fas fa-arrow-{{ request('services_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['services_sort_column', 'services_sort_direction']), [
                            'tab' => 'services',
                            'services_sort_column' => 'title',
                            'services_sort_direction' => request('services_sort_column') == 'title' && request('services_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Naziv
                            @if(request('services_sort_column') == 'title')
                                <i class="fas fa-arrow-{{ request('services_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Autor</th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['services_sort_column', 'services_sort_direction']), [
                            'tab' => 'services',
                            'services_sort_column' => 'visible',
                            'services_sort_direction' => request('services_sort_column') == 'visible' && request('services_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Status
                            @if(request('services_sort_column') == 'visible')
                                <i class="fas fa-arrow-{{ request('services_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['services_sort_column', 'services_sort_direction']), [
                            'tab' => 'services',
                            'services_sort_column' => 'visible_expires_at',
                            'services_sort_direction' => request('services_sort_column') == 'visible_expires_at' && request('services_sort_direction') == 'desc' ? 'asc' : 'desc'
                                            ])) }}" class="text-white text-decoration-none">Javne do
                            @if(request('services_sort_column') == 'visible_expires_at')
                                <i class="fas fa-arrow-{{ request('services_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                    <tr>
                        <!-- Desktop view -->
                        <td class="d-none d-md-table-cell">{{ $service->id }}</td>
                        <td class="d-none d-md-table-cell">
                            <a target="_blank" href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}" class="btn">
                                {{ $service->title }}
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <a href="?tab=users&users_search={{ urlencode($service->user->email) }}"
                                class="text-decoration-none d-flex align-items-center gap-2"
                                title="Prikaži korisnika">
                                <img src="{{ $service->user->avatar ? Storage::url('user/' . $service->user->avatar) : asset('images/default-avatar.png') }}"  alt="Avatar" width="30" height="30" class="rounded-circle">
                                <span>{{ $service->user->firstname }} {{ $service->user->lastname }}</span>
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($service->visible && $service->visible_expires_at > now())
                                <span class="badge bg-success">Aktivna</span>
                            @else
                                @if($service->visible_expires_at < now() && $service->is_unlimited )
                                    <span class="badge bg-success">Aktivna</span>
                                @else
                                    <span class="badge bg-danger">Neaktivna</span>
                                @endif
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($service->visible_expires_at)
                                @if($service->is_unlimited )
                                    {{ \Carbon\Carbon::parse($service->user->package_expires_at)->format('d.m.Y. H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y. H:i') }}
                                @endif
                            @else
                                Nije javno vidljivo
                            @endif
                        </td>

                        <!-- Mobile view -->
                        <td class="d-md-none">
                            <div class="mobile-service-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>ID: {{ $service->id }}</strong>
                                        <div class="small text-muted">
                                            @if($service->visible_expires_at)
                                                {{ \Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y. H:i') }}
                                            @else
                                                Nije javno vidljivo
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        @if($service->visible && $service->visible_expires_at > now())
                                            <span class="badge bg-success">Aktivna</span>
                                        @else
                                            <span class="badge bg-danger">Neaktivna</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <a target="_blank" href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}" class="text-decoration-none">
                                        <h6 class="mb-0">{{ $service->title }}</h6>
                                    </a>
                                </div>

                                <div class="mb-2">
                                    <a href="?tab=users&users_search={{ urlencode($service->user->email) }}"
                                        class="text-decoration-none d-flex align-items-center gap-2"
                                        title="Prikaži korisnika">
                                        <img src="{{ $service->user->avatar ? Storage::url('user/' . $service->user->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" width="40" height="40" class="rounded-circle">
                                        <div>
                                            <div class="fw-bold">{{ $service->user->firstname }} {{ $service->user->lastname }}</div>
                                            <div class="small text-muted">{{ $service->user->email }}</div>
                                        </div>
                                    </a>
                                </div>

                                <div class="text-center">
                                    <a target="_blank" href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> Pogledaj ponudu
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-center">
            {{ $services->onEachSide(1)->appends([
                'tab' => 'services',
                'services_search' => request('services_search'),
                'services_status' => request('services_status'),
                'services_sort_column' => request('services_sort_column'),
                'services_sort_direction' => request('services_sort_direction')
                ])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
    /* Stilovi za mobilni prikaz */
    .mobile-service-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-service-card .small {
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
    // Onemogući Enter u poljima za pretragu
    document.querySelectorAll('.search-service').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    });

    // Pretraga ponuda
    function setupSearch() {
        document.querySelectorAll('.search-service').forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const targetSelectId = this.getAttribute('data-target');
                const select = document.getElementById(targetSelectId);

                Array.from(select.options).forEach(option => {
                    const text = option.text.toLowerCase();
                    option.style.display = text.includes(searchTerm) || option.value === "" ? '' : 'none';
                });
            });
        });
    }

    // Validacija duplikata
    const form = document.getElementById('forcedServicesForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selects = form.querySelectorAll('select[name="forced_services[]"]');
            const selectedValues = [];
            let hasDuplicates = false;

            selects.forEach(select => {
                if (select.value) {
                    if (selectedValues.includes(select.value)) {
                        hasDuplicates = true;
                        select.classList.add('is-invalid');
                    } else {
                        selectedValues.push(select.value);
                        select.classList.remove('is-invalid');
                    }
                }
            });

            if (hasDuplicates) {
                e.preventDefault();
                alert('Ne možete izabrati istu ponudu više puta. Molimo proverite izbore.');
            }
        });
    }

    setupSearch();
});
</script>
