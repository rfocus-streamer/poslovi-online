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
                <div class="card-header text-center card-header text-white" style="background-color: #198754"><i class="fas fa-euro-sign"></i> Preporuči – Zaradi! </div>

                <div class="card-body">
                    <h5 class="text-center">Preporuči prodavca i na poklon dobijaš 70% od njegove prve članarine!</h5>
                    <span>Uključi se u naš affiliate program i ostvari 70% provizije od prve mesečne članarine svakog prodavca koga preporučiš. Podeli svoj jedinstveni link i gledaj kako tvoja zarada raste!</span><br>


                    @if(!Auth::user()->affiliate_accepted)
                        <form method="POST" action="{{ route('affiliate-activate') }}" >
                            @csrf
                            <!-- Prihvatam uslove -->
                            <div class="form-check mb-3 mt-5">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Saglasan sam na <a href="{{ route('affiliate-contract') }}" target="_blank">ugovorne uslove</a>
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

                        <div class="table-responsive">
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

                            <div class="mb-3">
                                <label for="payoutAmount" class="form-label">Iznos za isplatu (€)</label>
                                <input type="number" step="1" min="100" max="1000"
                                       class="form-control" id="payoutAmount" name="amount" required
                                       placeholder="Minimalno 100€">
                            </div>

                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Način isplate</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Izaberite način isplate</option>
                                    <option value="paypal">PayPal</option>
                                   <!--  <option value="bank">Bankovni transfer</option>
                                    <option value="crypto">Kriptovaluta</option> -->
                                </select>
                            </div>

                            <div class="mb-3" id="paypalEmailField" style="display: none;">
                                <label for="paypalEmail" class="form-label">PayPal email</label>
                                <input type="email" class="form-control" id="paypalEmail" name="paypal_email">
                            </div>

                            <div class="mb-3" id="bankDetailsField" style="display: none;">
                                <label for="bankAccount" class="form-label">Broj bankovnog računa</label>
                                <input type="text" class="form-control" id="bankAccount" name="bank_account">
                            </div>

                            <div id="payoutError" class="alert alert-danger d-none"></div>

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
@endsection
