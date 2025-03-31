@extends('layouts.app')

<link href="{{ asset('css/index.css') }}" rel="stylesheet">

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
                                     alt="Avatar" class="rounded-circle" width="100" height="100">
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
                            <input type="email" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                        </div>

                        <!-- Telefon -->
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i> Telefon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{9,15}" placeholder="06X/XXX-XXX" value="{{ Auth::user()->phone }}" required>
                        </div>

                        <!-- Rola -->
                        <div class="form-group mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" id="prodavac" value="prodavac" {{ ('seller' === Auth::user()->role || 'both' === Auth::user()->role) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="prodavac">Prodavac</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" id="kupac" value="kupac" {{ ('buyer' === Auth::user()->role || 'both' === Auth::user()->role) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kupac">Kupac</label>
                                    </div>
                                </div>
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
                                <span class="badge bg-primary px-3 py-2">Kupac</span>
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
                                        <i class="fas fa-calendar-alt text-secondary"></i> Mesečni plan:


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
                                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemate aktivan paket!
                                </div>

                                <div class="text-warning mb-2">
                                    <a href="{{ route('packages.index') }}" class="btn btn-outline-primary ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> <i class="fas fa-calendar-alt"></i>
                                    </a>
                                    <p class="text-center text-secondary">Odaberite paket</p>
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

                        <div class="text-warning mb-2">
                            <a href="{{ route('deposit.form') }}" class="btn btn-outline-warning ms-auto w-100" data-bs-toggle="tooltip" title="Deponuj novac"> <i class="fas fa-credit-card"></i>
                            </a>
                            <p class="text-center text-secondary">Deponuj novac</p>
                        </div>

                        <div class="text-warning mb-4 text-center">
                            @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= Auth::user()->stars ) <!-- Nasumična ocena između 3 i 5 -->
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <br>
                            <span class="text-dark"> Vaša ukupna ocena: {{ Auth::user()->stars }}</span>
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
