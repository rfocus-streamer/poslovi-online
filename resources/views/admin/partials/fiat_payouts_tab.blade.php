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
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Korisnik</th>
                    <th>Iznos</th>
                    <th>Datum zahteva</th>
                    <th>Datum isplate</th>
                    <th>Status</th>
                    <th>Način plaćanja</th>
                    <th>Transaction ID</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($fiatPayouts as $payout)
                <tr>
                    <td>{{ $payout->id }}</td>
                    <td>
                        <a href="#" data-action="profile" data-user-id="{{ $payout->user_id }}">
                            {{ $payout->user->firstname }} {{ $payout->user->lastname }}
                        </a>
                    </td>
                    <td>{{ number_format($payout->amount, 2) }} EUR</td>
                    <td>{{ $payout->request_date->format('d.m.Y.') }}</td>
                    <td>{{ $payout->payed_date ? $payout->payed_date->format('d.m.Y.') : 'N/A' }}</td>
                    <td>
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
                    <td>{{ ucfirst($payout->payment_method) }}</td>
                    <td>
                        @if($payout->transaction_id)
                            <small>{{ $payout->transaction_id }}</small>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
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

        <!-- Modal za unos transaction ID-a -->
        <div class="modal fade" id="transactionIdModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Unesite Transaction ID</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionModal = new bootstrap.Modal('#actionModal');
    window.actionModal = actionModal;
    const modalTitle = document.querySelector('#actionModal .modal-title');
    const modalBody = document.querySelector('#actionModal .modal-body');

    // Inicijalizacija modala za transaction ID
    const transactionIdModal = new bootstrap.Modal('#transactionIdModal');
    const transactionIdInput = document.getElementById('transactionIdInput');
    const confirmTransactionBtn = document.getElementById('confirmTransaction');

    // Promenljiva za čuvanje trenutne approve URL
    let currentApproveUrl = null;

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
                // ... postojeći kod za detalje ...
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
