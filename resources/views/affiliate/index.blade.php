@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
@section('content')
<style>
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    .blinking-alert {
        animation: blink 1s infinite;
    }

    .form-check-input {
        border-color: #198754;
    }

    .form-check-input:checked {
        background-color: #198754; /* Bootstrap "success" zelena */
    }
</style>

<div class="container py-5">
    <div class="row">
        <!-- Glavni sadržaj -->
        <div class="col-md-8">
            <!-- Prikaz poruka -->
            @if(session('success'))
                <div id="affiliate-message" class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="affiliate-message-danger" class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header text-center card-header btn-poslovi text-white"><i class="fas fa-euro-sign"></i> Preporuči – Zaradi! </div>

                <!-- Desktop -->
                <div class="d-none d-md-block card-body">
                    <h5 class="text-center">Preporuči prodavca i na poklon dobijaš 70% od njegove prve članarine!</h5>
                    <span>Uključi se u naš affiliate program i ostvari 70% provizije od prve mesečne članarine svakog prodavca koga preporučiš. Podeli svoj jedinstveni link i gledaj kako tvoja zarada raste!</span><br>


                    @if(!Auth::user()->affiliate_accepted)
                        <form method="POST" action="{{ route('affiliate-activate') }}" >
                            @csrf
                            <!-- Prihvatam uslove -->
                            <div class="form-check mb-3 mt-5">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Prihvatam uslove <a href="{{ route('affiliate-contract') }}" target="_blank">affiliate programa</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-power-off me-1"></i> Uključi
                                                    </button>
                        </form>
                    @else
                        <br><br>
                        <div class="text-center">
                            <span class="text-success">Tvoj affiliate status je aktivan, iskoristi mogućnost ovog programa da bi zaradio !</span>
                        </div>
                    @endif
                </div>

                <!-- Mobile -->
                <div class="d-md-none card-body">
                    <h6 class="text-center">Preporuči prodavca i na poklon dobijaš 70% od njegove prve članarine!</h6>
                    <span>Uključi se u naš affiliate program i ostvari 70% provizije od prve mesečne članarine svakog prodavca koga preporučiš. Podeli svoj jedinstveni link i gledaj kako tvoja zarada raste!</span><br>


                    @if(!Auth::user()->affiliate_accepted)
                        <form method="POST" action="{{ route('affiliate-activate') }}" >
                            @csrf
                            <!-- Prihvatam uslove -->
                            <div class="form-check mb-3 mt-5">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Prihvatam uslove <a href="{{ route('affiliate-contract') }}" target="_blank">affiliate programa</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-power-off me-1"></i> Uključi
                                                    </button>
                        </form>
                    @else
                        <br><br>
                        <div class="text-center">
                            <span class="text-success">Tvoj affiliate status je aktivan, iskoristi mogućnost ovog programa da bi zaradio !</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">

                        @if(Auth::user()->affiliate_accepted)
                            <h6 class="text-secondary">
                                <i class="fas fa-users"></i> Tvoj affiliate kod: <strong class="text-success">{{ Auth::user()->affiliate_code }} </strong>
                                <input type="hidden" class="form-control" id="affiliateLinkInput"
                                   value="{{ url('/register') }}?affiliateCode={{ Auth::user()->affiliate_code }}"
                                   readonly>
                            </h6>
                        @else
                            <h6 class="text-secondary">
                                <i class="fas fa-users"></i> Tvoj affiliate kod: <strong class="text-success"></strong>
                            </h6>
                        @endif

                        <h6 class="text-secondary">
                            <i class="fas fa-money-bill-wave"></i> Tvoja affiliate zarada: <strong class="text-success">{{ number_format(Auth::user()->affiliate_balance, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>

                        <div class="text-warning mb-1 modal-header">
                            <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#affiliatePayoutModal">
                                <i class="fas fa-money-bill-wave me-1"></i> Povuci affiliate novac
                            </button>
                        </div>

                        <div class="text-warning mb-3 modal-header">
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#affiliateRequestsStatsModal">
                                <i class="fas fa-money-bill-wave me-1"></i> Tvoje affiliate isplate
                            </button>
                        </div>


                        <div class="text-warning mb-3 modal-header">
                            <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#affiliateStatsModal">
                                <i class="fas fa-chart-line me-1"></i> Pregled affiliate statistike
                            </button>
                        </div>

                        <div class="text-warning text-center">
                            @if(Auth::user()->affiliate_accepted)
                                <a onclick="copyLink()" class="btn btn-outline-primary ms-auto w-100" data-bs-toggle="tooltip" title="Kopiraj link"> <i class="fas fa-link"></i>
                                </a>
                            @else
                                <a class="btn btn-outline-primary ms-auto w-100" data-bs-toggle="tooltip" title="Kopiraj link"> <i class="fas fa-link"></i>
                                </a>
                            @endif
                            <small class="text-secondary">Preporuči nas i zaradi — podeli svoj affiliate link!</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--  Modal -->
        <!-- Affiliate Stats Modal -->
        <div class="modal fade" id="affiliateStatsModal" tabindex="-1" aria-labelledby="affiliateStatsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="w-100 text-center">
                            <h5 class="modal-title" id="affiliateStatsModalLabel">
                                <i class="fas fa-users"></i> Tvoji affiliate korisnici
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Ukupno referala</h6>
                                        <h3 class="card-title">{{ Auth::user()->referrals->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Ukupna zarada</h6>
                                        <h3 class="card-title">{{ number_format(Auth::user()->commissionsEarned->sum('amount'), 2) }}€</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Aktivnih paketa</h6>
                                        <h3 class="card-title">{{ Auth::user()->referrals->filter(function($user) { return $user->package; })->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop -->
                        <div class="d-none d-md-table table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Registrovan</th>
                                        <th>Paket</th>
                                        <th>Cena</th>
                                        <th>Zarada</th>
                                        <th>Aktiviran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(Auth::user()->referrals as $key => $referral)

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{ $key +1 }}
                                            </div>
                                        </td>
                                        <td>{{ $referral->created_at->format('d.m.Y.') }}</td>
                                        <td>
                                            @if($referral->package)
                                                {{ $referral->package->name }}
                                            @else
                                               -
                                            @endif
                                        </td>
                                        <td>
                                            @if($referral->package)
                                                {{ number_format($referral->package->price, 2) }}€
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-success fw-bold">
                                            @php
                                                $commission = $referral->referralCommissions->sum('amount');
                                            @endphp
                                            {{ $commission ? number_format($commission, 2).'€' : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $referral->package ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $referral->referralCommissions->isNotEmpty() ? $referral->referralCommissions->first()->created_at->format('d.m.Y H:i') : 'Neaktivan' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nema registrovanih korisnika</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile & Tablet Cards -->
                        <div class="d-md-none">
                            @forelse(Auth::user()->referrals as $key => $referral)
                            <div class="card mb-3 subscription-card" data-id="{{ $referral->id }}">
                                <div class="card-header btn-poslovi-green text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Referal #{{ $key+1 }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Registrovan</small>
                                            <div>{{ $referral->created_at->format('d.m.Y.') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Paket</small>
                                            <div>
                                                @if($referral->package)
                                                    {{ $referral->package->name }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Cena</small>
                                            <div>
                                                @if($referral->package)
                                                    {{ number_format($referral->package->price, 2) }}€
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Zarada</small>
                                            <div class="text-success fw-bold">
                                                @php
                                                    $commission = $referral->referralCommissions->sum('amount');
                                                @endphp
                                                {{ $commission ? number_format($commission, 2).'€' : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">Aktiviran</small>
                                            <div>
                                                <span class="badge {{ $referral->package ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $referral->referralCommissions->isNotEmpty() ? $referral->referralCommissions->first()->created_at->format('d.m.Y H:i') : 'Neaktivan' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="card mb-3 subscription-card">
                                <div class="card-body text-center">
                                    <p class="text-muted">Nema registrovanih korisnika</p>
                                </div>
                            </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- Affiliate Payout Requests Stats Modal -->
        <div class="modal fade" id="affiliateRequestsStatsModal" tabindex="-1" aria-labelledby="affiliateRequestsStatsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="w-100 text-center">
                            <h5 class="modal-title" id="affiliateStatsModalLabel">
                                <i class="fas fa-money-bill-wave"></i> Tvoje affiliate isplate
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="d-none d-md-table modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="affiliatePayouts-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Podnet</th>
                                        <th>Isplaćen</th>
                                        <th>Iznos €</th>
                                        <th>Način plaćanja</th>
                                        <th>Uplata na</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @forelse($payouts as $key => $payout)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{ $payout->request_date->format('d.m.Y.') }}</td>
                                            <td>
                                                @if ($payout->payed_date)
                                                    {{ \Carbon\Carbon::parse($payout->payed_date)->format('d.m.Y.') }}
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ number_format($payout->amount, 2) }}</td>
                                            <td>
                                                @switch($payout->payment_method)
                                                    @case('paypal')
                                                        <i class="fab fa-paypal me-2"></i>PayPal
                                                        @break
                                                    @case('credit_card')
                                                        <i class="fas fa-credit-card me-2"></i>Kartica
                                                        @break
                                                    @case('bank_account')
                                                        <i class="fas fa-university me-2"></i>Bankovni transfer
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{$payout->payment_details}}</td>
                                            <td>
                                                @switch($payout->status)
                                                    @case('requested')
                                                        <span class="badge bg-warning">Na čekanju</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Završeno</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Odbijeno</span>
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nema još zahteva za isplatu</td>
                                        </tr>
                                        @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center pagination-buttons" id="pagination-links">
                                {{ $payouts->links() }}
                        </div>

                    </div>

                    <!-- Mobile & Tablet Cards -->
                    <div class="d-md-none">
                        @forelse($payouts as $key => $payout)
                        <div class="card mb-3 subscription-card" data-id="{{ $payout->id }}">
                            <div class="card-header btn-poslovi-green text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Isplata #{{ $key+1 }}</span>
                                    <span class="badge bg-light text-dark">
                                        @switch($payout->status)
                                            @case('requested')
                                                <span class="badge bg-warning">Na čekanju</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Završeno</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">Odbijeno</span>
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Podnet</small>
                                        <div>{{ $payout->request_date->format('d.m.Y.') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Isplaćen</small>
                                        <div>
                                            @if ($payout->payed_date)
                                                {{ \Carbon\Carbon::parse($payout->payed_date)->format('d.m.Y.') }}
                                            @else
                                                Nema podataka
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Iznos</small>
                                        <div class="fw-bold">{{ number_format($payout->amount, 2) }} €</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Način plaćanja</small>
                                        <div>
                                            @switch($payout->payment_method)
                                                @case('paypal')
                                                    <i class="fab fa-paypal me-2"></i>PayPal
                                                    @break
                                                @case('credit_card')
                                                    <i class="fas fa-credit-card me-2"></i>Kartica
                                                    @break
                                                @case('bank_account')
                                                    <i class="fas fa-university me-2"></i>Bankovni transfer
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">Uplata na</small>
                                        <div>{{ $payout->payment_details }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="card mb-3 subscription-card">
                            <div class="card-body text-center">
                                <p class="text-muted">Nema još zahteva za isplatu</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <!-- Payout Affiliate Modal -->
        <div class="modal fade" id="affiliatePayoutModal" tabindex="-1" aria-labelledby="affiliatePayoutModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="affiliatePayoutModalLabel">
                            <i class="fas fa-money-bill-wave"></i> Zahtev za affiliate isplatu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="payoutForm" method="POST" action="{{ route('affiliate.payout') }}">
                            @csrf

                            <div class="d-flex mb-3">
                                <h6 class="form-label">
                                    Dostupno za isplatu: <strong class="text-success">{{ number_format(Auth::user()->affiliate_balance, 2) }} €</strong>
                                </h6>
                            </div>

                            <!-- Inside your modal form -->
                            <div class="mb-3">
                                <label for="payoutAmount" class="form-label">Iznos za isplatu (€)</label>
                                <input type="number" step="1" min="100" max="{{ auth()->user()->affiliate_balance }}"
                                       class="form-control" id="payoutAmount" name="amount" required
                                       placeholder="Minimalno 100€">
                            </div>

                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Način isplate</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Izaberi način isplate</option>
                                    <option value="paypal">PayPal</option>
                                   <!--  <option value="bank_account">Bankovni transfer</option>
                                    <option value="credit_card">Kreditna kartica</option> -->
                                </select>
                            </div>

                            <div class="mb-3" id="paypalEmailField" style="display: none;">
                                <label for="paypalEmail" class="form-label">PayPal email</label>
                                <input type="email" class="form-control" id="paypalEmail" name="paypal_email">
                            </div>

                            <div class="mb-3" id="bankDetailsField" style="display: none;">
                                <label for="bankAccount" class="form-label">Broj bankovnog računa</label>
                                <input type="text" class="form-control" id="bankAccount" name="bank_account" placeholder="IBAN broj">
                            </div>

                            <div id="payoutError" class="alert alert-danger d-none text-center"></div>

                            <div id="payoutSuccess" class="alert alert-success d-none text-center"></div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-2"></i>Pošalji zahtev
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Toast Obaveštenje -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                         ✅ Link je uspešno kopiran!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
         </div>

        <!-- Payout Success Toast -->
        <div id="payoutSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> Uspešno ste poslali zahtev za isplatu!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

    </div>
</div>

<script>
    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('affiliate-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('affiliate-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }

    function copyLink() {
        const affiliateLink = document.getElementById('affiliateLinkInput');
        navigator.clipboard.writeText(affiliateLink.value).then(function() {
            let copyToast = new bootstrap.Toast(document.getElementById('copyToast'));
            copyToast.show();
        }, function(err) {
            console.error("Greška pri kopiranju: ", err);
        });
    }
</script>

<script>
    // Handle payout form submission with AJAX
    document.getElementById('payoutForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const errorDiv = document.getElementById('payoutError');
        const successDiv = document.getElementById('payoutSuccess');

        // Reset UI state
        errorDiv.classList.add('d-none');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesiram...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successToast = new bootstrap.Toast(document.getElementById('payoutSuccessToast'));
                successToast.show();

                successDiv.textContent = data.message;
                console.log(data);
                successDiv.classList.remove('d-none');
                // Sakrij error poruku ako je prikazana
                errorDiv.classList.add('d-none');

                // Reload the page after 5 seconds to show flash message
                setTimeout(() => {
                    window.location.reload();
                }, 5000);
            }
        })
        .catch(error => {
            //errorDiv.textContent = 'Došlo je do greške prilikom obrade zahteva';
            //errorDiv.classList.remove('d-none');
            console.error('Error:', error);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Pošalji zahtev';
        });
    });

    // Show/hide payment method fields based on selection
    document.getElementById('paymentMethod').addEventListener('change', function() {
        const paypalField = document.getElementById('paypalEmailField');
        const bankField = document.getElementById('bankDetailsField');

        paypalField.style.display = 'none';
        bankField.style.display = 'none';

        if (this.value === 'paypal') {
            paypalField.style.display = 'block';
        } else if (this.value === 'bank') {
            bankField.style.display = 'block';
        }
    });
</script>
<script type="text/javascript">
   document.addEventListener("DOMContentLoaded", function () {
    // Osnovni URL za AJAX pozive
    const baseUrl = "{{ route('affiliate.index') }}";

    // Poziv funkcije za prevođenje teksta paginacije
    translatePaginationText();

    // Kada korisnik klikne na link paginacije
    $('#pagination-links').on('click', 'a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');  // Preuzmi URL sa linka za paginaciju

        // Napravi AJAX poziv
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                // Ažuriraj sadržaj tabele i paginacije
                $('#affiliatePayouts-table').html($(response).find('#affiliatePayouts-table').html());
                $('#pagination-links').html($(response).find('#pagination-links').html());
                // Poziv funkcije za prevođenje teksta paginacije
                translatePaginationText();

                // Ažuriraj URL u browseru bez osvežavanja stranice
                history.pushState(null, null, url);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data: ", error);
            }
        });
    });

    // Omogući povratak/napred kroz istoriju sa AJAX sadržajem
    window.addEventListener('popstate', function() {
        $.ajax({
            url: location.href,
            type: 'GET',
            success: function(response) {
                $('#affiliatePayouts-table').html($(response).find('#affiliatePayouts-table').html());
                $('#pagination-links').html($(response).find('#pagination-links').html());
                translatePaginationText();
            }
        });
    });
});

// Funkcija za prevođenje teksta paginacije
function translatePaginationText() {
    const textElement = document.querySelector("p.text-sm.text-gray-700");
    if (textElement) {
        let text = textElement.textContent.trim();
        let matches = text.match(/\d+/g);

        if (matches && matches.length === 3) {
            let translatedText = `Prikazuje od ${matches[0]} do ${matches[1]} od ukupno ${matches[2]} rezultata`;
            textElement.textContent = translatedText;
        }
    }
}
</script>
@endsection
