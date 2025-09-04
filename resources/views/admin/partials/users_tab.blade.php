<div class="tab-pane fade {{ $activeTab === 'users' ? 'show active' : '' }}" id="users">
    <h2 class="mb-4">Korisnici</h2>

    <!-- Search card -->
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
                            <i class="fas fa-times"></i>
                            <span class="d-none d-md-inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark d-none d-md-table-header-group">
                <tr>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['users_sort_column', 'users_sort_direction']), [
                            'tab' => 'users',
                            'users_sort_column' => 'id',
                            'users_sort_direction' => request('users_sort_column') == 'id' && request('users_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">ID
                            @if(request('users_sort_column') == 'id')
                                <i class="fas fa-arrow-{{ request('users_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['users_sort_column', 'users_sort_direction']), [
                            'tab' => 'users',
                            'users_sort_column' => 'firstname',
                            'users_sort_direction' => request('users_sort_column') == 'firstname' && request('users_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Ime i prezime
                            @if(request('users_sort_column') == 'firstname')
                                <i class="fas fa-arrow-{{ request('users_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['users_sort_column', 'users_sort_direction']), [
                            'tab' => 'users',
                            'users_sort_column' => 'email',
                            'users_sort_direction' => request('users_sort_column') == 'email' && request('users_sort_direction') == 'asc' ? 'desc' : 'asc'
                                            ])) }}" class="text-white text-decoration-none">Email
                            @if(request('users_sort_column') == 'email')
                                <i class="fas fa-arrow-{{ request('users_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['users_sort_column', 'users_sort_direction']), [
                            'tab' => 'users',
                            'users_sort_column' => 'created_at',
                            'users_sort_direction' => request('users_sort_column') == 'created_at' && request('users_sort_direction') == 'desc' ? 'asc' : 'desc'
                                            ])) }}" class="text-white text-decoration-none">Registracija
                            @if(request('users_sort_column') == 'created_at')
                                <i class="fas fa-arrow-{{ request('users_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="?{{ http_build_query(array_merge(request()->except(['users_sort_column', 'users_sort_direction']), [
                            'tab' => 'users',
                            'users_sort_column' => 'last_seen_at',
                            'users_sort_direction' => request('users_sort_column') == 'last_seen_at' && request('users_sort_direction') == 'desc' ? 'asc' : 'desc'
                                            ])) }}" class="text-white text-decoration-none">Aktivan
                            @if(request('users_sort_column') == 'last_seen_at')
                                <i class="fas fa-arrow-{{ request('users_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <!-- Desktop view -->
                        <td class="d-none d-md-table-cell">{{ $user->id }}</td>
                        <td class="d-none d-md-table-cell">{{ $user->firstname }} {{ $user->lastname }}</td>
                        <td class="d-none d-md-table-cell">{{ $user->email }}</td>
                        <td class="d-none d-md-table-cell">{{ $user->created_at->format('d.m.Y.') }}</td>
                        <td class="d-none d-md-table-cell">{{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->format('d.m.Y H:i:s') : 'Nije se jos logovao' }}</td>

                        <!-- Mobile view -->
                        <td class="d-md-none">
                            <div class="mobile-user-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                                        <div class="small text-muted">ID: {{ $user->id }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="#" class="text-decoration-none"
                                            data-action="profile"
                                            data-user-id="{{ $user->id }}"
                                            title="Profil">
                                            <i class="fas fa-user text-primary"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none" title="Obriši">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="mb-1">
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    <span class="small">{{ $user->email }}</span>
                                </div>

                                <div class="mb-1">
                                    <i class="fas fa-calendar-plus me-1 text-muted"></i>
                                    <span class="small">Registracija: {{ $user->created_at->format('d.m.Y.') }}</span>
                                </div>

                                <div class="mb-2">
                                    <i class="fas fa-clock me-1 text-muted"></i>
                                    <span class="small">Aktivan: {{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->format('d.m.Y H:i') : 'Nije se jos logovao' }}</span>
                                </div>

                                <div class="d-flex justify-content-around border-top pt-2">
                                    <a href="#" class="text-decoration-none"
                                        data-action="deposit"
                                        data-user-id="{{ $user->id }}"
                                        title="Depozit">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i>
                                        <span class="small">Depozit</span>
                                    </a>
                                    <a href="#" class="text-decoration-none" title="Računi">
                                        <i class="fas fa-file-invoice-dollar text-info me-1"></i>
                                        <span class="small">Računi</span>
                                    </a>
                                    <a href="#" class="text-decoration-none" title="Preporuci">
                                        <i class="fas fa-users text-warning me-1"></i>
                                        <span class="small">Preporuke</span>
                                    </a>
                                    <a href="#" class="text-decoration-none" title="Tiketi">
                                        <i class="fas fa-ticket-alt text-secondary me-1"></i>
                                        <span class="small">Tiketi</span>
                                    </a>
                                </div>
                            </div>
                        </td>

                        <!-- Desktop action buttons -->
                        <td class="d-none d-md-table-cell">
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
            {{ $users->onEachSide(1)->appends([
                'tab' => 'users',
                'users_search' => request('users_search'),
                'users_sort_column' => request('users_sort_column', 'id'),
                'users_sort_direction' => request('users_sort_direction', 'asc')
                ])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
    /* Stilovi za mobilni prikaz */
    .mobile-user-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-user-card .small {
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

        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }
    }
</style>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    const actionModal = new bootstrap.Modal('#actionModal');
    const deleteModal = new bootstrap.Modal('#deleteModal');
    let currentUserId = null;

    // Opći handler za akcije
    document.querySelectorAll('#users [data-action]').forEach(link => {
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
