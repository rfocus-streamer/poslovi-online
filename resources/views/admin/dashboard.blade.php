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

        /* Stilovi za status pretplate */
        .badge-bg-success { background-color: #198754; }
        .badge-bg-warning { background-color: #ffc107; color: #000; }
        .badge-bg-danger { background-color: #dc3545; }
        .badge-bg-secondary { background-color: #6c757d; }

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
            @include('admin.partials.sidebar')

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
                    @include('admin.partials.users_tab')

                    <!-- Subscriptions Tab -->
                    @include('admin.partials.subscriptions_tab')

                    <!-- Transactions Tab -->
                    @include('admin.partials.transactions_tab')

                    <!-- Fiat Payouts Tab -->
                    @include('admin.partials.fiat_payouts_tab')

                    <!-- Services Tab -->
                    @include('admin.partials.services_tab')

                    <!-- Forced Services Tab ( Istaknute ponude Tab )-->
                    @include('admin.partials.forced_services_tab')

                    <!-- Projects Tab -->
                    @include('admin.partials.projects_tab')

                    <!-- Packages Tab -->
                    @include('admin.partials.packages_tab')

                    <!-- Email notification Tab -->
                    @include('admin.partials.email_notifications_tab')

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


    <!-- Korisnici modali -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

<script>
// Automatsko sakrivanje poruka
document.addEventListener('DOMContentLoaded', function() {
    const messageElement = document.getElementById('dashboard-message');
    if (messageElement) setTimeout(() => messageElement.remove(), 5000);

    const messageElementDanger = document.getElementById('dashboard-message-danger');
    if (messageElementDanger) setTimeout(() => messageElementDanger.remove(), 5000);
});

// Aktivacija tabova i URL management
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'users';
    const triggerEl = document.querySelector(`a[href="#${activeTab}"]`);

    if(triggerEl) new bootstrap.Tab(triggerEl).show();

    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const tabId = e.target.getAttribute('href').substring(1);
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tabId);
            history.replaceState(null, null, newUrl);
        });
    });
});

// Globalna formatAmount funkcija
let timeout;
function formatAmount() {
    clearTimeout(timeout);
    timeout = setTimeout(function() {
        let amountInput = document.getElementById('amount');
        let value = amountInput.value;
        if (value && value % 1 === 0) {
            amountInput.value = parseFloat(value).toFixed(2);
        }
    }, 500);
}
</script>
</body>
</html>
