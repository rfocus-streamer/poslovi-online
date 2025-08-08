<div class="tab-pane fade {{ $activeTab === 'forcedservices' ? 'show active' : '' }}" id="forcedservices">
    <h2 class="mb-4">Istaknute ponude</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Upravljanje istaknutim ponudama</h5>
            <p class="card-text">Možeš postaviti do 3 ponude koje će biti prikazane na početnoj strani.</p>

            <form id="forcedServicesForm" method="POST" action="{{ route('dashboard.forced-services.update') }}">
                @csrf
                <div class="row mb-3">
                    @for($i = 0; $i < 3; $i++)
                        <div class="col-md-4">
                            <label for="service_id_{{ $i+1 }}" class="form-label">Ponuda #{{ $i+1 }}</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control search-service"
                                                    placeholder="Pretraži ponude..."
                                                    data-target="service_id_{{ $i+1 }}"
                                                    onkeydown="return event.key !== 'Enter';">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <select class="form-select select-with-search"
                                                        id="service_id_{{ $i+1 }}"
                                                        name="forced_services[]"
                                                        size="5" <!-- Smanjena visina -->
                                                        style="overflow-y: auto;">
                                <option value="">-- Izaberi ponudu --</option>
                                    @foreach($allServices as $service)
                                        <option value="{{ $service->id }}"
                                            {{ isset($currentForcedServices[$i]) && $currentForcedServices[$i] == $service->id ? 'selected' : '' }}>
                                            #{{ $service->id }} - {{ $service->title }}
                                        </option>
                                    @endforeach
                            </select>
                        </div>
                     @endfor
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Sačuvaj izmene
                    </button>
                </div>
            </form>
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
