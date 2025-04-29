<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                           Korisnici
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'services' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#services">
                           Ponude
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'projects' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#projects">
                           Projekti
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'packages' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#packages">
                           Plan paketa
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="tab-content">
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
                                        <td></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $users->withQueryString()->links() }}
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
                            {{ $services->withQueryString()->links() }}
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
                            {{ $projects->withQueryString()->links() }}
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
                            {{ $packages->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
