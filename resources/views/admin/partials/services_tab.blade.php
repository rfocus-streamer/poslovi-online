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
                                </button>
                            @if(request('services_search'))
                                <a href="?tab=services" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100">
                            <a href="?tab=services&services_status=active"
                                class="btn btn-outline-success {{ request('services_status') === 'active' ? 'active' : '' }}">Aktivne</a>
                            <a href="?tab=services&services_status=inactive"
                                class="btn btn-outline-danger {{ request('services_status') === 'inactive' ? 'active' : '' }}">Neaktivne
                            </a>
                            <a href="?tab=services"
                                class="btn btn-outline-secondary {{ !request('services_status') ? 'active' : '' }}">Sve
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
                    <td>{{ $service->id }}</td>
                    <td>
                        <a target="_blank" href="{{ route('services.show', $service->id) }}" class="btn">
                            {{ $service->title }}
                        </a>
                    </td>
                    <td>
                        <a href="?tab=users&users_search={{ urlencode($service->user->email) }}"
                            class="text-decoration-none d-flex align-items-center gap-2"
                            title="Prikaži korisnika">
                            <img src="{{ $service->user->avatar ? Storage::url('user/' . $service->user->avatar) : asset('images/default-avatar.png') }}"  alt="Avatar" width="30" height="30" class="rounded-circle">
                            <span>{{ $service->user->firstname }} {{ $service->user->lastname }}</span>
                        </a>
                    </td>
                    <td>
                        @if($service->visible && $service->visible_expires_at > now())
                            <span class="badge bg-success">Aktivna</span>
                        @else
                            <span class="badge bg-danger">Neaktivna</span>
                        @endif
                    </td>
                    <td>
                        @if($service->visible_expires_at)
                            {{ \Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y. H:i') }}
                        @else
                            Nije javno vidljivo
                        @endif
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
