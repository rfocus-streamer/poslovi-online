<div class="tab-pane fade {{ $activeTab === 'fiatpayouts' ? 'show active' : '' }}" id="fiatpayouts">
    <h2 class="mb-4">Fiat Isplate</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="tab" value="fiatpayouts">
                <div class="input-group">
                    <input type="text"
                        class="form-control"
                        name="payouts_search"
                        placeholder="Pretraži isplate (ID, korisnik, status)..."
                        value="{{ request('payouts_search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('payouts_search'))
                        <a href="?tab=fiatpayouts" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark d-md-table-header-group">
                <tr>
                    <th>ID</th>
                    <th>Korisnik</th>
                    <th>Iznos</th>
                    <th>Datum zahteva</th>
                    <th>Datum isplate</th>
                    <th>Status</th>
                    <th>Način plaćanja</th>
                    <th class="d-none d-lg-table-cell">Transaction ID</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($fiatPayouts as $payout)
                <tr>
                    <!-- Desktop view -->
                    <td class="d-none d-md-table-cell">{{ $payout->id }}</td>
                    <td class="d-none d-md-table-cell">
                        <a href="#" data-action="profile" data-user-id="{{ $payout->user_id }}">
                            {{ $payout->user->firstname }} {{ $payout->user->lastname }}
                        </a>
                    </td>
                    <td class="d-none d-md-table-cell">{{ number_format($payout->amount, 2) }} EUR</td>
                    <td class="d-none d-md-table-cell">{{ $payout->request_date->format('d.m.Y.') }}</td>
                    <td class="d-none d-md-table-cell">{{ $payout->payed_date ? $payout->payed_date->format('d.m.Y.') : 'N/A' }}</td>
                    <td class="d-none d-md-table-cell">
                        @switch($payout->status)
                            @case('requested')
                                <span class="badge bg-warning">Na čekanju</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">Izvršeno</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Odbijeno</span>
                                @break
                        @endswitch
                    </td>
                    <td class="d-none d-md-table-cell">{{ ucfirst($payout->payment_method) }}</td>
                    <td class="d-none d-lg-table-cell">
                        @if($payout->transaction_id)
                            <small>{{ $payout->transaction_id }}</small>
                        @else
                            N/A
                        @endif
                    </td>

                    <!-- Mobile view -->
                    <td class="d-md-none">
                        <div class="mobile-payout-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>ID: {{ $payout->id }}</strong>
                                    <div class="small">
                                        <a href="#" data-action="profile" data-user-id="{{ $payout->user_id }}">
                                            {{ $payout->user->firstname }} {{ $payout->user->lastname }}
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    @switch($payout->status)
                                        @case('requested')
                                            <span class="badge bg-warning">Na čekanju</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Izvršeno</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Odbijeno</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>

                            <div class="mb-1">
                                <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                <span class="small">{{ number_format($payout->amount, 2) }} EUR</span>
                            </div>

                            <div class="mb-1">
                                <i class="fas fa-calendar-plus me-1 text-muted"></i>
                                <span class="small">Zahtev: {{ $payout->request_date->format('d.m.Y.') }}</span>
                            </div>

                            <div class="mb-1">
                                <i class="fas fa-calendar-check me-1 text-muted"></i>
                                <span class="small">Isplata: {{ $payout->payed_date ? $payout->payed_date->format('d.m.Y.') : 'N/A' }}</span>
                            </div>

                            <div class="mb-1">
                                <i class="fas fa-credit-card me-1 text-muted"></i>
                                <span class="small">Način: {{ ucfirst($payout->payment_method) }}</span>
                            </div>

                            @if($payout->transaction_id)
                            <div class="mb-2">
                                <i class="fas fa-receipt me-1 text-muted"></i>
                                <span class="small">Transaction ID: {{ $payout->transaction_id }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-around border-top pt-2">
                                <a href="#" class="text-decoration-none action-payout"
                                    data-payout-action="details"
                                    data-details-url="{{ route('fiat-payouts.details', $payout->id) }}"
                                    title="Detalji">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    <span class="small">Detalji</span>
                                </a>
                                <a href="#" class="text-decoration-none action-payout"
                                    data-payout-action="approve"
                                    data-approve-url="{{ route('fiat-payouts.approve', $payout->id) }}"
                                    title="Odobri">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <span class="small">Odobri</span>
                                </a>
                                <a href="#" class="text-decoration-none action-payout"
                                    data-payout-action="reject"
                                    data-reject-url="{{ route('fiat-payouts.reject', $payout->id) }}"
                                    title="Odbij">
                                    <i class="fas fa-times-circle text-danger me-1"></i>
                                    <span class="small">Odbij</span>
                                </a>
                            </div>
                        </div>
                    </td>

                    <!-- Desktop action buttons -->
                    <td class="d-none d-md-table-cell">
                        <div class="d-flex gap-2">
                            <a href="#" class="text-decoration-none action-payout"
                                data-payout-action="details"
                                data-details-url="{{ route('fiat-payouts.details', $payout->id) }}"
                                title="Detalji">
                                <i class="fas fa-info-circle text-primary"></i>
                            </a>
                            <a href="#" class="text-decoration-none action-payout"
                                data-payout-action="approve"
                                data-approve-url="{{ route('fiat-payouts.approve', $payout->id) }}"
                                title="Odobri">
                                <i class="fas fa-check-circle text-success"></i>
                            </a>
                            <a href="#" class="text-decoration-none action-payout"
                                data-payout-action="reject"
                                data-reject-url="{{ route('fiat-payouts.reject', $payout->id) }}"
                                title="Odbij">
                                <i class="fas fa-times-circle text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $fiatPayouts->appends(['tab' => 'fiatpayouts'])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal za unos transaction ID-a -->
<div class="modal fade" id="transactionIdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Unesite Transaction ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="transactionIdInput" class="form-label">Transaction ID</label>
                    <input type="text" class="form-control" id="transactionIdInput" required>
                    <div class="invalid-feedback">Unesite transaction ID</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                <button type="button" class="btn btn-primary" id="confirmTransaction">Potvrdi</button>
            </div>
        </div>
    </div>
</div>

<!-- action modal za fiat isplate -->
<div class="modal fade" id="actionModalFiat" tabindex="-1">
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

<style>
    /* Stilovi za mobilni prikaz isplata */
    .mobile-payout-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-payout-card .small {
        font-size: 0.85rem;
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
    // Inicijalizacija modala za fiat isplate
    const actionModalFiat = new bootstrap.Modal('#actionModalFiat');
    const modalTitleFiat = document.querySelector('#actionModalFiat .modal-title');
    const modalBodyFiat = document.querySelector('#actionModalFiat .modal-body');

    // Inicijalizacija modala za transaction ID
    const transactionIdModal = new bootstrap.Modal('#transactionIdModal');
    const transactionIdInput = document.getElementById('transactionIdInput');
    const confirmTransactionBtn = document.getElementById('confirmTransaction');

    // Promenljiva za čuvanje trenutne approve URL
    let currentApproveUrl = null;

    // Handler za sve akcije isplate
    document.querySelectorAll('.action-payout').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const action = this.dataset.payoutAction;
            const detailsUrl = this.dataset.detailsUrl;
            const approveUrl = this.dataset.approveUrl;
            const rejectUrl = this.dataset.rejectUrl;

            if (action === 'approve') {
                // Sačuvaj URL za kasniju upotrebu
                currentApproveUrl = approveUrl;

                // Resetuj input i prikaži modal
                transactionIdInput.value = '';
                transactionIdInput.classList.remove('is-invalid');
                transactionIdModal.show();

            } else if (action === 'reject') {
                const confirmation = confirm('Da li ste sigurni da želite da odbijete ovu isplatu?');

                if (confirmation) {
                    fetch(rejectUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Došlo je do greške na serveru');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Došlo je do greške: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Došlo je do greške prilikom obrade zahteva: ' + error.message);
                    });
                }
            } else if (action === 'details') {
                // Proveri da li actionModal postoji
                if (!actionModalFiat || !modalTitleFiat || !modalBodyFiat) {
                    console.error('Action modal nije pronađen');
                    alert('Došlo je do greške. Modal nije inicijalizovan.');
                    return;
                }

                // Učitaj detalje u glavni modal
                modalTitleFiat.textContent = 'Detalji isplate';
                modalBodyFiat.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Učitavanje...</span></div></div>';

                // Prikaži modal dok se podaci učitavaju
                actionModalFiat.show();

                fetch(detailsUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Došlo je do greške pri učitavanju detalja');
                    }
                    return response.json();
                })
                .then(data => {
                    modalBodyFiat.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Korisnik:</strong> ${data.user_name}</p>
                                <p><strong>Iznos:</strong> ${data.amount} EUR</p>
                                <p><strong>Datum zahteva:</strong> ${data.request_date}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> ${data.status}</p>
                                <p><strong>Način plaćanja:</strong> ${data.payment_method}</p>
                                <p><strong>Transaction ID:</strong> ${data.transaction_id || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    modalBodyFiat.innerHTML = '<div class="alert alert-danger">Došlo je do greške pri učitavanju detalja</div>';
                    console.error('Error:', error);
                });
            }
        });
    });

    // Handler za potvrdu transaction ID-a
    confirmTransactionBtn.addEventListener('click', function() {
        const transactionId = transactionIdInput.value.trim();

        if (!transactionId) {
            transactionIdInput.classList.add('is-invalid');
            return;
        }

        transactionIdInput.classList.remove('is-invalid');

        if (!currentApproveUrl) {
            alert('Došlo je do greške: nije pronađen URL za odobravanje');
            return;
        }

        // Šaljemo zahtev sa transaction ID-om
        fetch(currentApproveUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ transaction_id: transactionId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Došlo je do greške na serveru');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                transactionIdModal.hide();
                location.reload();
            } else {
                alert('Došlo je do greške: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Došlo je do greške prilikom obrade zahteva: ' + error.message);
        });
    });
});
</script>
