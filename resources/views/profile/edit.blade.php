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

            <!-- Prikaz poruke sa anchor ID -->
            @if(session('success'))
                <div id="profile-message" class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="profile-message-danger" class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card-header text-center mb-4"
                style="border-color: #198754; border: 2px solid #198754;">
                <i class="fas fa-user"></i> Uredi profil !
            </div>

                @if( Auth::user()->role === 'seller')
                    <div class="text-center mb-4">
                        <small class="text-secondary">Mesto gde veštine postaju prihod!</small>
                        <small class="text-secondary">Ponudi. Poveži se. Zaradi.</small>
                    </div>
                @endif


                @if( Auth::user()->role === 'buyer')
                    <div class="text-center mb-4">
                        <small class="text-secondary">Mesto gde tvoj sledeći projekat postaje stvarnost!</small><br>
                        <small class="text-secondary">Pronađi. Naruči. Napreduj.</small>
                    </div>
                @endif


                @if( Auth::user()->role === 'both')
                    <div class="text-center mb-4">
                        <small class="text-secondary">Mesto gde tvoj sledeći projekat postaje stvarnost a veštine postaju prihod!</small><br>
                        <small class="text-secondary">Pronađi. Naruči. Zaradi. Poveži se.</small>
                    </div>
                @endif

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH') <!-- Dodajemo PATCH metod jer forma koristi POST -->
                        <!-- Avatar Upload -->
                        <div class="form-group mb-3 text-center">
                            <label for="avatar" class="form-label d-block">
                                <img src="{{ Storage::url('user/' . Auth::user()->avatar) }}"
                                     alt="Avatar" class="rounded-circle avatar-img" width="100" height="100">
                            </label>
                            <input type="file" id="avatar" name="avatar" class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ime i Prezime -->
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label"><i class="fas fa-user me-1"></i> Ime</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" value="{{ Auth::user()->firstname }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label"><i class="fas fa-user-tag me-1"></i> Prezime</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" value="{{ Auth::user()->lastname }}" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-1">
                            <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" disabled="">
                        </div>

                        <!-- Telefon -->
                        <div class="form-group mb-1">
                            <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i> Telefon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{9,15}" placeholder="06X/XXX-XXX" value="{{ Auth::user()->phone }}" required>
                        </div>

                        <div class="row mb-3">
                            <!-- Ulica -->
                            <div class="col-12 col-md-4">
                                <label for="street" class="form-label"><i class="fas fa-road me-1"></i> Ulica</label>
                                <input type="text" id="street" name="street" class="form-control" value="{{ Auth::user()->street ?? '' }}" required>
                            </div>

                            <!-- Grad -->
                            <div class="col-12 col-md-4">
                                <label for="city" class="form-label"><i class="fas fa-city me-1"></i> Grad</label>
                                <input type="text" id="city" name="city" class="form-control" value="{{ Auth::user()->city ?? '' }}" required>
                            </div>

                            <!-- Zemlja -->
                            <div class="col-12 col-md-4">
                                <label for="country" class="form-label"><i class="fas fa-globe me-1"></i> Zemlja</label>
                                <input type="text" id="country" name="country" class="form-control" value="{{ Auth::user()->country ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn text-white w-100 mb-4" style="background-color: #198754">
                            <i class="fa fa-floppy-disk me-1"></i> Sačuvaj
                        </button>
                    </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">

                        <div class="text-center mb-4">
                            @if(Auth::user()->role === 'seller')
                                <span class="badge bg-success px-3 py-2">Prodavac</span>
                            @elseif(Auth::user()->role === 'buyer')
                                <span class="badge px-3 py-2" style="background: #9c1c2c;">Kupac</span>
                            @elseif(Auth::user()->role === 'both')
                                <span class="badge bg-warning text-dark px-3 py-2">Prodavac & Kupac</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Nepoznat</span>
                            @endif

                        </div>

                        @if( Auth::user()->role === 'seller' or  Auth::user()->role === 'both')
                            @php
                                $sellerLevels = [
                                    0 => 'Novi prodavac',
                                    1 => 'Novi prodavac',
                                    2 => 'Level 1 prodavac',
                                    3 => 'Level 2 prodavac',
                                    4 => 'Top Rated prodavac',
                                ];

                                $sellerLevelName = $sellerLevels[Auth::user()->seller_level] ?? 'Nepoznat nivo';
                            @endphp

                            @if(Auth::user()->package)
                                <div class="package">
                                    <h6 class="text-secondary">
                                        @if(Auth::user()->package->duration  === 'yearly')
                                            <i class="fas fa-calendar-alt text-secondary"></i> Godišnji plan:
                                        @else
                                            <i class="fas fa-calendar-alt text-secondary"></i> Mesečni plan:
                                        @endif

                                    @if(Auth::user()->package->slug === 'start')
                                        <i class="fas fa-box text-primary"></i>
                                    @elseif(Auth::user()->package->slug === 'pro')
                                        <i class="fas fa-gift text-success"></i>
                                    @elseif(Auth::user()->package->slug === 'premium')
                                        <i class="fas fa-gem text-warning"></i>
                                    @endif
                                    <strong class="text-success">{{ Auth::user()->package->name }}</strong>
                                    </h6>
                                    <h6 class="text-secondary">
                                       <i class="fas fa-file-text"></i> Plan opis: <strong class="text-success">{{ Auth::user()->package->description }}</strong>
                                    </h6>
                                    <h6 class="text-secondary">
                                       <i class="fas fa-calendar-times"></i> Plan ističe: <strong class="text-success">{{ \Carbon\Carbon::parse(Auth::user()->package_expires_at)->format('d.m.Y H:i:s') }}</strong>
                                    </h6>
                                </div>
                                <hr>
                            @else
                                <div class="alert alert-danger text-center">
                                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemaš aktivan paket!
                                </div>

                                <div class="text-warning mb-2">
                                    <a href="{{ route('packages.index') }}" class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> <i class="fas fa-calendar-alt"></i>
                                    </a>
                                    <p class="text-center text-secondary">Odaberi paket</p>
                                </div>
                            @endif


                            <h6 class="text-secondary">
                                <i class="fas fa-user"></i> Nivo prodavca: <strong class="text-success">{{ $sellerLevelName }}</strong>
                            </h6>
                            <h6 class="text-secondary">
                                <i class="fas fa-credit-card"></i> Ukupna mesečna zarada: <strong class="text-success">{{ number_format($totalEarnings, 2) }} <i class="fas fa-euro-sign"></i></strong>
                            </h6>
                        @endif

                        <h6 class="text-secondary">
                            <i class="fas fa-credit-card"></i> Trenutni depozit: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>

                        @if(Auth::user()->role === 'seller' or Auth::user()->role === 'both')
                            <div class="text-warning mb-2">
                                <button type="button" class="btn btn-poslovi w-100 mb-2" data-bs-toggle="modal" data-bs-target="#fiatPayoutModal">
                                    <i class="fas fa-money-bill-wave me-1"></i> Povuci novac
                                </button>
                                <a href="{{ route('deposit.form') }}" class="btn btn-outline-warning ms-auto w-100" data-bs-toggle="tooltip" title="Deponuj novac"> <i class="fas fa-credit-card"></i>
                                </a>
                                <p class="text-center text-secondary">Deponuj novac</p>
                            </div>
                        @endif

                        <div class="text-warning mb-4 text-center">
                            @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= Auth::user()->stars ) <!-- Nasumična ocena između 3 i 5 -->
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <br>
                            <span class="text-secondary"> Tvoja ukupna ocena: {{ Auth::user()->stars }}</span>
                        </div>

                        <div class="text-warning mb-2 text-center">
                             <a class="btn btn-outline-success ms-auto w-100" title="Promeni lozinku" data-bs-toggle="modal" data-bs-target="#passwordModal"> <i class="fas fa-key"></i>
                            </a>
                            <p class="text-center text-secondary">Promeni lozinku</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Modal -->
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="w-100 text-center">
                            <h5 class="modal-title" id="passwordModalLabel"><i class="fas fa-key"></i> Promena lozinke</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="passwordForm" method="POST" action="{{ route('profile.changePassword') }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nova lozinka</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Potvrdite novu lozinku</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                            </div>
                            <!-- Div za prikaz greške -->
                            <div id="passwordError" class="alert alert-danger d-none text-center"></div>
                            <div class="w-100 text-center">
                                <button type="submit" class="btn text-white w-100 mb-4" style="background-color: #198754">
                                    <i class="fa fa-floppy-disk me-1"></i> Sačuvaj lozinku
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


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

         <!-- Payout Fiat Modal -->
        <div class="modal fade" id="fiatPayoutModal" tabindex="-1" aria-labelledby="fiatPayoutModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="fiatPayoutModalLabel">
                            <i class="fas fa-money-bill-wave"></i> Zahtev za isplatu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="payoutForm" method="POST" action="{{ route('affiliate.payout') }}">
                            @csrf

                            <div class="d-flex mb-3">
                                <h6 class="form-label">
                                    Dostupno za isplatu: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} €</strong>
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

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#profile-message') {
        const element = document.getElementById('profile-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('profile-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('profile-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }

    document.getElementById("passwordForm").addEventListener("submit", function(event) {
        let password = document.getElementById("new_password").value;
        let confirmPassword = document.getElementById("new_password_confirmation").value;
        let errorDiv = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            event.preventDefault(); // Sprečava slanje forme
            errorDiv.textContent = "Lozinke se ne poklapaju!"; // Postavlja poruku
            errorDiv.classList.remove("d-none"); // Prikazuje div

            // Sakriva poruku nakon 5 sekundi
            setTimeout(() => {
                errorDiv.classList.add("d-none");
            }, 5000);
        } else {
            errorDiv.classList.add("d-none"); // Sakriva div ako su lozinke iste
        }
    });
});
</script>
@endsection
