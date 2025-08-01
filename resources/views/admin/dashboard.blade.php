<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/default.css') }}" rel="stylesheet">
    <style>
        .sidebar {
            background: #2f353a;
            min-height: 100vh;
        }
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }

        .modal-header{
            background-color: white;
        }

        .modal-content {
          background-color: white;
        }

        .avatar-img{
            width: 120px !important;
            height: 120px !important;
            object-fit: cover;
        }

        .nav-link { transition: all 0.3s; }
        .nav-link:hover { transform: translateX(5px); }
        .table-hover tr:hover { transform: translateY(-2px); }
        .table a:hover { opacity: 0.8; transform: scale(1.1); }

        /* Stilovi za pretragu istaknutih ponuda */
        .select-with-search {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            width: 100%;
        }

        .select-with-search option {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .select-with-search option:hover {
            background-color: #f8f9fa;
        }

        .search-service {
            border-radius: 0.375rem 0 0 0.375rem !important;
        }

        .input-group {
            margin-bottom: 0.5rem !important;
        }

        /* Stilovi za linkove autora */
        .table-hover tr:hover td a {
            text-decoration: underline;
        }

        /* Stilovi za statuse */
        .badge {
            font-size: 0.9em;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        /* Responsivne prilagodbe */
        @media (max-width: 768px) {
            .col-md-4 {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">Admin panel</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link text-white {{ $activeTab === 'users' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#users">
                           <i class="fas fa-user "></i> Korisnici
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'services' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#services">
                           <i class="fas fa-file-signature "></i> Ponude
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'forcedservices' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#forcedservices">
                           <i class="fas fa-star"></i> Istaknute ponude
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'projects' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#projects">
                           <i class="fas fa-handshake "></i> Projekti
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'packages' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#packages">
                           <i class="fas fa-calendar-alt "></i> Plan paketa
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'unusedfiles' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#unusedfiles">
                           <i class="fa fa-folder-open"></i> Nepotrebni file-ovi
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="tab-content">
                    <!-- Prikaz poruke sa anchor ID -->
                    @if(session('success'))
                        <div id="dashboard-message" class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div id="dashboard-message-danger" class="alert alert-danger text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Users Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'users' ? 'show active' : '' }}" id="users">
                        <h2 class="mb-4">Korisnici ({{ count($users) }})</h2>

                        <div class="card mb-4">
                            <div class="card-body">
                                <form method="GET" action="">
                                    <input type="hidden" name="tab" value="users">
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control"
                                               name="users_search"
                                               placeholder="Pretraži korisnike (ime, prezime, email, ID)..."
                                               value="{{ request('users_search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('users_search'))
                                            <a href="?tab=users" class="btn btn-outline-danger">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Ime i prezime</th>
                                        <th>Email</th>
                                        <th>Registracija</th>
                                        <th>Aktivan</th>
                                        <th>Akcija</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d.m.Y.') }}</td>
                                        <td>{{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->format('d.m.Y H:i:s') : 'Nije se jos logovao' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="#" class="text-decoration-none"
                                                    data-action="profile"
                                                    data-user-id="{{ $user->id }}"
                                                    title="Profil">
                                                    <i class="fas fa-user text-primary"></i>
                                                </a>
                                                <a href="#" class="text-decoration-none"
                                                data-action="deposit"
                                                data-user-id="{{ $user->id }}"
                                                title="Depozit">
                                                    <i class="fas fa-money-bill-wave text-success"></i>
                                                </a>
                                                <a href="#" class="text-decoration-none" title="Računi">
                                                    <i class="fas fa-file-invoice-dollar text-info"></i>
                                                </a>
                                                <a href="#" class="text-decoration-none" title="Preporuci">
                                                    <i class="fas fa-users text-warning"></i>
                                                </a>
                                                <a href="#" class="text-decoration-none" title="Tiketi">
                                                    <i class="fas fa-ticket-alt text-secondary"></i>
                                                </a>
                                                <a href="#" class="text-decoration-none" title="Obriši">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $users->onEachSide(1)->appends(['tab' => 'users'])->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                    <!-- Services Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'services' ? 'show active' : '' }}" id="services">
                        <h2 class="mb-4">Ponude ({{ count($services) }})</h2>

                        <!-- Pretraga -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form method="GET" action="">
                                    <input type="hidden" name="tab" value="services">
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
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Naziv</th>
                                        <th>Autor</th>
                                        <th>Status</th>
                                        <th>Javne do</th>
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
                                               class="text-decoration-none"
                                               title="Prikaži korisnika">
                                               <img src="{{ Storage::url('user/' . $service->user->avatar) }}"
                                         alt="Avatar" width="30" height="30">
                                                {{ $service->user->firstname }} {{ $service->user->lastname }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($service->visible)
                                                <span class="badge bg-success">Aktivna</span>
                                            @else
                                                <span class="badge bg-danger">Neaktivna</span>
                                            @endif
                                        </td>
                                        <td>{{ $service->visible_expires_at }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $services->onEachSide(1)->appends([
                                    'tab' => 'services',
                                    'services_search' => request('services_search')
                                ])->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                    <!-- Istaknute ponude Tab -->
                    <!-- Forced Services Tab -->
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

                    <!-- Projects Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'projects' ? 'show active' : '' }}" id="projects">
                        <h2 class="mb-4">Projekti</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Naziv</th>
                                        <th>Klijent</th>
                                        <th>Rok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->id }}</td>
                                        <td>{{ $project->name }}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $projects->onEachSide(1)->appends(['tab' => 'projects'])->links('pagination::bootstrap-5') }}
                                </div>
                        </div>
                    </div>

                    <!-- Packages Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'packages' ? 'show active' : '' }}" id="packages">
                        <h2 class="mb-4">Plan paketa</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Naziv</th>
                                        <th>Cena</th>
                                        <th>Trajanje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $package)
                                    <tr>
                                        <td>{{ $package->id }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->price }}</td>
                                        <td>{{ $package->duration }} dana</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $packages->onEachSide(1)->appends(['tab' => 'packages'])->links('pagination::bootstrap-5') }}
                                </div>
                        </div>
                    </div>

                    <!-- Nepotrebni file-ovi -->
                    <div class="tab-pane fade {{ $activeTab === 'unusedfiles' ? 'show active' : '' }}" id="unusedfiles">
                        <h2 class="mb-4">Nepotrebni fajlovi</h2>

                        @if(count($unusedFiles))
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Folder</th>
                                        <th>Naziv fajla</th>
                                        <th>Pregled</th>
                                        <th>Akcija</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unusedFiles as $index => $file)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ explode('/', $file)[0] }}</td> {{-- npr. "tickets" --}}
                                        <td>{{ basename($file) }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{ route('files.delete') }}" method="POST" onsubmit="return confirm('Da li želiš da obrišeš fajl: {{ basename($file) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="file_path" value="{{ $file }}">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <p class="text-muted">Nema nepotrebnih fajlova.</p>
                        @endif
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!-- Korisnici modali -->
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Učitavanje...
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Potvrda brisanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Jeste li sigurni da želite obrisati korisnika <strong id="userName"></strong>?</p>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Obriši</button>
                </div>
            </div>
        </div>
    </div>

<script>
    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('dashboard-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('dashboard-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }

document.addEventListener('DOMContentLoaded', function() {
    const actionModal = new bootstrap.Modal('#actionModal');
    const deleteModal = new bootstrap.Modal('#deleteModal');
    let currentUserId = null;

    // Opći handler za akcije
    document.querySelectorAll('[data-action]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.dataset.action;
            currentUserId = this.dataset.userId;
            const userName = this.dataset.userName;

            if(action === 'delete') {
                document.getElementById('userName').textContent = userName;
                document.getElementById('deleteForm').action = `/admin/users/${currentUserId}`;
                deleteModal.show();
                return;
            }

            const actionTitles = {
                profile: 'Profil korisnika',
                deposit: 'Depozit korisnika',
                invoices: 'Računi korisnika',
                referrals: 'Preporuke korisnika',
                tickets: 'Tiketi korisnika'
            };

            actionModal._element.querySelector('.modal-title').textContent = actionTitles[action];
            actionModal.show();

            // AJAX poziv
            fetch(`/api/admin/${currentUserId}/${action}`)
                .then(response => response.text())
                .then(html => {

                    actionModal._element.querySelector('.modal-body').innerHTML = html;
                })
                .catch(error => {
                    actionModal._element.querySelector('.modal-body').innerHTML =
                        `<div class="alert alert-danger">Došlo je do greške pri učitavanju podataka</div>`;
                });
        });
    });

    // Handler za brisanje
    document.getElementById('confirmDelete').addEventListener('click', function() {
        fetch(document.getElementById('deleteForm').action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': 'DELETE'
            },
        })
        .then(response => {
            if(response.ok) {
                document.querySelector(`tr[data-user-id="${currentUserId}"]`).remove();
                deleteModal.hide();
            } else {
                alert('Došlo je do greške prilikom brisanja');
            }
        });
    });
});
</script>
    <!-- Korisnici modali -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aktiviraj tab iz URL-a
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'users';
        const triggerEl = document.querySelector(`a[href="#${activeTab}"]`);
        if(triggerEl) new bootstrap.Tab(triggerEl).show();

        // Ažuriraj URL pri promeni taba
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const tabId = e.target.getAttribute('href').substring(1);
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('tab', tabId);
                history.replaceState(null, null, newUrl);
            });
        });
    });
    </script>
<script type="text/javascript">
    let timeout;
    function formatAmount() {
        // Ako je prethodni timeout postavljen, brišemo ga
        clearTimeout(timeout);

        // Postavljamo novi timeout koji će se izvršiti nakon 300ms
        timeout = setTimeout(function() {
            let amountInput = document.getElementById('amount');
            let value = amountInput.value;

            // Ako je unos celo broj, formatiramo ga sa dve decimale
            if (value && value % 1 === 0) {
                amountInput.value = parseFloat(value).toFixed(2);
            }
        }, 500);  // 500ms čekanja
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                    }
                    selectedValues.push(select.value);
                }
            });

            if (hasDuplicates) {
                e.preventDefault();
                alert('Ne možete izabrati istu ponudu više puta. Molimo proverite izbore.');
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Onemogući Enter u poljima za pretragu
    document.querySelectorAll('.search-service').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });
    });

    // Funkcija za pretragu ponuda unutar istaknutih (tab)
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Onemogući Enter u polju za pretragu korisnika
    const userSearchInput = document.querySelector('input[name="users_search"]');
    if (userSearchInput) {
        userSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }

    // Fokusiraj polje za pretragu kada se otvori tab
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('href') === '#users') {
                const searchInput = document.querySelector('input[name="users_search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    });
});
</script>
</body>
</html>
