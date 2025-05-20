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
            object-fit: cover;
        }

        .nav-link { transition: all 0.3s; }
        .nav-link:hover { transform: translateX(5px); }
        .table-hover tr:hover { transform: translateY(-2px); }
        .table a:hover { opacity: 0.8; transform: scale(1.1); }
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
                        <h2 class="mb-4">Korisnici</h2>
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
                                                <a href="#" class="text-decoration-none" title="Depozit">
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
                        <h2 class="mb-4">Ponude</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Naziv</th>
                                        <th>Cena</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->id }}</td>
                                        <td>{{ $service->title }}</td>
                                        <td>{{ $service->price }}</td>
                                        <td>{{ $service->status }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $services->onEachSide(1)->appends(['tab' => 'services'])->links('pagination::bootstrap-5') }}
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
</body>
</html>
