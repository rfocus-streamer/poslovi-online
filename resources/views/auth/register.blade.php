@extends('layouts.app')
<title>Poslovi Online | Registracija</title>
<link href="{{ asset('css/index.css') }}" rel="stylesheet">
@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white">
        <span class="hero-subtitle">Kreirajte novi nalog</span>
        <h1 class="hero-title text-center">Poslovi<mark>online</mark></h1>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content col-md-8 mx-auto">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/preloader.png') }}"
                     class="rounded-circle"
                     alt="Poslovi Online"
                     width="50"
                     height="50">
            </div>
            <span class="hero-subtitle text-center text-dark fw-bold">Registracija novog korisnika</span>
            <div class="modal-body p-5">

                <!-- Prikaz poruke sa anchor ID -->
                @if(session('success'))
                    <div id="register-message" class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" onsubmit="return validatePasswords()">
                    @csrf

                    <!-- Dodajte ovo na početak forme -->
                    <input type="hidden" name="affiliateCode" value="{{ request('affiliateCode') }}">

                    <!-- Ime i Prezime -->
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname" class="form-label">
                                    <i class="fas fa-user me-1"> </i>Ime
                                </label>
                                <input type="text"
                                       id="firstname"
                                       name="firstname"
                                       class="form-control @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname') }}"
                                       required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname" class="form-label">
                                    <i class="fas fa-user-tag me-1"></i> Prezime
                                </label>
                                <input type="text"
                                       id="lastname"
                                       name="lastname"
                                       class="form-control @error('lastname') is-invalid @enderror"
                                    value="{{ old('lastname') }}"
                                       required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-1">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lozinka i Potvrda -->
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Lozinka
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Ponovite lozinku
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                    value="{{ old('password') }}"
                                       required>
                                <small id="passwordHelp" class="form-text text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Telefon -->
                    <div class="form-group mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-1"></i> Telefon
                        </label>
                        <input type="tel"
                               id="phone"
                               name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               pattern="[0-9]{9,15}"
                               placeholder="06X/XXX-XXX"
                               value="{{ old('phone') }}"
                               required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Rola -->
                    <div class="form-group mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="roles[]"
                                           id="prodavac"
                                           value="prodavac"
                                           {{ in_array('prodavac', old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prodavac">
                                        Prodavac
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="roles[]"
                                           id="kupac"
                                           value="kupac"
                                           {{ in_array('kupac', old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kupac">
                                        Kupac
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('roles')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        <i class="fas fa-user-plus me-1"></i>Registruj se
                    </button>

                    <!-- Već imate nalog? -->
                    <div class="text-center">
                        <p>Već imate nalog?
                            <a href="{{ route('login') }}" class="text-decoration-none">Prijavite se ovde</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    // Automatski prikaz modalnog prozora
    window.onload = function() {
        const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    };

    // Provera lozinki
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const passwordHelp = document.getElementById('passwordHelp');

        if(password !== confirmPassword) {
            passwordHelp.textContent = 'Lozinke se ne poklapaju!';
            return false;
        }
        return true;
    }

    // Provera u realnom vremenu
    document.getElementById('password_confirmation').addEventListener('input', function() {
        validatePasswords();
    });

    // Restrikcije za telefon
    document.getElementById('phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // Onemogući zatvaranje modala klikom izvan
    document.getElementById('registerModal').addEventListener('show.bs.modal', function(event) {
        document.querySelector('.modal-backdrop').style.backgroundColor = 'rgba(0,0,0,0.7)';
    });
</script>
