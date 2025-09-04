<div class="tab-pane fade {{ $activeTab === 'privileged_commissions' ? 'show active' : '' }}" id="privileged_commissions">
    <h2 class="mb-4">Privilegovani procenti korisnika</h2>

    <!-- Search card za pronalaženje korisnika za dodavanje -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Dodaj nove privilegovane procente za korisnika</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="privileged_commissions">
                <div class="input-group">
                    <input type="text"
                        class="form-control"
                        name="user_search"
                        placeholder="Pretraži korisnike (ime, prezime, email, ID)..."
                        value="{{ request('user_search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                        <span class="d-none d-md-inline">Pretraži</span>
                    </button>
                    @if(request('user_search'))
                        <a href="?tab=privileged_commissions" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i>
                            <span class="d-none d-md-inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            @if(request('user_search') && $searchedUsers->count() > 0)
            <div class="mt-3">
                <h6>Rezultati pretrage:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="d-none d-md-table-header-group">
                            <tr>
                                <th>ID</th>
                                <th>Ime i prezime</th>
                                <th>Email</th>
                                <th>Akcija</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($searchedUsers as $user)
                            <tr>
                                <td class="d-none d-md-table-cell">{{ $user->id }}</td>
                                <td class="d-none d-md-table-cell">{{ $user->firstname }} {{ $user->lastname }}</td>
                                <td class="d-none d-md-table-cell">{{ $user->email }}</td>
                                <td class="d-none d-md-table-cell">
                                    <button class="btn btn-sm btn-success add-privileged-commission-btn"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->firstname }} {{ $user->lastname }}">
                                        <i class="fas fa-plus"></i> Dodaj procente
                                    </button>
                                </td>

                                <!-- Mobile view for search results -->
                                <td class="d-md-none">
                                    <div class="mobile-user-search-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                                                <div class="small text-muted">ID: {{ $user->id }}</div>
                                            </div>
                                            <button class="btn btn-sm btn-success add-privileged-commission-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->firstname }} {{ $user->lastname }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="mb-1">
                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                            <span class="small">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @elseif(request('user_search'))
            <div class="mt-3 alert alert-info">
                Nema rezultata za pretragu: "{{ request('user_search') }}"
            </div>
            @endif
        </div>
    </div>

    <!-- Tabela sa postojećim procentima -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Postojeći privilegovani procenti korisnika</h5>
        </div>
        <div class="card-body">
            @if($privilegedCommissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark d-none d-md-table-header-group">
                        <tr>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->except(['privileged_commissions_sort_column', 'privileged_commissions_sort_direction']), [
                                    'tab' => 'privileged_commissions',
                                    'privileged_commissions_sort_column' => 'id',
                                    'privileged_commissions_sort_direction' => request('privileged_commissions_sort_column') == 'id' && request('privileged_commissions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                ])) }}" class="text-white text-decoration-none">ID
                                    @if(request('privileged_commissions_sort_column') == 'id')
                                        <i class="fas fa-arrow-{{ request('privileged_commissions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Korisnik</th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->except(['privileged_commissions_sort_column', 'privileged_commissions_sort_direction']), [
                                    'tab' => 'privileged_commissions',
                                    'privileged_commissions_sort_column' => 'buyer_commission',
                                    'privileged_commissions_sort_direction' => request('privileged_commissions_sort_column') == 'buyer_commission' && request('privileged_commissions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                ])) }}" class="text-white text-decoration-none">Provizija kupca
                                    @if(request('privileged_commissions_sort_column') == 'buyer_commission')
                                        <i class="fas fa-arrow-{{ request('privileged_commissions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->except(['privileged_commissions_sort_column', 'privileged_commissions_sort_direction']), [
                                    'tab' => 'privileged_commissions',
                                    'privileged_commissions_sort_column' => 'seller_commission',
                                    'privileged_commissions_sort_direction' => request('privileged_commissions_sort_column') == 'seller_commission' && request('privileged_commissions_sort_direction') == 'asc' ? 'desc' : 'asc'
                                ])) }}" class="text-white text-decoration-none">Provizija prodavca
                                    @if(request('privileged_commissions_sort_column') == 'seller_commission')
                                        <i class="fas fa-arrow-{{ request('privileged_commissions_sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->except(['privileged_commissions_sort_column', 'privileged_commissions_sort_direction']), [
                                    'tab' => 'privileged_commissions',
                                    'privileged_commissions_sort_column' => 'created_at',
                                    'privileged_commissions_sort_direction' => request('privileged_commissions_sort_column') == 'created_at' && request('privileged_commissions_sort_direction') == 'desc' ? 'asc' : 'desc'
                                ])) }}" class="text-white text-decoration-none">Datum kreiranja
                                    @if(request('privileged_commissions_sort_column') == 'created_at')
                                        <i class="fas fa-arrow-{{ request('privileged_commissions_sort_direction') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Akcija</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($privilegedCommissions as $commission)
                        <tr>
                            <!-- Desktop view -->
                            <td class="d-none d-md-table-cell">{{ $commission->id }}</td>
                            <td class="d-none d-md-table-cell">
                                <a href="#" class="text-decoration-none view-user-profile"
                                   data-user-id="{{ $commission->user_id }}">
                                    {{ $commission->user->firstname }} {{ $commission->user->lastname }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $commission->user->email }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $commission->buyer_commission }}%</td>
                            <td class="d-none d-md-table-cell">{{ $commission->seller_commission }}%</td>
                            <td class="d-none d-md-table-cell">{{ $commission->created_at->format('d.m.Y. H:i') }}</td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary edit-privileged-commission-btn"
                                            data-commission-id="{{ $commission->id }}"
                                            data-user-id="{{ $commission->user_id }}"
                                            data-buyer-commission="{{ $commission->buyer_commission }}"
                                            data-seller-commission="{{ $commission->seller_commission }}"
                                            title="Izmeni">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-privileged-commission-btn"
                                            data-commission-id="{{ $commission->id }}"
                                            data-user-name="{{ $commission->user->firstname }} {{ $commission->user->lastname }}"
                                            title="Obriši">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Mobile view -->
                            <td class="d-md-none">
                                <div class="mobile-commission-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>ID: {{ $commission->id }}</strong>
                                            <div class="small text-muted">
                                                {{ $commission->created_at->format('d.m.Y. H:i') }}
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-primary edit-privileged-commission-btn"
                                                    data-commission-id="{{ $commission->id }}"
                                                    data-user-id="{{ $commission->user_id }}"
                                                    data-buyer-commission="{{ $commission->buyer_commission }}"
                                                    data-seller-commission="{{ $commission->seller_commission }}"
                                                    title="Izmeni">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-privileged-commission-btn"
                                                    data-commission-id="{{ $commission->id }}"
                                                    data-user-name="{{ $commission->user->firstname }} {{ $commission->user->lastname }}"
                                                    title="Obriši">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <a href="#" class="text-decoration-none view-user-profile"
                                           data-user-id="{{ $commission->user_id }}">
                                            <i class="fas fa-user me-1 text-primary"></i>
                                            <strong>{{ $commission->user->firstname }} {{ $commission->user->lastname }}</strong>
                                        </a>
                                        <div class="small text-muted">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $commission->user->email }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-around border-top pt-2">
                                        <div class="text-center">
                                            <div class="fw-bold text-success">{{ $commission->buyer_commission }}%</div>
                                            <div class="small text-muted">Kupac</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="fw-bold text-info">{{ $commission->seller_commission }}%</div>
                                            <div class="small text-muted">Prodavac</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $privilegedCommissions->appends([
                        'tab' => 'privileged_commissions',
                        'privileged_commissions_sort_column' => request('privileged_commissions_sort_column', 'id'),
                        'privileged_commissions_sort_direction' => request('privileged_commissions_sort_direction', 'asc')
                    ])->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @else
            <div class="alert alert-info">
                Trenutno nema definisanih privilegovanih procenata za korisnike.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal za dodavanje procenata -->
<div class="modal fade" id="addPrivilegedCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Dodaj privilegovane procente za korisnika</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPrivilegedCommissionForm" method="POST" action="{{ route('admin.privileged_commissions.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="privileged_commission_user_id">
                    <div class="mb-3">
                        <label for="privileged_buyer_commission" class="form-label">Provizija kupca (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               id="privileged_buyer_commission" name="buyer_commission" value="3" required>
                    </div>
                    <div class="mb-3">
                        <label for="privileged_seller_commission" class="form-label">Provizija prodavca (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               id="privileged_seller_commission" name="seller_commission" value="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                    <button type="submit" class="btn btn-primary">Sačuvaj</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal za izmenu procenata -->
<div class="modal fade" id="editPrivilegedCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Izmeni privilegovane procente korisnika</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPrivilegedCommissionForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="commission_id" id="edit_privileged_commission_id">
                    <div class="mb-3">
                        <label for="edit_privileged_buyer_commission" class="form-label">Provizija kupca (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               id="edit_privileged_buyer_commission" name="buyer_commission" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_privileged_seller_commission" class="form-label">Provizija prodavca (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               id="edit_privileged_seller_commission" name="seller_commission" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                    <button type="submit" class="btn btn-primary">Sačuvaj izmene</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal za brisanje procenata -->
<div class="modal fade" id="deletePrivilegedCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Potvrda brisanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Jeste li sigurni da želite obrisati privilegovane procente za korisnika <strong id="privileged_commission_user_name"></strong>?</p>
                <form id="deletePrivilegedCommissionForm" method="POST">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                <button type="button" class="btn btn-danger" id="confirmPrivilegedCommissionDelete">Obriši</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Stilovi za mobilni prikaz */
    .mobile-commission-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-user-search-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-commission-card .small,
    .mobile-user-search-card .small {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addPrivilegedCommissionModal = new bootstrap.Modal('#addPrivilegedCommissionModal');
    const editPrivilegedCommissionModal = new bootstrap.Modal('#editPrivilegedCommissionModal');
    const deletePrivilegedCommissionModal = new bootstrap.Modal('#deletePrivilegedCommissionModal');

    // Dodavanje procenata
    document.querySelectorAll('.add-privileged-commission-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            document.getElementById('privileged_commission_user_id').value = userId;
            document.querySelector('#addPrivilegedCommissionModal .modal-title').textContent =
                `Dodaj privilegovane procente za korisnika: ${userName}`;

            addPrivilegedCommissionModal.show();
        });
    });

    // Izmena procenata
    document.querySelectorAll('.edit-privileged-commission-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commissionId = this.dataset.commissionId;
            const buyerCommission = this.dataset.buyerCommission;
            const sellerCommission = this.dataset.sellerCommission;

            document.getElementById('edit_privileged_commission_id').value = commissionId;
            document.getElementById('edit_privileged_buyer_commission').value = buyerCommission;
            document.getElementById('edit_privileged_seller_commission').value = sellerCommission;

            document.getElementById('editPrivilegedCommissionForm').action = `/privileged-commissions/${commissionId}`;

            editPrivilegedCommissionModal.show();
        });
    });

    // Brisanje procenata
    document.querySelectorAll('.delete-privileged-commission-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commissionId = this.dataset.commissionId;
            const userName = this.dataset.userName;

            document.getElementById('privileged_commission_user_name').textContent = userName;
            document.getElementById('deletePrivilegedCommissionForm').action = `/privileged-commissions/${commissionId}`;

            deletePrivilegedCommissionModal.show();
        });
    });

    // Potvrda brisanja
    document.getElementById('confirmPrivilegedCommissionDelete').addEventListener('click', function() {
        document.getElementById('deletePrivilegedCommissionForm').submit();
    });

    // Prikaz profila korisnika
    document.querySelectorAll('.view-user-profile').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;

            // Koristimo postojeći modal za akcije
            const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
            actionModal._element.querySelector('.modal-title').textContent = 'Profil korisnika';
            actionModal.show();

            // Učitavanje podataka o korisniku
            fetch(`/api/admin/${userId}/profile`)
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
});
</script>
